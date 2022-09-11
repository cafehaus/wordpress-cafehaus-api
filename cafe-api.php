<?php
/*
Plugin Name: CAFE API
Description: 兼容小程序、APP和H5的多端 API 插件，提供更加优雅的路由、入参和出参，开箱即用零依赖零设置，让前端用着更省心
Version: 1.0.0
Author: cafehaus
Author URI: https://github.com/cafehaus
License: GPL v3
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