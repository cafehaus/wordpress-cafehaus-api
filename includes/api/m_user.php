<?php

class M_User extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'users', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_users' ),
        ] );
    }

    // 获取文章列表
    public function get_users($request) {
        $query_params = $request->get_query_params();
        $page = (int)$query_params['page'] ?: 1;
        $size = (int)$query_params['size'] ?: 10;

        $args = array(
            'count_total' => true, // 默认就是true，是否计算总数，跟WP_Query、WP_Comment_Query里的 no_found_rows 参数一样
            'number'        => $size,
            'paged'         => $page,
        );

        $query = new WP_User_Query( $args );
        $total = $query->get_total();
        $max_pages = ceil( $total / $size );
        $users = $query->get_results();

        // 用户角色 wp_options => wp_user_roles
        // administrator-管理员 editor-编辑 author-作者 contributor-贡献者 subscriber-订阅者
        $role_info = array(
            administrator =>  '管理员',
            editor        =>  '编辑',
            author        =>  '作者',
            contributor   =>  '贡献者',
            subscriber     =>  '订阅者',
        );

        foreach ( $users as $u ) {
            $roleId = $u->roles[0];

            $list[] = array(
                "id" => $u->ID,
                "name" => $u->data->display_name ?: $u->$data->user_nicename,
                "date" => $u->data->user_registered,
                "roleId" => $roleId,
                "roleName" => $role_info[$roleId],
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