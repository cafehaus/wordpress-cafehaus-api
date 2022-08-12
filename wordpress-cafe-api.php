<?php
/*
Plugin Name: Wordpress Cafe Api
Description: 支持多端前后端分离开发的 REST API 接口
Version: 0.0.1
Author: cafehaus
*/

define('CafeApiPlugin_DIR', plugin_dir_path(__FILE__));
include(CafeApiPlugin_DIR . 'includes/api/api.php');

if ( !class_exists( 'CafeApiPlugin' ) ) {
    class CafeApiPlugin {
        public function __construct() {
            new CAFE_API();
        }
    }

    new CafeApiPlugin();
}