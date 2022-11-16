<?php

// 直接用 Comment 类名安装会报错
class CAFEHAUS_Comment extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'comments', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_comments' ),
        ]);
        register_rest_route( $this->namespace, 'post/comments', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_post_comments' ),
        ]);
        register_rest_route( $this->namespace, 'comment/add', [
            'methods'   => 'POST',
            'callback'  =>  array( $this, 'add_comment' ),
            'args'      => array( // 校验         
                'commentContent' => array(
                    'required' => true
                ),
                'postId' => array(
                    'required' => true
                ),
                'commentAuthorId' => array(
                    'required' => true
                ),
                'commentAuthor' => array(
                    'required' => true
                ),
            )
        ]);
    }

    // 获取评论列表
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

    // 获取文章评论
    public function get_post_comments($request) {
        $query_params = $request->get_query_params();
        $post_id = (int)$query_params['postId'];
        if (empty($post_id)) {
            return new WP_Error(10001, "文章id不能为空", "");
        }

        $args = array(
            'post_id' => $post_id,
        );

        $query = new WP_Comment_Query( $args );
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

        // 是否要格式化成树形数据
        $tree = ($query_params['tree'] === 'false') ? false : true;
        if ($tree) {
            $list = $this->fmt_tree($list);
        }

        $result["data"] = $list;
        $result["code"] = "200";
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    // 发表评论
    public function add_comment($request) {
        $comment_content = $request['commentContent'];
        if (mb_strlen($comment_content) > 20 ) {
            return new WP_Error(10001, "评论内容不能超过200个字符", "");
        }    
        $post_id = (int)$request['postId'];
        $comment_parent_id = (int)$request['commentParentId'] ?: 0;
        $comment_author_id = (int)$request['commentAuthorId'];
        $comment_author =  $request['commentAuthor'];

        $args = array(
            'comment_content' => $comment_content,
            'comment_post_ID' => $post_id,
            'user_id' => $comment_author_id,
            'comment_author' => $comment_author,
            'comment_parent' => $comment_parent_id,
            'comment_approved' => 0, // 默认不显示，要审核通过后展示
        );

        // https://developer.wordpress.org/reference/functions/wp_insert_comment/
        // wp_insert_comment( array $commentdata ): int|false
        $comment_res = wp_insert_comment($args);
        // 创建成功会直接返回评论 id
        if (is_int($comment_res)) {
            $result["data"] = $comment_res;
            $result["code"] = "200";
            $result["message"] = "评论成功，审核通过后将会显示";

            $response = new WP_REST_Response($result, 200);
            return $response;
        } else {
            return $comment_res;
        }
    }


    // 格式化文章评论成树桩结构
    // private function fmt_tree($rows, $id = 'id', $pid = 'parentId') {
    //     $items = array();
    //     foreach ($rows as $row) {
    //         $items[$row[$id]] = $row;
    //     }
    //     foreach ($items as $item) {
    //         $items[$item[$pid]]['children'][$item[$id]] = &$items[$item[$id]];
    //     }
    //     return isset($items[0]['children']) ? $items[0]['children'] : array();
    // }
    private function fmt_tree($arr, $pid = '0') {
        $tree = array();
        foreach ($arr as $key => $value) {
            if ($value['parentId'] == $pid) {
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