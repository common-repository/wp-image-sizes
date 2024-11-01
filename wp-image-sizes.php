<?php 
/**
 * Plugin Name: WP Image Sizes
 * Plugin URI: https://aiwatech.com/wp-image-sizes
 * Description: Save server space by creating selected image sizes for every post type. It allows to select registered image sizes in media uploader and helps to avoid creation of unneccessary images.
 * Version: 1.1.3
 * Requires at least: 4.7
 * Tested up to: 5.7.2
 * Author: Aiwatech
 * Author URI: https://aiwatech.com
 * Text Domain: wpis
*/

defined( "ABSPATH" ) or die();

global $wpis,
		$wpis_image_sizes,
		$wpis_disabled_sizes,
		$wpis_settings,
		$wpis_plugin_data;

$wpis 				= get_option( "wpis" );
$wpis_image_sizes 	= isset($wpis["image_sizes"])
						? $wpis["image_sizes"]
						: [];

$wpis_disabled_sizes = isset($wpis["disabled_sizes"])
						? $wpis["disabled_sizes"]
						: [];

$wpis_settings 		= isset($wpis["settings"]) 
						? $wpis["settings"] 
						: [];

if( !is_array($wpis_image_sizes) ){
	$wpis_image_sizes = [];	
}
//=== Include plugin.php for plugin related functions 
if( !function_exists( "is_plugin_active" ) ) {
	require_once ABSPATH . "wp-admin/includes/plugin.php";
}

//--- Get Plugin Data
$wpis_plugin_data = get_plugin_data(__FILE__);

define( "WPIS_PLUGIN_VERSION", $wpis_plugin_data["Version"] );
define( "WPIS_PLUGIN_NAME", plugin_basename(__FILE__) );
define( "WPIS_PLUGIN_URL", plugin_dir_url( __FILE__ ) );
define( "WPIS_PLUGIN_PATH", plugin_dir_path(__FILE__) );

//=== Add activation option when plugin is activated, Used for redirect
register_activation_hook(__FILE__, "wpis_plugin_activated");
function wpis_plugin_activated() {
	add_option( "wpis_activated", true);
}

//==== Add Plugin Menu Page ===//
if(!function_exists("wpis_menu_pages")):
	add_action("admin_menu", "wpis_menu_pages", 80 );
	function wpis_menu_pages(){
		add_options_page(
			__("WP Image Sizes", "wpis"), 
			__("WP Image Sizes", "wpis"), 
			"manage_options",
			"wpis",
			"draw_wpis_page"
		);		
	}
	
	function draw_wpis_page(){
		include_once( WPIS_PLUGIN_PATH . "includes/wpis-settings.php" );	
	}
endif;

include_once( "wpis-init.php" );
