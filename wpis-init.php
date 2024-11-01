<?php 
defined( "ABSPATH" ) or die();

//=== Check if Current Page is WPIS Page ===//
if(!function_exists("is_wpis_page")):
	function is_wpis_page(){
		global $current_screen;
		$get = array_map("sanitize_text_field", $_GET);
		
		return (isset($current_screen->base) && $current_screen->base == "settings_page_wpis")
				|| ( isset($get["page"]) && $get["page"] == "wpis" );
	}
endif;

if(!function_exists("wpis_styles")):
//==== Add Plugin Styles ===//
add_action( "admin_enqueue_scripts", "wpis_styles" );
function wpis_styles(){
	wp_enqueue_style( "wpis-styles", WPIS_PLUGIN_URL . "assets/css/wpis-style.css", [], WPIS_PLUGIN_VERSION );
	wp_enqueue_script( "wpis-js", WPIS_PLUGIN_URL . "assets/js/wpis.js", ["jquery", "jquery-ui-dialog"], WPIS_PLUGIN_VERSION);
	
	printf("<script type=\"text/javascript\"> var wpis_ajaxed = false; </script>");
}
endif;


if(!function_exists("wpis_image_sizes_advanced")):
//==== Filter Image Sizes ===//
add_filter("intermediate_image_sizes_advanced", "wpis_image_sizes_advanced", 10, 3);
function wpis_image_sizes_advanced( $sizes, $metadata, $attachment_id ){
	global $wpis_image_sizes,
			$wpis_disabled_sizes;
	
	$wpis_session 	= get_option( "wpis_session" );

	$post_type = get_post_type( intval($_REQUEST["post_id"]) );
	
	if( !$post_type && get_current_screen() ){
		if( get_current_screen()->base != "attachment" ){
			$post_type = "attachment";
		}
	}
	
	if( !isset($wpis_session["wpis_disable_sizes"][$post_type]) ){
		$wpis_disable_sizes = in_array($post_type, $wpis_disabled_sizes);
	}
	else{
		$wpis_disable_sizes = $wpis_session["wpis_disable_sizes"][$post_type] == "true" 
								|| $wpis_session["wpis_disable_sizes"][$post_type] === true;
	}
	
	//=== Return early if image sizes were disabled
	if( $wpis_disable_sizes ){
		return [];	
	}
	
	$selected_image_sizes = isset($wpis_session["wpis_image_sizes"][$post_type]) 
							? array_map("sanitize_text_field",$wpis_session["wpis_image_sizes"][$post_type]) 
							: array_map("sanitize_text_field", (array)$wpis_image_sizes[$post_type]);
							
	if( $selected_image_sizes ){
		foreach( $sizes as $size_name => $size_meta ){
			if( !in_array($size_name, $selected_image_sizes) ){
				unset($sizes[$size_name]);
			}
		}
	}
	
	return $sizes;
}
endif;

if(!function_exists("save_wpis_image_sizes")):
add_action("wp_ajax_save_wpis_image_sizes", "save_wpis_image_sizes");
function save_wpis_image_sizes(){
	
	$post_type = sanitize_text_field( $_POST["post_type"] );
	$wpis_session = get_option( "wpis_session" );
	
	$res["success"] = 0;
	$res["sizes"] = [];
	
	$wpis_image_sizes = [];

	if( $_POST["wpis_image_sizes"] ){
		$wpis_image_sizes = array_map( "sanitize_text_field", $_POST["wpis_image_sizes"] );
		$res["success"]	= 1;
		$res["sizes"]	= $wpis_image_sizes;
	}
	
	$wpis_session["wpis_image_sizes"][$post_type] = $wpis_image_sizes;
	
	if( !isset($wpis_session["wpis_disable_sizes"]) ){
		$wpis_session["wpis_disable_sizes"] = [];	
	}
	
	if( isset($_POST["wpis_disable_sizes"]) ){
		$wpis_disable_sizes = sanitize_text_field($_POST["wpis_disable_sizes"]);
		$wpis_session["wpis_disable_sizes"][$post_type] = $wpis_disable_sizes == "true" || $wpis_disable_sizes === true;	
	}
	
	update_option( "wpis_session", $wpis_session );
	
	die(json_encode($res));
}
endif;

