<?php

class Category extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'categories', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_all_category' ),
        ] );
    }

    // 获取分类列表
    public function get_all_category($request) {
        $categories = get_categories();

        foreach ( $categories as $cate ) {
            $list[] = array(
                "id" => $cate->id,
                "name" => $cate->name,
                "description" => $cate->description,
                "count" => $cate->count,
                "parent" => $cate->parent, // 父级id为0时，实际上是无父级
            );
        }

        $result["data"] = $list;
        $result["code"] = "200";
        $result["success"] = true;
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }
}