<?php

class CAFEHAUS_Tag extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'tags', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_tags_list' ),
        ] );
    }

    // 获取标签列表
    public function get_tags_list($request) {
        $query_params = $request->get_query_params();
        $page = (int)$query_params['page'] ?: 1;
        $size = (int)$query_params['size'] ?: 10;
        $orderby = $query_params['orderby'] ?: 'name'; // name, slug, count
        $order = $query_params['order'] ?: 'ASC'; // DESC, ASC
        // 注意 query 里的参数会被转成字符串
        $hide_empty = ($query_params['hideEmpty'] === 'true') ? true : false;

        $args = array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => $hide_empty, // for development
            'orderby'    => $orderby, // 排序 count name
            'order'      => $order,
            // 'count'      => true, // 是否计算总数，实际测试无效
            'number'     => $size,
            'offset'     => ($page - 1) * $size,
        );

        $tags = get_tags($args);
        $total = wp_count_terms(array(
            'taxonomy'    => 'post_tag',
            'hide_empty'  => $hide_empty
        ));

        foreach ( $tags as $t ) {
            $list[] = array(
                "id" => $t->term_id,
                "name" => $t->name,
                "slug" => $t->slug, // 别名
                "description" => $t->description,
                "count" => $t->count, // 文章数
            );
        }

        $data["list"] = $list;
        $data["total"] = $total;
        $data["totalPages"] = ceil( $total / $size );
        $data["page"] = $page;
        $data["size"] = $size;

        $result["data"] = $data;
        $result["code"] = "200";
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }
}