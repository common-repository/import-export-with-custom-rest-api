<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function wot_rapi_pippin_get_image_id($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
    if(!empty($attachment)) {
        return $attachment[0];
    }
    else {
        return '';
    }
}

// function to add the images from third party url
function wot_rapi_replace_internal_images($shtml){

    try{
        $doc = new DOMDocument();
        $doc->loadHTML($shtml);
        $tags = $doc->getElementsByTagName('img');

        foreach ($tags as $tag) {
            $oldSrc = $tag->getAttribute('src');
            $newScrURL = $oldSrc;

            $upload_dir = wp_upload_dir();
            if($oldSrc){
                $image_data = file_get_contents($oldSrc);
            }

            $filename = basename( $oldSrc );
            if ( wp_mkdir_p( $upload_dir['path'] ) ) {
                $file = $upload_dir['path'] . '/' . $filename;
            }
            else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            if( !file_exists($file) ){
                $IMGFileName = basename($oldSrc);
                $upload = wp_upload_bits($IMGFileName, null, file_get_contents($oldSrc, FILE_USE_INCLUDE_PATH));
                $imageFile = $upload['file'];
                $wp_filetype = wp_check_filetype($imageFile, null);
                $attachment = array(
                    'guid'=> $file,
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' =>  sanitize_file_name( $IMGFileName ),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $imageFile);
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                if(!empty($attach_id)) {
                    $newScrURL = wp_get_attachment_url($attach_id);
                }
            }
            else {
                $image_url = $upload_dir['url'].'/'.$filename;
                $image_id = wot_rapi_pippin_get_image_id($image_url);
                $newScrURL = $image_url;
            }

            $tag->setAttribute('src', $newScrURL);
            $tag->setAttribute('srcset', $newScrURL);
        }

        $htmlString = $doc->saveHTML();
        return $htmlString;
    }
    catch(Exception $e) {
        echo $e->getMessage();
        return $shtml;
    }

}

require_once( WOT_RAPI_ADMIN_DIR.'/wp-background-processing-master/class-logger.php' );
if ( ! class_exists( 'WP_Async_Request' ) ) {
    require_once( WOT_RAPI_ADMIN_DIR.'/wp-background-processing-master/classes/wp-async-request.php' );
}
if ( ! class_exists( 'WP_Background_Process' ) ) {
    require_once( WOT_RAPI_ADMIN_DIR.'/wp-background-processing-master/classes/wp-background-process.php' );
}

class IMPORT_BACKGROUND_PROCESS {

	/**
	 * @var WP_Example_Request
	 */
	protected $process_single;

	/**
	 * @var WP_Example_Process
	 */
	protected $process_all;

	/**
	 * IMPORT_BACKGROUND_PROCESS constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );		
		add_action( 'init', array( $this, 'process_handler' ) );
	}

	/**
	 * Init
	 */
	public function init() {
//            $this->process_single = new WOT_IMPORT_REQUEST();
            $this->process_all    = new WOT_IMPORT_Process();
            
	}
	

	/**
	 * Process handler
	 */
	public function process_handler() {
            
		if ( !empty( $_POST['import_api'] ) ) {
			$this->handle_all($_POST['import_api']);
		}
	}

	/**
	 * Handle all
	 */
	protected function handle_all($importurl) {
            $args = array();
            $response = wp_remote_get( $importurl, $args );

            $this->import_process = new WOT_IMPORT_Process();
            
            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                $blog_content = json_decode($response['body']);
                
                $i = 1;
                $_SESSION['successfull_count'] = 0;
                $_SESSION['unsuccessfull_count'] = 0;
                foreach( $blog_content as $single_post ){ 
                    //echo $i.'<br/>';
                    $this->import_process->push_to_queue($single_post);
                    $i++;
                }
               
                $this->import_process->save()->dispatch();
            }
		
	}

}

new IMPORT_BACKGROUND_PROCESS();

//class WOT_IMPORT_REQUEST extends WP_Async_Request {
//    
//        use WP_Example_Logger;
//	/**
//	 * @var string
//	 */
//	protected $action = 'import_blogpost';
//                
//	/**
//	 * Handle
//	 *
//	 * Override this method to perform any actions required
//	 * during the async request.
//	 */
//	protected function handle() {
//          
//	}
//}

class WOT_IMPORT_Process extends WP_Background_Process {

	use WP_Example_Logger;

	/**
	 * @var string
	 */
	protected $action = 'mp_import_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
        // $single_post = $item;

        $blog_args = array('post_type' => 'post');
        $insertation_count = 0;

