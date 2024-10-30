<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;
$wot_table = WOT_RAPI_SETTINGS_TABLE;
/*
* Delete record 
*/ 
if(isset($_GET['delid'])){
    $delid = intval($_GET['delid']);
    $wpdb->delete( $wot_table, array( 'ID' => $delid ), array( '%d' ) );
}

    /*
    * Select records to display 
    */ 
    $entriesList_param = ( "SELECT * FROM $wot_table ORDER BY id DESC" );
    $entriesList = $wpdb->get_results( $entriesList_param , OBJECT  );

    if(count($entriesList) > 0){
        $count = 1; ?>

        <div class='wotrapi_customheader'>
            <h2> <?php _e( 'List of Endpoints created till now', 'import-export-with-custom-rest-api' ); ?> </h2>
        </div>

        <?php 
            if(intval(isset($_GET['delid']))){
                ?>
                    <div class="notice notice-error is-dismissible wot_error_spacing">
                        <p> <?php  _e( 'The api have been deleted successfully', 'import-export-with-custom-rest-api' ); ?> </p>
                    </div>
                <?php
            }
        ?>

        <div class="wotrapi_wrapper">
            <div class="wot_table_responsive">
                <table width='100%' border='1' style='border-collapse: collapse;'>
                    <tr>
                        <th><?php _e( 'API Type', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Final API Url', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Selected Data', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Delete This?', 'import-export-with-custom-rest-api' ); ?></th>
                    </tr>
                    <?php
                        foreach($entriesList as $entry){
                            $id = $entry->id;
                            $api_slug = $entry->api_slug;
                            $selected_data = $entry->selected_data;
                            $selected_data_arrray = json_decode($selected_data, true);
                            $api_type = $entry->api_type;
                            $final_api_url = $entry->final_api_url;
                        ?>
                    <tr>
                        <td><?php echo esc_attr($api_type); ?></td>
                        <td><a href=<?php echo esc_attr($final_api_url); ?> target='_blank'><?php echo esc_attr($final_api_url); ?></a></td>
                        <td>
                            <?php
                            $isEmpty = 0; 
                                foreach ($selected_data_arrray as $key => $value) {
                                    if($value == '1'){
                                        $isEmpty = 1;
                                        ?>
                                            <p><?php echo esc_attr($key); ?></p>
                                        <?php
                                    }
                                }
                                if($isEmpty==0){
                                    ?><p> <?php  _e( 'You have selected nothing!', 'import-export-with-custom-rest-api' ); ?> </p> <?php
                                }
                            ?>
                        </td>
                        <td><a href='?page=show-all-api&delid=<?php echo esc_attr($id); ?>'><span class='dashicons dashicons-table-col-delete'></span></a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <?php $count++;
    }else{ ?>
        <div class="wotrapi_wrapper">
            <div class="wot_table_responsive">
                <table width='100%' border='1' style='border-collapse: collapse;'>
                    <tr>
                        <th><?php _e( 'API Type', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Final API Url', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Selected Data', 'import-export-with-custom-rest-api' ); ?></th>
                        <th><?php _e( 'Delete This?', 'import-export-with-custom-rest-api' ); ?></th>
                    </tr>
                    <tr><td colspan='4'>
                        <?php  _e( 'No record found!', 'import-export-with-custom-rest-api' ); ?>
                    </td></tr>
                </table>
            </div>
        </div>
    <?php } ?>
