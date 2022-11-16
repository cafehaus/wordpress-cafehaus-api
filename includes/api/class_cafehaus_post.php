<?php

class CAFEHAUS_Post extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'posts', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_articles' ),
        ]);
        register_rest_route( $this->namespace, 'post/(?P<id>[\d]+)', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_detail_by_url' ),
        ]);
        register_rest_route( $this->namespace, 'post', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_Detail' ),
            'args'      => array(
                'id' => array(
                    'required' => true
                ),
            )
        ]);
    }

    // 获取文章列表
    public function get_articles($request) {
        // https://developer.wordpress.org/reference/classes/wp_query/
        $query_params = $request->get_query_params();
        $page = (int)$query_params['page'] ?: 1;
        $posts_per_page = (int)$query_params['size'] ?: 10;
        $post_type = $query_params['type'] ?: 'post'; // post, page
        $post_status = $query_params['status'] ?: 'publish'; // publish, future, draft, pending, private, trash
        $orderby = $query_params['orderby'] ?: 'date'; // ID, date, title, modified, rand, comment_count
        $order = $query_params['order'] ?: 'DESC'; // DESC, ASC

        $args = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $page,
            'post_type'         =>  $post_type,
            'post_status'       =>  $post_status,
            'orderby'           =>  $orderby,
            'order'             =>  $order
        );

        // $title = $query_params['title'];
        // if (!empty($title)) { // 文章标题，需完全匹配，似乎没多大用
        //     $args['title'] = $title;
        // }
        $search = $query_params['search'];
        if (!empty($search)) { // 文章标题模糊搜索
            $args['s'] = $search;
        }
        $authorId = $query_params['authorId'];
        if (!empty($authorId)) { // 文章作者id，多个用英文逗号分隔
            $args['author'] = $authorId;
        }
        $cateId = $query_params['cateId'];
        if (!empty($cateId)) { // 分类id，多个用英文逗号分隔
            $args['cat'] = (int)$cateId;
        }
        $tagId = $query_params['tagId'];
        if (!empty($tagId)) { // 标签id，多个用英文逗号分隔
            $args['tag_id'] = $tagId;
        }
        $sticky = $query_params['sticky'];
        if (!empty($sticky) && $sticky === 'true') { // 是否只查询置顶文章
            // 注意参数中间是两个下划线
            $args['post__in'] = get_option( 'sticky_posts' );
        }

        // https://developer.wordpress.org/reference/functions/get_posts/
        // 时间日期
        // year int Accepts any four-digit year
        // monthnum int 1-12
        // day int 1-31
        // hour int 0-23
        // minute int 0-59
        // second int 0-59
        $year = $query_params['year'];
        if (!empty($year)) { // 4个数字年份
            $args['year'] = (int)$year;
        }
        $monthnum = $query_params['monthnum'];
        if (!empty($monthnum)) { // 月份
            $args['monthnum'] = (int)$monthnum;
        }
        $day = $query_params['day'];
        if (!empty($day)) { // 天
            $args['day'] = (int)$day;
        }
        $hour = $query_params['hour'];
        if (!empty($hour)) { // 小时
            $args['hour'] = (int)$hour;
        }
        $minute = $query_params['minute'];
        if (!empty($minute)) { // 分钟
            $args['minute'] = (int)$minute;
        }
        $second = $query_params['second'];
        if (!empty($second)) { // 秒
            $args['second'] = (int)$second;
        }

        $query = new WP_Query( $args );
        $max_pages = $query->max_num_pages;
        $total = $query->found_posts;
        $posts = $query->posts;

        foreach ( $posts as $post ) {
            $id = $post->ID;
            $content = $post->post_content; // 去提取内容里的图片

            // 注意：类自身的方法要通过 $this 去调用
            $img = $this->get_article_img($id);
            $imgs = $this->get_html_images($content);

            $list[] = array(
                "id" => $id,
                "title" => $post->post_title,
                "postDate" => $post->post_date,
                "commentCount" => (int)$post->comment_count,
                "img" => $img, // 文章设置的特色图
                "imgs" => $imgs, // 文章内容中解析出的所有图片地址
                "format" => get_post_format($id) ?: 'standard', // WP_Query里查出来无这个字段，注意默认的标准形式 get_post_format 也会返回 false
                "sticky" => is_sticky($id), // 是否置顶
                "status" => $post->post_status, // 文章状态
                "type" => $post->post_type, // 文章类型
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

    // 获取文章详情: 通过 id 参数
    public function get_detail($request) {
        // get_next_post() 和 get_previous_post()：Null if global $post is not set
        // 获取前后文函数里有用到 $post，不 global 一下，会直接返回 null
        global $post;

        $query_params = $request->get_query_params();
        $id = $query_params['id'];

        $post = get_post($id);
        $postId = $post->ID;

        if (empty($post) || empty($postId)) {
            return new WP_Error(20001, "文章不存在", "");
        }

        $result["data"] = $this->fmt_detail_data($post, $postId);
        $result["code"] = "200";
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    // 获取文章详情：通过路径传参
    public function get_detail_by_url($request) {
        // get_next_post() 和 get_previous_post()：Null if global $post is not set
        // 获取前后文函数里有用到 $post，不 global 一下，会直接返回 null
        global $post;

        $id = (int) $request['id'];
        $post = get_post($id);
        $postId = $post->ID;

        if (empty($post) || empty($postId)) {
            return new WP_Error(20001, "文章不存在", "");
        }

        $result["data"] = $this->fmt_detail_data($post, $postId);
        $result["code"] = "200";
        $result["message"] = "请求成功";

        $response = new WP_REST_Response($result, 200);
        return $response;
    }

    private function fmt_detail_data($post, $postId) {
        $authorId = $post->post_author;
        $user = get_user_by('id', $authorId);
        $author_name = $user->data->display_name ?: $user->data->user_nicename;

        $tags = $this->fmt_data(get_the_tags($postId)); // term_id、name
        $categories = $this->fmt_data(get_the_category($postId)); // term_id、name

        // 获取前后文
        // get_adjacent_post()
        // get_next_post
        // get_previous_post
        $next = get_next_post();
        $nextId = $next->ID;
        $next_post = null;
        if (!empty($nextId)) {
            $nextContent = $next->post_content;
            $next_post = array(
                "id"      =>  $nextId,
                "title"   =>  $next->post_title,
                "img"     =>  $this->get_article_img($nextId, $nextContent),
                // "imgs"    =>  $this->get_html_images($nextContent),
            );
        }

        $previous = get_previous_post();
        $previousId = $previous->ID;
        $previous_post = null;
        if (!empty($previousId)) {
            $previousContent = $previous->post_content;
            $previous_post = array(
                "id"      =>  $previousId,
                "title"   =>  $previous->post_title,
                "img"     =>  $this->get_article_img($previousId, $previousContent),
                // "imgs"    =>  $this->get_html_images($previousContent),
            );
        }

        $data["id"] = $postId;
        $data["title"] = $post->post_title;
        $data["content"] = $post->post_content;
        $data["excerpt"] = $post->post_excerpt;
        $data["postDate"] = $post->post_date;
        $data["authorId"] = $authorId;
        $data["author"] = $author_name;
        $data["commentCount"] = (int)$post->comment_count;
        $data["commentStatus"] = $post->comment_status; // 是否可以评论 open, closed
        $data["format"] = get_post_format($postId) ?: 'standard'; // 文章格式(WP_Query里查出来无下面两个字段)
        $data["sticky"] = is_sticky($postId); // 是否置顶
        $data["status"] = $post->post_status; // 文章状态
        $data["type"] = $post->post_type; // 文章类型
        $data["categories"] = $categories;
        $data["tags"] = $tags;
        $data["nextPost"] = $next_post; // 后一篇文章
        $data["previousPost"] = $previous_post; // 前一篇文章

        return $data;
    }

    // 获取文章特色图
    // $postId 文章ID，必传参数
    // $content 文章内容，可选参数，传了如果没有特色图，会默认去内容中提取图片地址
    private function get_article_img($postId, $content) {
        //获取缩略的ID
        $imgId = get_post_thumbnail_id($postId);
        if (!empty($imgId)) {
            //特色图缩略图：thumbnail/medium/large/full
            $image = wp_get_attachment_image_src($imgId, 'medium');
            return $image[0];
        } else {
            if (!empty($content)) {
                return $this->get_html_images($content, 0);
            } else {
                return '';
            }
        }
    }

    /*
    * 解析HTML中的所有图片地址
    * $content 参数
    * $index 可选参数，指定返回第几个，默认 all 返回所有
    * return：如果指定了 $index 值 返回 字符串 否则 返回数组参数
    * get_html_images($content,0) 返回第一个图片地址
    * get_html_images($content) 返回数组
    * $pattern = "/<img.*?src=[\'|\"](.*?(?:[\.gif|\.GIF|\.jpg|\.JPG|\.jpge|\.JPGE|\.png|\.PNG]))[\'|\"].*?[\/]?>/";
    */
    private function get_html_images($content, $index = 'all'){
        $pattern = "<img.*?src=[\'｜\"](.*?)[\'|\"].*?>";
        preg_match_all($pattern, $content, $match);

        if(isset($match[1]) && !empty($match[1])){
            if($index==='all'){
                return $match[1];
            }
            if(is_numeric($index) && isset($match[1][$index])){
                return $match[1][$index];
            }
        }
        return null;
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
            return null;
        }
    }
}