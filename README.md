# REST API WP CAFE
支持多端前后端分离开发的 REST API 接口

## 参考文档
[REST API Handbook](https://developer.wordpress.org/rest-api/)

## 接口文档

### 一、文章模块
#### 1、获取文章
* 请求地址：/wp-json/cafe/v1/articles
* 请求方式：GET
* 请求参数

| 名称  | 类型   | 必填 | 备注 |
| ----  | ----   | ---- | ---- |
| page  | number | N    | -    |
| size  | number | N    | -    |

* 返回结果
```json
{
  "code": "200",
  "success": true,
  "msg": "请求成功",
  "data": {
    "list": [],
    "total": 0,
    "totalPages": 0,
    "page": 1,
    "size": 10,
  }
}
```