<?php

class M_Article extends WP_REST_Controller{
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
            'args'      => array(
                'id' => array(
                    'required' => true
                ),
            )
        ] );
    }

    // 获取文章列表
    public function get_articles($request) {
        $query_params = $request->get_query_params();
        $page = (int)$query_params['page'] ?: 1;
        $posts_per_page = (int)$query_params['size'] ?: 10;

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
            $content = $post->post_content; // 去提取内容里的图片

            // 注意：类自身的方法要通过 $this 去调用
            $img = $this->get_article_img($id, $content);
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
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    // 获取文章详情
    public function get_article_Detail($request) {
        $query_params = $request->get_query_params();
        $id = $query_params['id'];

        $post = get_post($id);
        $postId = $post->ID;

        if (empty($post) || empty($postId)) {
            // $error["code"] = "20001";
            // $error["message"] = "文章不存在";
            // $error["data"] = "";
            return new WP_Error("20001", "文章不存在", "");
        }
        $authorId = $post->post_author;
        $user = get_user_by('id', $authorId);
        $author_name = $user->data->display_name ?: $user->data->user_nicename;

        $tags = $this->fmt_data(get_the_tags($postId)); // term_id、name
        $categories = $this->fmt_data(get_the_category($postId)); // term_id、name

        $data["id"] = $postId;
        $data["title"] = $post->post_title;
        $data["content"] = $post->post_content;
        $data["excerpt"] = $post->post_excerpt;
        $data["postDate"] = $post->post_date;
        $data["authorId"] = $authorId;
        $data["author"] = $author_name;
        $data["commentCount"] = (int)$post->comment_count;
        $data["categories"] = $categories;
        $data["tags"] = $tags;

        $result["data"] = $data;
        $result["code"] = "200";
        $result["message"] = "请求成功";

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

    // 格式化数据
    private function fmt_data($arr) {
        if (!empty($arr)) {
            // $new_arr = array();
            // foreach ($arr as $val) {
            //     $cur_info["id"] = $val->term_id;
            //     $cur_info["name"] = $val->name;
            //     array_push($new_arr, $cur_info);
            // }
            // return $new_arr;

            foreach ($arr as $val) {
                $list[] = array(
                    "id" => $val->term_id,
                    "name" => $val->name,
                );
            }

            return $list;
        } else {
            return [];
        }
    }
}