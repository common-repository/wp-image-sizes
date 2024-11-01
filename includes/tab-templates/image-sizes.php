<?php 
defined( "ABSPATH" ) or die();

global $wpis_settings,
		$wpis_image_sizes,
		$wpis_disabled_sizes;
		
$core_cpts = get_post_types(["public" => true, "_builtin" => true],"objects");
$custom_cpts = get_post_types(["_builtin" => false],"objects");

$cpts = array_merge($core_cpts, $custom_cpts);

$excludes = ["wpcf7_contact_form"];

$wp_additional_image_sizes = wp_get_additional_image_sizes();
$intermediate_image_sizes = get_intermediate_image_sizes();?>
<div class="wpis-grid-columns-75-25">
	<div>
    	<div class="section-header">
            <p class="description text-red"><?php _e("Select the image sizes for all post types, Only selected image sizes will be generated", "wpis");?></p>
        </div>

        <div class="wpis-grid-columns-3">
            <?php 
            if( !empty( $cpts ) ){
                foreach( $cpts as $cpt ){
                    if( in_array($cpt->name, $excludes)) continue;
                ?>
                <fieldset class="wpis-fieldset">
                    <legend><?php _e(ucwords($cpt->labels->singular_name), "wpis");?></legend>
                    
                        <?php 
                        if( !empty( $intermediate_image_sizes ) ){
                            echo( wp_sprintf('<label class="disable-size-checkbox fw-6"><input class="wpis-checkbox" name="wpis[disabled_sizes][]" type="checkbox" value="%s" %s>%s</label><br />', 
									$cpt->name,
									in_array($cpt->name, (array)$wpis_disabled_sizes) ? 'checked="checked"' : '',
									__("Disable thumbnail creation", "wpis"))
							);
							
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
            
                                echo( wp_sprintf('<label class="image-size-checkbox"><input class="wpis-checkbox" name="wpis[image_sizes][%s][]" type="checkbox" value="%s" %s %s>%s (%s x %s)</label><br />', 
										$cpt->name,
										$image_size,
										in_array($image_size, (array)$wpis_image_sizes[$cpt->name]) ? 'checked="checked"' : '',
										in_array($cpt->name, (array)$wpis_disabled_sizes) ? 'disabled="disabled"' : '',
										ucwords($image_size),
										$width,
										$height)
								);
                            }		
                        }?>
                    
                </fieldset>
                <?php 
                }
            } ?>
        </div>
    </div>
    <div>
    	<div class="pro-features-section">
			<h3><?php _e("WPIS Pro Features", "wpis");?></h3>
			<div class="wpis-features-card">
				<h4><?php _e("Bulk Create Image Sizes", "wpis");?></h4>
				<div class="card-description">
					<?php _e("Allows you to select post types and available image size to generate only those image sizes for selected post type", "wpis");?>
				</div>
			</div>
			<div class="wpis-features-card">
				<h4><?php _e("Bulk Delete Image Sizes", "wpis");?></h4>
				<div class="card-description">
					<?php _e("Allows you to select post types and available image size to delete only those image sizes for selected post type", "wpis");?>
				</div>
			</div>
			<div class="wpis-features-card">
				<h4><?php _e("Create Images on Runtime", "wpis");?></h4>
				<div class="card-description">
					<?php _e("When a page is loaded with specific image size that doesn't exist, This enabled will allow you to generate those image size on runtime", "wpis");?>
				</div>
			</div>
			<a href="https://aiwatech.com/product/wp-image-sizes-pro/" target="_blank" class="button button-primary buy-pro"><?php _e("Buy Pro", "wpis");?></a>
		</div>
    </div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".disable-size-checkbox > .wpis-checkbox").change(function(){

			if($(this).is(":checked")){
				$(this).closest(".wpis-fieldset").find(".image-size-checkbox > .wpis-checkbox").attr("disabled", "disabled");
			}else{
				$(this).closest(".wpis-fieldset").find(".image-size-checkbox > .wpis-checkbox").removeAttr("disabled");
			}
		});
	});
</script>