        if( !empty( $item->title ) ){
            $blog_args['post_title'] = $item->title;
        }

        if( !empty( $item->content ) ){
            $post_content = wot_rapi_replace_internal_images($item->content);
            $blog_args['post_content'] = mb_convert_encoding($post_content,'UTF-8');
        }

        if( !empty( $item->post_status ) ){
            $blog_args['post_status'] = $item->post_status;
        }

        if( !empty( $item->slug ) ){
            $blog_args['post_name'] = $item->slug;
        }

        if( !empty( $item->post_excerpt ) ){
            $blog_args['post_excerpt'] = $item->post_excerpt;
        }

        if( !empty( $item->post_date ) ){
            $blog_args['post_date'] = $item->post_date;
        }
        
        if( !empty( $item->author_data->post_author ) && !empty( $item->author_data->post_author_email ) && !empty( $item->author_data->post_author_role ) ){
            $username = $item->author_data->post_author; //get user name from the API
            $useremail = sanitize_email($item->author_data->post_author_email);
            $userrole = $item->author_data->post_author_role;

            if(  !username_exists( $username ) ) {
                // if user not exist in our wordpress setup then create one 
                // Generate the password and create the user
                $password = wp_generate_password( 12, false );
                $user_id = wp_create_user( $username, $password, $useremail );

                // Set the nickname
                wp_update_user(
                    array(
                    'ID'          =>    $user_id,
                    'nickname'    =>    $username,
                    )
                );

                // Set the role
                $user = new WP_User( $user_id );
                $user->set_role( $userrole );

                // Email the user
                wp_mail( $useremail, 'Welcome! You are added with REST API | Custom API generator for cross platform and import export in WordPress plugin and', 'Your Password: ' . $password );
                $blog_args['post_author'] = $user_id;

                if( !empty( $item->author_custom_meta ) ){
                    foreach ($item->author_custom_meta as $key => $value) {
                        add_metadata( 'user', $user_id_from_api, $key, $value[0]);
                    }
                }
            }else{
                // if user exist then print username 
                $user_id_from_api = get_user_by('login',$username)->ID;
                $blog_args['post_author'] = $user_id_from_api;

                if( !empty( $item->author_custom_meta ) ){
                    foreach ($item->author_custom_meta as $key => $value) {
                        add_metadata( 'user', $user_id_from_api, $key, $value[0]);
                    }
                }
            }
        }

        if( !empty( $item->tag_names ) ){
            $multiple_tags = $item->tag_names;
            foreach ($multiple_tags as $single_tag) {
                if( !tag_exists( $single_tag ) ) {
                    wp_insert_term(
                        $single_tag,
                        'post_tag',
                        array(
                            'slug' => $single_tag
                        )
                    );
                }
                $blog_args['tags_input'] = $multiple_tags;
            }
        }

        if( !empty( $item->categories_name ) ){

            $multiple_categories = $item->categories_name;
            foreach ($multiple_categories as $single_category) {
                if( !category_exists( $single_category ) ) {
                    wp_insert_term(
                        $single_category,
                        'category',
                        array(
                            'slug' => $single_category
                        )
                    );
                }
            }

            $category_ids = [];
            foreach ($multiple_categories as $insert_single_cat_to_post) {
                $single_cat_id = get_cat_ID($insert_single_cat_to_post);
                array_push($category_ids, $single_cat_id);
            }
            $blog_args['post_category'] = $category_ids;
        }

        // insert custom post from here
        $post_id = wp_insert_post( $blog_args );
        
        if( !empty( $item->post_custom_meta ) ){
            foreach ($item->post_custom_meta as $key => $value) {
                add_post_meta($post_id, $key, $value[0]);
            }
        }

        if( !empty( $item->feature_image ) ){
            $image_url = $item->feature_image;
            $insertation_count = 1;
            ini_set('max_execution_time', $user_max_execution_time);

            // function Generate_Featured_Image( $image_url, $post_id  ){
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($image_url);
            $filename = basename($image_url);
            if(wp_mkdir_p($upload_dir['path']))
            $file = $upload_dir['path'] . '/' . $filename;
            else
            $file = $upload_dir['basedir'] . '/' . $filename;
            file_put_contents($file, $image_data);
        
            $wp_filetype = wp_check_filetype($filename, null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
            $res2= set_post_thumbnail( $post_id, $attach_id );
        }

        if($insertation_count == 1){
            $exisiting_post = get_site_option( 'post_insertation_count');
            $exisiting_post_increase = $exisiting_post + 1;
            update_site_option( 'post_insertation_count', $exisiting_post_increase );
        }
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}

?>