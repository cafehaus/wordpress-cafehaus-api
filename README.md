# REST API WP CAFE
支持多端前后端分离开发的 REST API 接口

## 参考文档
[REST API Handbook](https://developer.wordpress.org/rest-api/)

## 接口文档

### 一、文章模块
#### 1、获取文章列表
* 请求地址：/wp-json/cafe/v1/articles
* 请求方式：GET
* 请求参数

| 名称  | 位置   | 类型   | 必填 | 备注 |
| ----  | ----   | ----   | ---- | ---- |
| page  | query  | number | N    | -    |
| size  | query  | number | N    | -    |

* 返回结果
```json
{
  "code": "200",
  "success": true,
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "title": "我是文章标题",
        "postDate": "2022-07-01 12:12:34",
        "commentCount": 3,
        "img": "",
      }
    ],
    "total": 1,
    "totalPages": 1,
    "page": 1,
    "size": 10,
  }
}
```

#### 2、获取文章详情
* 请求地址：/wp-json/cafe/v1/article/<:id>
* 请求方式：GET
* 请求参数

| 名称  | 位置   | 类型   | 必填 | 备注 |
| ----  | ----   | ----   | ---- | ---- |
| id    | query  | number | Y    | -    |

* 返回结果
```json
{
  "code": "200",
  "success": true,
  "msg": "请求成功",
  "data": {
    "id": 1,
    "title": "我是文章标题",
    "postDate": "2022-07-01 12:12:34",
    "commentCount": 3,
    "img": "",
  }
}
```