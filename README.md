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
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "title": "我是文章标题",
        "postDate": "2022-07-01 12:12:34",
        "commentCount": 3, // 评论数
        "img": "", // 特色图
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
  "msg": "请求成功",
  "data": {
    "id": 1,
    "title": "我是文章标题",
    "content": "文章正文内容富文本",
    "excerpt": "文章简介",
    "postDate": "2022-07-01 12:12:34",
    "authorId": "123",
    "author": "作者",
    "categories": [{ // 文章分类
      "id": "22",
      "name": "web"
    }],
    "tags": [{ // 文章标签
      "id": "44",
      "name": "wordpress"
    }],
  }
}
```

### 二、分类模块
#### 1、获取分类列表
* 请求地址：/wp-json/cafe/v1/categories
* 请求方式：GET
* 请求参数

| 名称       | 位置   | 类型   | 必填 | 备注                  |
| ----       | ----   | ----   | ---- | ----                  |
| hideEmpty  | query  | boolean| N    | 是否隐藏空内容分类    |

* 返回结果
```json
{
  "code": "200",
  "msg": "请求成功",
  "data": [
    {
      "id": 1,
      "name": "我是分类名",
      "description": "我是分类描述",
      "count": 3, // 分类下的文章数
      "parent": "12", // 父级id为0时，实际上是无父级
    }
  ]
}
```

### 三、评论模块
#### 1、获取评论列表
* 请求地址：/wp-json/cafe/v1/comments
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
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "content": "我是评论内容",
        "date": "2022-07-01 12:12:34", // 评论日期
        "author": "周小黑", // 评论人
        "postId": "1", // 评论所属文章 id
        "parentId": "334", // 父级评论 id
      }
    ],
    "total": 1,
    "totalPages": 1,
    "page": 1,
    "size": 10,
  }
}
```

### 四、用户模块
#### 1、获取用户列表
* 请求地址：/wp-json/cafe/v1/users
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
  "msg": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "name": "我是用户名",
        "date": "2022-07-01 12:12:34", // 注册时期
        "roleId": "administrator",
        "roleName": "管理员", // administrator-管理员 editor-编辑 author-作者 contributor-贡献者 subscriber-订阅者
      }
    ],
    "total": 1,
    "totalPages": 1,
    "page": 1,
    "size": 10,
  }
}
```