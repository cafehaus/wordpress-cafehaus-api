<?php
/*
Plugin Name: REST API WP CAFE
Description: 支持多端前后端分离开发的 REST API 接口
Version: 0.0.1
Author: cafehaus
*/

function cafe_rest_hello_callback() {
    return 'hello ~';
}

function cafe_rest_register_route() {
    register_rest_route( 'cafe/v1', 'hello', [
        'methods'   => 'GET',
        'callback'  => 'cafe_rest_hello_callback'
    ] );
}

add_action('rest_api_init', 'cafe_rest_register_route');