if(!function_exists("wpis_session_reset")):
//==== Reset WPIS Session ===//
add_action("wp_ajax_wpis_session_reset", "wpis_session_reset");
function wpis_session_reset(){
	$res["success"] = 0;
	$res["message"] = __("Session has been reset", "wpis");
	
	update_option( "wpis_session", [] );
	
	die(json_encode($res));
}
endif;

if(!function_exists("wpis_media_uploader_ui")):
//==== Show Image Sizes on Media Uploader ===//
add_action( "post-upload-ui", "wpis_media_uploader_ui" ); 
function wpis_media_uploader_ui(){
	$post_type = get_post_type( intval($_REQUEST["post"]) );
	
	if( !$post_type && get_current_screen() ){
		if( get_current_screen()->base != "attachment" ){
			$post_type = "attachment";
		}
	}
	?>
	<div class="wpis-media-uploader"><span class="spinner is-active"></span></div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$(".upload-ui > .button.browser").attr("disabled", "disabled");

			//=== Save Selected Sizes in WPIS Session
			$.ajax({
				url: "<?=admin_url("admin-ajax.php")?>",
				type: "POST", 
				dataType: "json",
				data: {
					action: "wpis_load_uploader_sizes",
					post_type:"<?=$post_type?>",
				},
				success: function( res ){
					if( res.html ){
						$(".upload-ui > .button.browser").removeAttr("disabled");
						$(".wpis-media-uploader").html(res.html);
					}
				}
			});
			
			//=== Reset WPIS Session on Media Uploader Close
			if( wp.media ){
				
				wp.media.frame.on("close", function(e){
					
					if( !wpis_ajaxed ){
						wpis_ajaxed = true;
						
						$.ajax({
							url: "<?=admin_url("admin-ajax.php")?>",
							type: "POST", 
							data: {
								action: "wpis_session_reset",
							},
							success: function(res){
								wpis_ajaxed = false;
							}
						});
					}
				});
				
			}
		});
    </script>
<?php
}
endif;

