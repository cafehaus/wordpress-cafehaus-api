<?php

class Article extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'articles', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_articles' ),
        ] );
        // register_rest_route( $this->namespace, 'article/(?P<id>[\d]+)', [
        register_rest_route( $this->namespace, 'article', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_article_Detail' ),
        ] );
    }

    // 获取文章列表
    public function get_articles($request) {
        $query_params = $request->get_query_params();
        $page = $query_params['page'] || 1;
        $posts_per_page = $query_params['size'] || 10;

        $args = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $page,
            'orderby'           => 'date',
            'order'             => 'desc',
        );

        $query = new WP_Query( $args ); 
        $max_pages = $query->max_num_pages;
        $total = $query->found_posts;
        $posts = $query->posts;

        foreach ( $posts as $post ) {
            $id = $post->ID;
            // 注意：类自身的方法要通过 $this 去调用
            $img = $this->get_article_img($id);
            $list[] = array(
                "id" => $id,
                "title" => $post->post_title,
                "postDate" => $post->post_date,
                "commentCount" => (int)$post->comment_count,
                "img" => $img,
            );
        }
        $data["list"] = $list;
        $data["total"] = $total;
        $data["totalPages"] = $max_pages;
        $data["page"] = $page;
        $data["size"] = $posts_per_page;

        $result["data"] = $data;
        $result["code"] = "200";
        $result["success"] = true;
        $result["msg"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    // 获取文章详情
    public function get_article_Detail($request) {
        $query_params = $request->get_query_params();
        $id = $query_params['id'];

        if (empty($id)) {
            $error["code"] = "10001";
            $error["msg"] = "文章id参数不存在";
            $error["data"] = "";
            return new WP_Error($error);
        }

        $post = get_post($id);
        $postId = $post->ID;

        if (empty($postId)) {
            $error["code"] = "20001";
            $error["msg"] = "文章不存在";
            $error["data"] = "";
            return new WP_Error($error);
        }
        $authorId = $post->post_author;
        $user = get_user_by('id', $authorId);
        $author_name = $user->display_name || $user->user_nicename;

        $data["id"] = $post->ID;
        $data["title"] = $post->post_title;
        $data["content"] = $post->post_content;
        $data["excerpt"] = $post->post_excerpt;
        $data["postDate"] = $post->post_date;
        $data["authorId"] = $authorId;
        $data["author"] = $author_name;
        $data["commentCount"] = (int)$post->comment_count;
        $data["categories"] = $post->categories;
        $data["tags"] = $post->tags;

        $result["data"] = $data;
        $result["code"] = "200";
        $result["success"] = true;
        $result["msg"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    // 获取文章特色图
    private function get_article_img($postId) {
        //获取缩略的ID
        $imgId = get_post_thumbnail_id($postId);
        if (!empty($imgId)) {
            //特色图缩略图：thumbnail/medium/large/full
            $image = wp_get_attachment_image_src($imgId, 'medium');
            return $image[0];
        } else {
            return '';
        }
    }
}