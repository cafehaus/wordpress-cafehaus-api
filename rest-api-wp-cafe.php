<?php
/*
Plugin Name: REST API WP CAFE
Description: 支持多端前后端分离开发的 REST API 接口
Version: 0.0.1
Author: cafehaus
*/

define('RestApiWpCafePlugin_DIR', plugin_dir_path(__FILE__));
include(RestApiWpCafePlugin_DIR . 'includes/api/api.php');

if ( !class_exists( 'RestApiWpCafePlugin' ) ) {
    class RestApiWpCafePlugin {
        public function __construct() {
            new CAFE_API();
        }
    }

    new RestApiWpCafePlugin();
}