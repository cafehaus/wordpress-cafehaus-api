<?php

class CAFEHAUS_User extends WP_REST_Controller{
    public function __construct() {
        $this->namespace = 'cafe/v1';
    }
    public function init() {
        register_rest_route( $this->namespace, 'users', [
            'methods'   => 'GET',
            'callback'  =>  array( $this, 'get_users' ),
        ]);
        register_rest_route( $this->namespace, 'register', [
            'methods'   => 'POST',
            'callback'  =>  array( $this, 'user_register' ),
            // 'args'      => array( // 校验返回的错误提示格式不是想要的，放到下面自定义校验           
            //     'userName' => array(
            //         'required' => true
            //     ),
            //     'password' => array(
            //         'required' => true
            //     )
            // )
        ]);
        register_rest_route( $this->namespace, 'login', [
            'methods'   => 'POST',
            'callback'  =>  array( $this, 'user_login' ),
        ]);
        register_rest_route( $this->namespace, 'user/password/update', [
            'methods'   => 'POST',
            'callback'  =>  array( $this, 'user_password_update' ),
        ]);
        register_rest_route( $this->namespace, 'user/info/update', [
            'methods'   => 'POST',
            'callback'  =>  array( $this, 'user_info_update' ),
        ]);
    }

    // 获取用户列表
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

