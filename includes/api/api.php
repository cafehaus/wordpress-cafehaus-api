<?php
include_once( 'm_article.php' );
include_once( 'm_category.php' );
include_once( 'm_tag.php' );
include_once( 'm_comment.php' );
include_once( 'm_user.php' );

class CAFE_API extends WP_REST_Controller{
    public function __construct() {
        $this->init();
    }
    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_my_routes' ) );
    }

    public function register_my_routes() {
        $api_list = array(
            'M_Article',
            'M_Category',
            'M_Tag',
            'M_Comment',
            'M_User',
        );
        foreach ( $api_list as $api ) {
            $this->$api = new $api();
            $this->$api->init();
        }
    }
}