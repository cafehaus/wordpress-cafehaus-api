<?php

function get_articles($request) {
    $query_params = $request->get_query_params();
    $page = $query_params['page'];
    $posts_per_page = $query_params['size'];

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
        $img = get_article_img($id);
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

function get_article_img($postId) {
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

function get_articles_route() {
    register_rest_route( 'cafe/v1', 'articles', [
        'methods'   => 'GET',
        'callback'  => 'get_articles'
    ] );
}