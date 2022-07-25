<?php

// 直接用 Comment 类名安装会报错
class M_Comment extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'comments', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_comments' ),
        ] );
    }

    // 获取文章列表
    public function get_comments($request) {
        $query_params = $request->get_query_params();
        $page = (int)$query_params['page'] ?: 1;
        $size = (int)$query_params['size'] ?: 10;
        // $pre = $size * ($page - 1);
        // $sql = $wpdb->prepare("select * from wp_comments where comment_approved='1' AND comment_ID >= (select comment_ID from wp_comments where comment_approved='1' order by comment_ID limit ".$pre.", 1) limit ".$size.";");
        // $comments = $wpdb->get_results($sql);

        // @type bool  $no_found_rows  Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default: true.
        // no_found_rows 是否禁止 SQL_CALC_FOUND_ROWS 查询，这玩意儿就是用来计算总条数的，据说性能不高，默认 true 被禁止了，所以 max_num_pages、found_comments 就不会被设置
        // 查询文章用到的 WP_Query 其实源码里也有这个参数，但是没有默认值
        $args = array(
            'no_found_rows' => false,
            'number'        => $size,
            'paged'         => $page,
            'orderby'       => 'comment_date',
            'order'         => 'desc',
        );

        $query = new WP_Comment_Query( $args );
        $max_pages = $query->max_num_pages;
        $total = $query->found_comments;
        $comments = $query->comments;

        foreach ( $comments as $c ) {
            $list[] = array(
                "id" => $c->comment_ID,
                "content" => $c->comment_content,
                "date" => $c->comment_date,
                "author" => $c->comment_author,
                "postId" => $c->comment_post_ID,
                "parentId" => $c->comment_parent, // 父级评论ID
            );
        }

        $data["list"] = $list;
        $data["total"] = $total;
        $data["totalPages"] = $max_pages;
        $data["page"] = $page;
        $data["size"] = $size;

        $result["data"] = $data;
        $result["code"] = "200";
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }
}