        $list = [];
        foreach ( $users as $u ) {
            // $roleId = $u->roles[0];

            // $list[] = array(
            //     "id" => $u->ID,
            //     "userName" => $u->$data->user_login, // 用户名
            //     "nickname" => $u->data->display_name ?: $u->$data->user_nicename,
            //     "date" => $u->data->user_registered,
            //     "roleId" => $roleId,
            //     "roleName" => $role_info[$roleId],
            // );
            array_push($list, $this->fmt_user_info($u));
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

    // 注册
    public function user_register($request) {
        $username = $request['userName'];
        $password = $request['password'];

        if (empty($username)) { // 最长只能 60
            return new WP_Error(10001, "用户名不能为空", "");
        } else if (!preg_match("/^[a-zA-Z0-9_]*$/",$username)) {
            return new WP_Error(10001, "用户名只能包含数字、字母和下划线", "");
        }

        if (empty($password)) {
            return new WP_Error(10001, "密码不能为空", "");
        } else if (!preg_match("/^[a-zA-Z0-9_]*$/",$password)) {
            return new WP_Error(10001, "密码只能包含数字、字母和下划线", "");
        }

        $user = wp_create_user($username, $password);

        // wp_create_user( string $username, string $password, string $email = '' ): int|WP_Error
        // 创建成功会直接返回用户 id
        if (is_int($user)) {
            $result["data"] = $user;
            $result["code"] = "200";
            $result["message"] = "注册成功";

            $response = new WP_REST_Response($result, 200);
            return $response;
        } else if (is_wp_error($user)) {
            return $user;
        }

        // $error = $user["errors"];
        // if (!empty($error)) {
        //     $existing_user = $error["existing_user_login"];

        //     if (!empty($existing_user)) {
        //         $existing_user_msg = $existing_user[0] ?: "账号不能重复";
        //         return new WP_Error("0", $existing_user_msg, "");
        //         // 用户名已注册返回的是下面这样的
        //         // {
        //         //     "errors": {
        //         //         "existing_user_login": [
        //         //             "抱歉，用户名已存在！"
        //         //         ]
        //         //     },
        //         //     "error_data": []
        //         // }
        //     } else {
        //         return new WP_Error("0", "注册失败", "");
        //     }

        // } else {
        //     return new WP_Error("0", "注册失败", "");
        // }
    }

    // 登录
    public function user_login($request) {
        $username = $request['userName'];
        $password = $request['password'];

        if (empty($username)) {
            // 坑：WP_Error 的第一个状态码如果是字符串数字会被直接转成数字
            // 切记不能设置为数字或字符串 0，会导致不能抛出错误提示
            return new WP_Error(10001, "用户名不能为空", "");
        }

        if (empty($password)) {
            return new WP_Error(10001, "密码不能为空", "");
        }

        $user = wp_authenticate($username, $password);
        if (is_wp_error($user)) {
            // message 里会带 html 标签
            // $messages = $user->get_error_messages();
            // $message = $user->get_error_message();
            $error_code = $user->get_error_code();
            $error_enum = array(
                "invalid_username"   =>  "用户不存在",
                "incorrect_password" =>  "密码不正确"
            );
            $message = $error_enum[$error_code] ?: "登录失败";

            // $result["data"] = $user;
            $result["data"] = "";
            $result["code"] = "0";
            $result["message"] = $message;

            $response = new WP_REST_Response($result, 200);
            return $response;
        } else {
            $result["data"] = $this->fmt_user_info($user, 'login');
            $result["code"] = "200";
            $result["message"] = "登录成功";

            $response = new WP_REST_Response($result, 200);
            return $response;
        }
    }

    // 修改密码
    public function user_password_update($request) {
        $user_id = $request['userId'];
        $password = $request['password'];

        if (empty($user_id)) {
            return new WP_Error(10001, "用户id不能为空", "");
        }
        if (empty($password)) {
            return new WP_Error(10001, "密码不能为空", "");
        }

        // wp_set_password( string $password, int $user_id ) // 此方法没有返回值，不用这个
        // $password_res = wp_set_password($password, $user_id);

        $args = array(
            'ID'              => $user_id,
            'user_pass'       => $password,
        );
        
        // wp_update_user( array|object|WP_User $userdata ): int|WP_Error
        $update_res = wp_update_user($args);
        // 修改成功会直接返回用户 id
        if (is_int($update_res)) {
            $result["data"] = $update_res;
            $result["code"] = "200";
            $result["message"] = "新密码设置成功";

            $response = new WP_REST_Response($result, 200);
            return $response;
        } else if (is_wp_error($update_res)) {
            return $update_res;
        }
    }

    // 修改用户信息
    public function user_info_update($request) {
        $user_id = $request['userId'];
        $nickname = $request['nickname'];
        // $display_name =  $request['displayName'];
        $user_email =  $request['email'];
        $description =  $request['description'];

        if (empty($user_id)) {
            return new WP_Error(10001, "用户id不能为空", "");
        }
        if (empty($nickname)) {
            return new WP_Error(10001, "昵称不能为空", "");
        }
        if (empty($user_email)) {
            return new WP_Error(10001, "邮箱不能为空", "");
        }

        $args = array(
            'ID'              => $user_id,
            'nickname'        => $nickname,
            'display_name'    => $nickname,
            'user_email'      => $user_email,
            'description'     => $description,
        );
        
        // wp_update_user( array|object|WP_User $userdata ): int|WP_Error
        $update_res = wp_update_user($args);
        // 修改成功会直接返回用户 id
        if (is_int($update_res)) {
            $result["data"] = $update_res;
            $result["code"] = "200";
            $result["message"] = "修改成功";

            $response = new WP_REST_Response($result, 200);
            return $response;
        } else if (is_wp_error($update_res)) {
            return $update_res;
        }
    }

    private function fmt_user_info($u, $t = "") {
        // 用户角色 wp_options => wp_user_roles
        // administrator-管理员 editor-编辑 author-作者 contributor-贡献者 subscriber-订阅者
        $role_info = array(
            administrator =>  '管理员',
            editor        =>  '编辑',
            author        =>  '作者',
            contributor   =>  '贡献者',
            subscriber    =>  '订阅者',
        );
        $roleId = $u->roles[0];

        $user = array(
            "id" => $u->ID,
            "userName" => $u->data->user_login, // 用户名
            "nickname" => $u->data->display_name ?: $u->$data->user_nicename,
            "date" => $u->data->user_registered,
            "roleId" => $roleId,
            "roleName" => $role_info[$roleId],
        );

        // 列表页不返回用户邮箱
        if ($t === 'login') {
            $user['email'] = $u->data->user_email;
        }
        return $user;
    }
}