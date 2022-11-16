<?php

class CAFEHAUS_Category extends WP_REST_Controller{
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
        $query_params = $request->get_query_params();
        // 注意 query 里的参数会被转成字符串
        $hide_empty = ($query_params['hideEmpty'] === 'true') ? true : false;

        // 是否要格式化成树形数据
        $tree = ($query_params['tree'] === 'false') ? false : true;

        $args = array(
            "hide_empty" => $hide_empty, // 是否隐藏空内容的分类
        );
        $categories = get_categories($args);

        foreach ( $categories as $cate ) {
            $list[] = array(
                "id" => $cate->term_id,
                "name" => $cate->name,
                "description" => $cate->description,
                "count" => $cate->count,
                "parent" => $cate->parent, // 父级id为0时，实际上是无父级
            );
        }

        if ($tree) {
            $list = $this->fmt_tree($list);
        }

        $result["data"] = $list;
        $result["code"] = "200";
        $result["success"] = true;
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    private function fmt_tree($arr, $pid = '0') {
        $tree = array();
        foreach ($arr as $key => $value) {
            if ($value['parent'] == $pid) {
                $value['children'] = $this->fmt_tree($arr, $value['id']);
                if (!$value['children']) {
                    unset($value['children']);
                }
                $tree[] = $value;
            }
        }
        return $tree;
    }
}