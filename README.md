# WORDPRESS CAFE API
支持多端前后端分离开发的一套 REST API 接口，苦于 wordpress 官方提供的 REST API 接口数据格式前端比较难用，所以自己开发了一套

## 接口特点
* 统一接口数据返回格式：code、data、message，方便前端做统一拦截处理
* 统一分页查询数据，直接在数据中返回总条数和总页，方便前端做分页和判断是否到最后一页
* 统一接口入参、出参变量名，字段名全部统一小驼峰命名
* 去掉接口中无用的数据，官方的 REST API 接口中返回了很多无用的字段，部分字段前端根本用不上

## 相关项目
[wordpress-cafe-api](https://github.com/cafehaus/wordpress-cafe-api)
[wordpress-uniapp](https://github.com/cafehaus/wordpress-uniapp)
[wordpress-taro](https://github.com/cafehaus/wordpress-taro)
[wordpress-flutter](https://github.com/cafehaus/wordpress-flutter)
[wordpress-vue](https://github.com/cafehaus/wordpress-vue)
[wordpress-react](https://github.com/cafehaus/wordpress-react)
[wordpress-angular](https://github.com/cafehaus/wordpress-angular)
[wordpress-electron](https://github.com/cafehaus/wordpress-electron)

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
  "message": "请求成功",
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
  "message": "请求成功",
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
  "message": "请求成功",
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
  "message": "请求成功",
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
  "message": "请求成功",
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