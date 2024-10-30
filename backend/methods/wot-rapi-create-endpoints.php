<?php 
// Exit //if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*
* customize the rest api data according to the user requirement
*/
function wot_rapi_posts_route($data){
    $args = [
        'numberposts' => 99999,
        'post_type' => 'post'
    ];

    $id=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["id"];
    $post_type=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["type"];
    $PostTitle=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["post_title"];
    $post_status=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["post_status"];
    $slug=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["slug"];
    $content=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["post_content"];
    $post_excerpt=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["post_excerpts"];
    $date=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["date"];
    $author=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author"];
    $author_fname=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_fname"];
    $author_lname=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_lname"];
    $author_email=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_email"];
    $author_role=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_role"];
    $categories=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["categories"];
    $categories_name=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["categories_name"];
    $tags=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["tags"];
    $tag_names=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["tag_names"];
    $author_avatar=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_image"];
    $author_custom_meta=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["author_custom_meta"];
    $feature_image=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["feature_image"];
    $feature_image_alt_txt=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["feature_image_alt_txt"];
    $next_post_url=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["next_post_url"];
    $next_post_title=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["next_post_title"];
    $previous_post_url=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["previous_post_url"];
    $previous_post_title=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["previous_post_title"];
    $social_media_sharing=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["social_media_sharing"];
    $post_custom_meta=json_decode(json_decode($data->get_attributes()['args'][0],true),true)["post_custom_meta"];

    $multiple_posts = get_posts($args);
    $data = [];
    $i = 0;
    
    foreach($multiple_posts as $single_post) {
        global $post;
        $post = get_post($single_post->ID);
        $data[$i]['ID'] = $single_post->ID;

        if($id==1){
            $data[$i]['ID'] = $single_post->ID; 
        } 
        if($post_type==1){
            $data[$i]['post_type'] = $single_post->post_type;
        }
        if($PostTitle==1){
            $data[$i]['title'] = $single_post->post_title;
        }
        if($post_status==1){
            $data[$i]['post_status'] = $single_post->post_status;
        }
        if($slug==1){
            $data[$i]['slug'] = $single_post->post_name;
        }
        if($content==1){
            $data[$i]['content'] = $single_post->post_content;
        }
        if($post_excerpt==1){
            $data[$i]['post_excerpt'] = $single_post->post_excerpt;
        }
        if($date==1){
            $data[$i]['post_date'] = $single_post->post_date;
        }
        if($author==1 || $author_fname==1 || $author_lname==1 || $author_email==1 || $author_role==1){
            $get_author_id = get_post_field ('post_author', $single_post->ID);
            $author_data = [];
                if($author==1){
                    $author_data['post_author'] = get_the_author_meta('display_name', $get_author_id);
                }
                if($author_fname==1){
                    $author_data['post_author_firstname'] = get_the_author_meta('first_name', $get_author_id);
                }
                if($author_lname==1){
                    $author_data['post_author_last_name'] = get_the_author_meta('last_name', $get_author_id);
                }
                if($author_email==1){
                    $author_data['post_author_email'] = get_the_author_meta('user_email', $get_author_id);
                }
                if($author_role==1){
                    $author_data['post_author_role'] = get_userdata($get_author_id)->roles[0];
                }

            $data[$i]['author_data'] = $author_data;
        }
        if($categories==1){
            $all_cat = get_the_category($single_post->ID);
            if( !empty( $all_cat ) ){
                $data[$i]['categories'] = array_column($all_cat, 'term_id');
            }else{
                $data[$i]['categories'] = null;
            }
        }
        if($categories_name==1){
            $all_cat = get_the_category($single_post->ID);
            if( !empty( $all_cat ) ){
                $data[$i]['categories_name'] = array_column($all_cat, 'name');
            }else{
                $data[$i]['categories_name'] = null;
            }
        }
        if($tags==1){
            $all_tags = get_the_tags($single_post->ID);
            if( !empty( $all_tags ) ){
                $data[$i]['tag_ids'] = array_column($all_tags, 'term_id');
            }else{
                $data[$i]['tag_ids'] = null;
            }
        }
        if($tag_names==1){
            $all_tags = get_the_tags($single_post->ID);
            if( !empty( $all_tags ) ){
                $data[$i]['tag_names'] = array_column($all_tags, 'name');
            }else{
                $data[$i]['tag_names'] = null;
            }
        }
        if($author_avatar==1){
            $author_avatar_url = get_avatar_url( get_the_author_meta( $single_post->ID ), 32 );
            if(!empty($author_avatar)){ 
                $data[$i]['author_avatar'] = $author_avatar_url;
            }else{
                $data[$i]['author_avatar'] = null;
            }
        }
        if($author_custom_meta==1){
            $author_id = get_post_field('post_author', $single_post->ID);
			$data[$i]['author_custom_meta'] = get_metadata( 'user', $author_id );
        }
        if($feature_image==1){
            $data[$i]['feature_image'] = wp_get_attachment_url( get_post_thumbnail_id($single_post->ID) );
        }
        if($feature_image_alt_txt==1){
            $data[$i]['feature_image_alt_txt'] =  get_post_meta( get_post_thumbnail_id($single_post->ID), '_wp_attachment_image_alt', true );
        }
        if($next_post_url==1){
            $wot_next_post = get_adjacent_post(false, '', false);
            if(!empty($wot_next_post)){
                $data[$i]['next_post_url'] = get_permalink($wot_next_post->ID);
            }else{
                $data[$i]['next_post_url'] = null;
            }
        }
        if($next_post_title==1){
            $wot_next_post = get_adjacent_post(false, '', false);
                if(!empty($wot_next_post)){
                    $data[$i]['next_post_title'] = get_the_title($wot_next_post->ID);
                }else{
                    $data[$i]['next_post_title'] = null;
                }
        }
        if($previous_post_url==1){
            $wot_previous_post = get_adjacent_post(false, '', true);
                if(!empty($wot_previous_post)){
                    $data[$i]['previous_post_url'] = get_permalink($wot_previous_post->ID);
                }else{
                    $data[$i]['previous_post_url'] = null;
                }
        }
        if($previous_post_title==1){
            $wot_previous_post = get_adjacent_post(false, '', true);
                if(!empty($wot_previous_post)){
                    $data[$i]['previous_post_title'] = get_the_title($wot_previous_post->ID);
                }else{
                    $data[$i]['previous_post_title'] = null;
                }
        }
        if($social_media_sharing==1){
            $blog_link = get_permalink($single_post->ID);
            $blog_title = get_the_title($single_post->ID);
            
            $share_links = array(
                'twitter' => "https://twitter.com/intent/tweet?text=".$blog_title."&url=".$blog_link,
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=".$blog_link."&quote=".$blog_title,
                'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=".$blog_link
            );

            $data[$i]['social_media_sharing'] = $share_links;
        }
        if($post_custom_meta==1){
            // For acf only
            // $data[$i]['post_custom_meta'] = get_post_meta($single_post->ID);

            // For every meta feilds in post data
            $data[$i]['post_custom_meta'] = get_metadata( 'post', $single_post->ID );
        }

        $i++;
    }
    return $data;
}

