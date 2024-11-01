<?php 
defined( "ABSPATH" ) or die();

global $wpis,
		$wpis_image_sizes,
		$wpis_settings;
		
$wpis = get_option( "wpis" );
$wpis_image_sizes= $wpis["image_sizes"];
$wpis_settings 	= $wpis["settings"];

$tabs = [];
$tabs["image-sizes"]= __("Image Sizes", "wpis");

$settings_url = add_query_arg( ["page" => "wpis"], admin_url("options-general.php") );
?>
<form method="post" action="options.php" class="form form-horizontal">
    <?php wp_nonce_field("update-options") ?>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="wpis" />
    
    <div class="wrap wpis-wrap">
    	<h1><?php _e("WP Image Sizes Settings", "wpis");?></h1>
       	<nav class="nav-tab-wrapper wpis-tab-wrapper">
        	<?php 
			$active_tab = isset($_GET["tab"]) && isset( $tabs[$_GET["tab"]] ) ? sanitize_text_field( $_GET["tab"] ) : "image-sizes";
			foreach( $tabs as $id => $title ){
				printf('<a href="%s" class="nav-tab %s">%s</a>',
						add_query_arg(["tab" => $id], $settings_url),
						$id == $active_tab ? "nav-tab-active": "",
						$title
						);
			}
			?>
        </nav>
        <div class="tab-content wpis-tab-content">
        	<?php foreach( $tabs as $id => $title ){?>
	            <div class="tab-pane wpis-<?=$id?>-tab <?=$id == $active_tab ? "active" : "";?>" id="<?=$id?>">
					<?php include("tab-templates/$id.php");?>
                </div>
            <?php }?>
        </div>
        
    </div>
    <?php submit_button(__("Save Settings", "wpis"), "primary", "submit", true);?>
</form>