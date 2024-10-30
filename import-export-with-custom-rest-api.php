<?php
/*
Plugin Name:    REST API | Custom API Generator For Cross Platform And Import Export In WP
Plugin URI:     https://wordpress.org/plugins/import-export-with-custom-rest-api/
Description:    Custom API generator for cross platform and import export in WordPress
Version:        2.0.4
Author:         WebOccult Technologies Pvt Ltd
Author URI:     https://www.weboccult.com
Text Domain:    import-export-with-custom-rest-api
Domain Path:    /languages
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'WOT_RAPI_DIR' ) ) {
    define('WOT_RAPI_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WOT_RAPI_URL' ) ) {
    define('WOT_RAPI_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
//get more knowledge on this
if( !defined('WOT_RAPI_BASENAME') ){
    define('WOT_RAPI_BASENAME', 'wot-rapi');  // plugin base name
}
if( !defined( 'WOT_RAPI_ADMIN_DIR' ) ) {
    define('WOT_RAPI_ADMIN_DIR', WOT_RAPI_DIR . '/backend' ); // plugin admin dir
}
if( !defined( 'WOT_RAPI_ADMIN_URL' ) ) {
    define('WOT_RAPI_ADMIN_URL', WOT_RAPI_DIR . 'backend' ); // plugin admin url
}
if( !defined( 'WOT_RAPI_SETTINGS_TABLE' ) ) {
    define( 'WOT_RAPI_SETTINGS_TABLE', 'wot_rapi_settings' ); // define the table name - to store seleted options details
}
//include custom function file for backend
include WOT_RAPI_ADMIN_DIR . '/includes/wot-rapi-back-end-custom-functions.php';

function wot_rapi_load_textdomain() {

  load_plugin_textdomain( 'import-export-with-custom-rest-api', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

}
add_action( 'init', 'wot_rapi_load_textdomain' );

//get more knowledge on this
function load_scripts() {
    wp_enqueue_style('wotrapi_custom_css', WOT_RAPI_URL . 'backend/assets/public-style.css');
    wp_enqueue_script('wotrapi_custom_js', WOT_RAPI_URL . 'backend/assets/public-script.js');
}
add_action( 'admin_init','load_scripts');

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 */
register_activation_hook( __FILE__, 'wot_rapi_install' );

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, 'wot_rapi_deactivate' );

/**
 * Uninstall Hook
 *
 * Register plugin deactivation hook.
 */
register_uninstall_hook ( __FILE__, 'wot_rapi_uninstall' );

/**
 * Plugin Setup (On Activation)
 *
 * Does the initial setup,
 * stest default values for the plugin options.
 */
function wot_rapi_install() {
    
    //create custom table for plugin
    wot_rapi_create_tables();

    //IMP Call of Function
    //Need to call when custom post type is being used in plugin
    flush_rewrite_rules();
}

/**
 * Plugin Setup (On Deactivation)
 *
 * Delete plugin options.
 */
function wot_rapi_deactivate() {

}
/**
 * Plugin Setup (On Uninstall)
 *
 * Delete plugin options.
 */
function wot_rapi_uninstall() {
    //drop custom table for plugin
    wot_rapi_drop_tables();
}

?>