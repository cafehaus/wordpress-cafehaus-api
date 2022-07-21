<?php
include_once( 'article.php' );

class CAFE_API extends WP_REST_Controller{
    public function __construct() {
        $this->init();
    }
    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_my_routes' ) );
    }

    public function register_my_routes() {
        $api_list = array(
            'Article',
        );
        foreach ( $api_list as $api ) {
            $this->$api = new $api();
            $this->$api->init();
        }
    }
}