//=== Load Image Sizes Uploader UI ===//
if( !function_exists("wpis_load_uploader_sizes")):
add_action("wp_ajax_wpis_load_uploader_sizes", "wpis_load_uploader_sizes");
function wpis_load_uploader_sizes(){
	global $wpis_image_sizes,
			$wpis_disabled_sizes;
	
	$wpis_session = get_option( "wpis_session" );
	
	$intermediate_image_sizes = get_intermediate_image_sizes();
	$wp_additional_image_sizes = wp_get_additional_image_sizes();
		
	$post_type = sanitize_text_field( $_REQUEST["post_type"] );

	if( !$post_type && get_current_screen() ){
		if( get_current_screen()->base != "attachment" ){
			$post_type = "attachment";
		}
	}
	
	if( !isset($wpis_session["wpis_disable_sizes"][$post_type]) ){
		$wpis_disable_sizes = in_array($post_type, $wpis_disabled_sizes);
	}
	else{
		$wpis_disable_sizes = $wpis_session["wpis_disable_sizes"][$post_type] == "true" || $wpis_session["wpis_disable_sizes"][$post_type] === true;
	}
	ob_start();
	?>
    
    <div class="wpis-image-sizes">
	    <h3><?php _e("Select image sizes", "wpis");?></h3>
        <p class="description text-red"><?php _e("If no image size is selected, images of all sizes will be created.")?></p>
        <p class="description textleft">
        <?php 
		echo( wp_sprintf('<label class="disable-size-checkbox fw-6"><input name="wpis_disable_sizes" class="wpis-checkbox" type="checkbox" %s>%s</label>', 
					$wpis_disable_sizes ? 'checked="checked"' : '',
					__("Disable thumbnail creation <small>(This will not create any additional thumbnail.)</small>", "wpis")
				)
			);
		?>
        </p>
        <div class="wpis-image-size-boxes">
		<?php
		$wpis_image_sizes[$post_type] = isset($wpis_session["wpis_image_sizes"][$post_type]) 
										? array_map("sanitize_text_field",$wpis_session["wpis_image_sizes"][$post_type]) 
										: array_map("sanitize_text_field", (array)$wpis_image_sizes[$post_type]);
		
		foreach( $intermediate_image_sizes as $image_size ){
			
			if( isset( $wp_additional_image_sizes[$image_size]["width"] ) ){
				$width = $wp_additional_image_sizes[$image_size]["width"];	
			}else{
				$width = get_option( "{$image_size}_size_w" );
			}
			
			if( isset( $wp_additional_image_sizes[$image_size]["height"] ) ){
				$height = $wp_additional_image_sizes[$image_size]["height"];	
			}else{
				$height = get_option( "{$image_size}_size_h" );
			}
			
			echo( wp_sprintf('<label class="image-size-checkbox"><input name="wpis_image_sizes[%s][]" class="wpis-checkbox" type="checkbox" value="%s" %s %s>%s (%s x %s)</label>', 
					$image_size,
					$image_size,
					in_array($image_size, $wpis_image_sizes[$post_type]) ? 'checked="checked"' : '',
					$wpis_disable_sizes ? 'disabled="disabled"' : '',
					ucwords($image_size), 
					$width,
					$height
				)
			);
		}
		
		?>
        </div>
    </div>
    <script type="text/javascript">
    	
		jQuery(document).ready(function($) {
			
			$(".image-size-checkbox > input, .disable-size-checkbox > input").change(function(e){
				e.stopImmediatePropagation();
				assign_image_sizes();
			});
			
		});
		
		function assign_image_sizes(){
			var $ = jQuery;
			
			wpis_image_sizes 	= [];
			wpis_disable_sizes 	= $(".disable-size-checkbox > .wpis-checkbox").is(":checked"); 
			
			if( wpis_disable_sizes ){
				$(".image-size-checkbox > .wpis-checkbox").attr("disabled", "disabled");
			}else{
				$(".image-size-checkbox > .wpis-checkbox").removeAttr("disabled");
			}
			
			$(".image-size-checkbox").each(function(){
				var checkbox = $(this).find("input:checkbox");
				if( checkbox.is(":checked") ){
					checkbox.attr("checked", "checked");
					if( !wpis_image_sizes.includes(checkbox.val()) ){
						wpis_image_sizes.push( checkbox.val() );	
					}
				}else{
					checkbox.removeAttr("checked");
				}
			});

			$.ajax({
				url: "<?=admin_url("admin-ajax.php")?>",
				type: "POST", 
				data: {
					action: "save_wpis_image_sizes",
					post_type:"<?=$post_type?>",
					wpis_image_sizes: wpis_image_sizes,
					wpis_disable_sizes: wpis_disable_sizes,
				}
			});
		}
	</script>
    <?php 
	$res["html"] = ob_get_clean();
	
	die( json_encode($res));
}
endif;

//=== Hooked update wpis settings 
if( !function_exists("wpis_update_option") ):
add_filter( "pre_update_option", "wpis_update_option", 10, 3 );
function wpis_update_option( $value, $option, $old_value ){
	
	if( $option == "wpis" ){
		update_option( "wpis_session", [] );
	}
	
	return $value;
}
endif;

//=== Redirect to plugins settings page after activation
if( !function_exists("wpis_plugin_redirect") ):
add_action("admin_init", "wpis_plugin_redirect");
function wpis_plugin_redirect() {
	global $wpis;
	
	//--- Plugin activation option is set and image sizes are not set in settings then redirect
	if ( get_option( "wpis_activated") && empty($wpis["image_sizes"]) ) {
		
		delete_option( "wpis_activated" );
		
		wp_redirect( add_query_arg( ["page" => "wpis"], admin_url("options-general.php") ) );
		exit;		
    }
	
	if( ! wp_doing_ajax() && !isset($_REQUEST["short"]) ){
		update_option( "wpis_session", [] );	
	}
}
endif;

if( !function_exists("wpis_admin_notices") ):
add_action( "admin_notices", "wpis_admin_notices" );
function wpis_admin_notices() {
	global $wpis_image_sizes;
	global $current_screen;
	
	if( $current_screen->base != "settings_page_wpis" && ( !is_array($wpis_image_sizes) || empty($wpis_image_sizes) ) ){?>
		<div class="notice notice-warning is-dismissible">
        	<p>
            	<?php _e("You have not assigned image sizes to post types, Please assign image sizes in", "wpis");?>
                <a href="<?=add_query_arg( ["page" => "wpis"], admin_url("options-general.php") );?>"><?php _e("WPIS settings", "wpis");?></a>
            </p>
        </div>
    <?php
    }
}
endif;