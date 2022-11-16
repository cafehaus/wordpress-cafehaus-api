<?php
include_once( 'class_cafehaus_post.php' );
include_once( 'class_cafehaus_category.php' );
include_once( 'class_cafehaus_tag.php' );
include_once( 'class_cafehaus_comment.php' );
include_once( 'class_cafehaus_user.php' );

class CAFEHAUS_API extends WP_REST_Controller{
    public function __construct() {
        $this->init();
    }
    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_my_routes' ) );
    }

    public function register_my_routes() {
        $api_list = array(
            'CAFEHAUS_Post',
            'CAFEHAUS_Category',
            'CAFEHAUS_Tag',
            'CAFEHAUS_Comment',
            'CAFEHAUS_User',
        );
        foreach ( $api_list as $api ) {
            $this->$api = new $api();
            $this->$api->init();
        }
    }
}