/*
*create multiple routs according to the user inputs
*/
add_action('rest_api_init', 'wot_rapi_create_rest_route');
function wot_rapi_create_rest_route(){
    global $wpdb, $post;
    $entriesLists = $wpdb->get_results("SELECT * FROM ". WOT_RAPI_SETTINGS_TABLE);
    $arrays = json_decode(json_encode($entriesLists), true);

    /*
    * Resgister rest route with new endpoint everytime user fill up the form
    * in the callback function we have passed the user selected data as args
    */
    foreach ($arrays as $array) {
        $route_slug = $array['api_slug'];
        $selected_data = $array['selected_data'];

        register_rest_route('wp/v1', $route_slug, [
            'methods' => 'GET',
            'callback' => 'wot_rapi_posts_route',
            'permission_callback' => '__return_true',
            'args' => array(json_encode($selected_data, true))
        ], true);
    }
};


/* 
* params is the query array passed to WP_Query
*/
add_action( 'rest_post_query', 'wot_rapi_override_per_page_for_posts' );
function wot_rapi_override_per_page_for_posts( $params ) {
    if ( isset( $params ) AND isset( $params[ 'posts_per_page' ] ) ) {
            $params[ 'posts_per_page' ] = PHP_INT_MAX;
    }
    return $params;
}
?>