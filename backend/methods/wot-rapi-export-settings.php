<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// define the global variable to use the queries
global $wpdb, $blog_api_options;
$wot_table = WOT_RAPI_SETTINGS_TABLE;

//define default api slug
if(!isset($_POST['api_slug'])){
    $_POST['api_slug'] = __( 'posts', 'import-export-with-custom-rest-api' );
}elseif(isset($_POST['api_slug']) && empty($_POST['api_slug'])){
    $_POST['api_slug'] = __( 'posts', 'import-export-with-custom-rest-api' ); 
}
$count = 1; 

// If form is perfectly submitted then add fields to the db
if(isset($_POST['wotrapi_blogdata_submit'])){
    
    $_POST['field']['post_title'] = 1;
    $_POST['field']['post_content'] = 1;
    $_POST['field']['author'] = 1;
    $_POST['field']['author_email'] = 1;
    $_POST['field']['author_role'] = 1;
    foreach ($blog_api_options as $blog_api_option) {
        if(!isset($_POST['field'])){
            $_POST['field'] = array();
        }

        if (in_array($blog_api_option[1], array_keys($_POST['field']))){
            $user_selected_status = 1;
        }else{
            $user_selected_status = 0;
        }

        $user_selected_data[$blog_api_option['1']] = $user_selected_status;
    }

    /*
    *Check the api slug in database and if we found it in db then update it else insert it  
    */
    $entriesList_param = ( "SELECT * FROM $wot_table ORDER BY id DESC" );
    $entriesList = $wpdb->get_results( $entriesList_param , OBJECT  );

    if(count($entriesList) > 0){
        $count = 1;
        $existing_entires = array();

        foreach($entriesList as $entry){
            array_push($existing_entires, $entry->api_slug);
            $count++;
        }
    }

    $specific_api_slug = sanitize_title_with_dashes($_POST['api_slug']);
    $final_url = sanitize_text_field(get_bloginfo('url') . esc_html__( '/wp-json/wp/v1/', 'import-export-with-custom-rest-api' ) . $specific_api_slug);
    if(isset($existing_entires)){
        if(in_array($_POST['api_slug'], $existing_entires)){
            $wpdb->update(WOT_RAPI_SETTINGS_TABLE, array(
                    'selected_data' => (string)json_encode($user_selected_data),
                ), 
                array('api_slug' => $specific_api_slug )
            );
        }else{
            $wpdb->insert(WOT_RAPI_SETTINGS_TABLE, array(
                'api_slug' => $specific_api_slug,
                'selected_data' => (string)json_encode($user_selected_data),
                'api_type' => 'Blog',
                'final_api_url' => $final_url, // ... and so on
            ));
        }
    }else{
        $wpdb->insert(WOT_RAPI_SETTINGS_TABLE, array(
            'api_slug' => $specific_api_slug,
            'selected_data' => (string)json_encode($user_selected_data),
            'api_type' => 'Blog',
            'final_api_url' => $final_url, // ... and so on
        ));
    }
    ?>
        <script>
            window.setTimeout(function() {
                window.location.href = '<?php esc_html_e( (get_bloginfo('url') . "/wp-admin/admin.php?page=show-all-api"), 'import-export-with-custom-rest-api' ); ?>';
            }, 2000);
        </script>
    <?php
}
?>

<!-- Below is the form to get user data to create api it will call the above code -->
<div class="blank_endpoint">
    <div class="wotrapi_customheader">
        <h2><?php _e( 'Select the fields you want in you blog API', 'import-export-with-custom-rest-api' ); ?></h2>
    </div>

    <?php
        if(isset($_POST['wotrapi_blogdata_submit'])){
            if(isset($existing_entires)){
                if(in_array($_POST['api_slug'], $existing_entires)){  
                    ?>
                        <div class="notice notice-info  is-dismissible wot_success_spacing"><p>
                        <?php  _e( 'Options are updated.', 'import-export-with-custom-rest-api' ); ?> 
                        </p> </div>
                    <?php 
                }else{
                    ?>
                        <div class="notice notice-success is-dismissible wot_success_spacing"> <p>
                        <?php  _e( 'Options are saved.', 'import-export-with-custom-rest-api' ); ?> 
                        </p> </div>
                    <?php
                }
            }else{
                ?>
                    <div class="notice notice-success is-dismissible wot_error_spacing"> <p>
                    <?php  _e( 'Options are saved.', 'import-export-with-custom-rest-api' ); ?> 
                    </p> </div>
                <?php
            }
        }
    ?>

    <form method='post' action =''>
        <div class="wotrapi_wrapper">
            <div class='wotrapi_userInputForm'>
                <ul class="wotrapi_userInputs">
                    <?php 
                        foreach($blog_api_options as $blog_api_option){
                    ?>
                            <li class="wotrapi_userSingleInput">
                                <div class="wotrapi_element_name">
                                    <?php echo esc_attr($blog_api_option[0]); ?>
                                </div>
                                <div class="wotrapi_element_user_status toggle-box-switch-board pointer">
                                    <div class="toggle-box-switch-circle"></div>
                                    <?php  if($blog_api_option[1] == 'author' || $blog_api_option[1] == 'author_email' || $blog_api_option[1] == 'author_role' || $blog_api_option[1] == 'post_title' || $blog_api_option[1] == 'post_content'){ ?>
                                        <input class="wotrapi_toggle minimum_input" type='checkbox' name='field[<?php echo esc_attr($blog_api_option[1]); ?>]' checked>
                                    <?php }else{ ?>
                                        <input class="wotrapi_toggle" type='checkbox' name='field[<?php echo esc_attr($blog_api_option[1]); ?>]'>
                                    <?php } ?>
                                </div>
                            </li>
                    <?php
                            $count++;
                        }
                    ?>
                </ul>

                <div class="text-center">
                    <div class="wotrapi_edit_endpoints">
                        <a class="wotrapi_editSlug" href="<?php esc_html_e( (get_bloginfo('url') . "/wp-json/wp/v1/"), 'import-export-with-custom-rest-api' ); ?><?php echo esc_attr($_POST['api_slug']); ?>"><?php esc_html_e( (get_bloginfo('url') . "/wp-json/wp/v1/"), 'import-export-with-custom-rest-api' ); ?><?php echo esc_attr($_POST['api_slug']); ?></a>
                        <button type="button" class="wotrapi_btn_edit_endpoint button-secondary"><?php _e( 'Edit Endpoint', 'import-export-with-custom-rest-api' ) ?></button>
                    </div>
                
                    <div class="wotrapi_save_endpoints d-none">
                        <span><?php esc_html_e( (get_bloginfo('url') . "/wp-json/wp/v1/"), 'import-export-with-custom-rest-api' ); ?></span>
                        <input type="text" class="wotrapi_default_endpoint" name="api_slug" maxlength="10" onkeypress="return /^[a-zA-Z0-9._-]+$/i.test(event.key)" value="<?php echo esc_attr($_POST['api_slug']); ?>">
                        <button type="button" class="wotrapi_btn_save_endpoint button-secondary"><?php _e( 'change Endpoint', 'import-export-with-custom-rest-api' ) ?></button>
                    </div>

                    <div>
                        <input class="button-primary wotrapi_blogdata_submit mt25" type='submit' name='wotrapi_blogdata_submit' value='<?php _e( 'Submit', 'import-export-with-custom-rest-api' ) ?>'>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>