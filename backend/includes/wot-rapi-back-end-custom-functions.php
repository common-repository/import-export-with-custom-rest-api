<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add database on the plugin installation
 */
function wot_rapi_create_tables(){
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $create_table = "CREATE TABLE ".WOT_RAPI_SETTINGS_TABLE." (
        id INT NOT NULL AUTO_INCREMENT, 
        api_slug VARCHAR(255) NOT NULL, 
        selected_data longtext NOT NULL, 
        api_type VARCHAR(255) NOT NULL, 
        final_api_url VARCHAR(255) NOT NULL, 
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_table );
}
/*
 * global field array
 */
$blog_api_options = array(
                array("ID","id"),
                array("Post title","post_title"),
                array("Post Content","post_content"),
                array("Post Excerpts","post_excerpts"),
                array("Post Type","type"),
                array("Post Status","post_status"),
                array("Slug","slug"),
                array("Date","date"),
                array("Categorie IDs","categories"),
                array("Categorie names","categories_name"),
                array("Tag IDs","tags"),
                array("Tag Names","tag_names"),
                array("Author","author"),
                array("Author First Name","author_fname"),
                array("Author Last Name","author_lname"),
                array("Author Email Address","author_email"),
                array("Author role","author_role"),
                array("Author avatar","author_image"),
                array("Author Custom Meta","author_custom_meta"),
                array("Feature Image","feature_image"),
                array("Feature Image ALT Text","feature_image_alt_txt"),
                array("Next Post URL","next_post_url"),
                array("Next Post Title","next_post_title"),
                array("Previous Post URL","previous_post_url"),
                array("Previous Post Title","previous_post_title"),
                array("Social Media Sharing","social_media_sharing"),
                array("Post Custom Meta","post_custom_meta"),
            );

/**
 * Drop the plugin database while deleting the plugin
 */
function wot_rapi_drop_tables(){
    global $wpdb;
    $wpdb->query( 'DROP TABLE IF EXISTS '.WOT_RAPI_SETTINGS_TABLE.' ' );
}

/**
 * export page settings
 * this will create options for user to select the elements in api
 */
function wot_rapi_export_settings(){ 
    include WOT_RAPI_ADMIN_DIR . '/methods/wot-rapi-export-settings.php';
}

/**
 * on the submitting of user form it will create the endpoints 
 */
include WOT_RAPI_ADMIN_DIR . '/methods/wot-rapi-create-endpoints.php';

// import page settings
function wot_rapi_import_settings(){
    include WOT_RAPI_ADMIN_DIR . '/methods/wot-rapi-import-settings.php';
}

// Display all the apis created till now
function wot_rapi_display_apis(){
    include WOT_RAPI_ADMIN_DIR . '/methods/wot-rapi-display-api-list.php';
}


/**
 * on the submitting of import url, data will start to import
 */
include WOT_RAPI_ADMIN_DIR . '/methods/wot-rapi-import-functions.php';

/**
 * Add Custom admin menu
 */
function wot_rapi_add_custom_admin_menu(){
    add_menu_page(  esc_html__( 'Setup your rest API settings', 'import-export-with-custom-rest-api' ), esc_html__( 'Rest API Settings', 'import-export-with-custom-rest-api' ), 'manage_options', 'rapi-settings', 'wot_rapi_export_settings');
    add_submenu_page( 'rapi-settings',esc_html__( 'Export settings', 'import-export-with-custom-rest-api' ), esc_html__( 'Export settings', 'import-export-with-custom-rest-api' ),"manage_options", "rapi-settings");
    add_submenu_page( 'rapi-settings',esc_html__( 'Import settings', 'import-export-with-custom-rest-api' ), esc_html__( 'Import settings', 'import-export-with-custom-rest-api' ),"manage_options", "import-settings", "wot_rapi_import_settings");
    add_submenu_page( 'rapi-settings',esc_html__( 'Check API', 'import-export-with-custom-rest-api' ), esc_html__( 'Check API', 'import-export-with-custom-rest-api' ),"manage_options", "show-all-api", "wot_rapi_display_apis");
}

//Action to call the admin menu function
add_action( 'admin_menu', 'wot_rapi_add_custom_admin_menu' );


//function to check the session
function wot_rapi_register_my_session(){
    if( ! session_id() ) {
        session_start();
    }
}

add_action('init', 'wot_rapi_register_my_session');
?>