<?php
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;

class JWTAuth {
    // 盐值
    protected $key = 'example_key';

    /**
     * 生成 token
     * @param int $id 登录的用户id
     * @param int $username 登录名
     */
    public static function getToken(int $id, string $username){
        $payload = array(
            "iat" => time(), //签发时间
            'exp' => time()+10800, // 过期时间，三个小时
            'data'=> ['name'=>$username,'uid'=>$id] // 携带数据
        );
        $token = JWT::encode($payload, self::$key, 'HS256');
        return $token;
    }

    /**
     * 校验 token
     * @param string $token
     * @return array
     */
    public static function checkToken(string $token){
        //校验token
        try {
            JWT::$leeway = 60; // 当前时间减去60，把时间留点余地
            $data = (array)JWT::decode($token, new Key(self::$key, 'HS256'));
            $result['data'] = $data['data'];
            $result['message'] = 'token 验证成功';
            $result['code'] = 10000;
            return $result;
        } catch (SignatureInvalidException $exception) { // 验证失败
            $result['code'] = 10001;
            $result['message']   = 'token 无效，请重新登录！';
            return $result;
        } catch (ExpiredException $exception){
            $result['code'] = 10002;
            $result['message']   = '登录已过期，请重新登录！';
            return $result;
        } catch (\Exception $exception){
            $result['code'] = 10003;
            $result['message']   = '未知错误，请重新登录！';
            return $result;
        }
    }
}