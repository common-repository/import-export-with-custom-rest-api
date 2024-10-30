<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>

<div class="blank_endpoint import-form">
    <div class="wotrapi_customheader">
        <h2><?php _e( 'Import Blog Posts', 'import-export-with-custom-rest-api' ) ?></h2>
    </div>

    <!-- check if session is there then print the value -->
    <?php 
        // if user have added the wrong endpoint or url then throw an error
        if ( array_key_exists( 'incorrect_input', $_SESSION ) ) { ?>
            <div class="notice notice-error is-dismissible wot_error_spacing"> 
                <p>
                    <?php echo esc_attr($_SESSION['incorrect_input']); ?>
                </p> 
            </div>
            <?php unset($_SESSION['incorrect_input']); 
        }

        if ( !empty( $_POST['import_api'] ) ) { ?>
			<div class="notice notice-info is-dismissible wot_error_spacing"> 
                <p>
                    <?php  _e( 'The process of post-insertion is started, please wait for a while.', 'import-export-with-custom-rest-api' ); ?>
                </p> 
            </div>
		    <?php 
        }
    ?>

    <form method='post' action =''>
        <div class="wotrapi_wrapper">
            <div class='wotrapi_userInputForm'>
                <div class="text-center">
                    <div class="wotrapi_import_api">
                        <input type="text" class="" name="import_api" placeholder="<?php _e( 'Enter your generated api url here', 'import-export-with-custom-rest-api' ); ?>"  >
                    </div>
                    <div>
                        <input class="button-primary" type='submit' name='wotrapi_import_submit' value='Import'>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>