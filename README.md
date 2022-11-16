# CAFEHAUS API
兼容小程序、APP和H5的多端 API 插件，提供更加优雅的路由、入参和出参，开箱即用零依赖零设置，让前端用着更省心

## 使用插件
下载本插件代码，直接压缩，然后进入你的 WordPress 管理后台 - 安装插件 - 上传安装即可使用

## 接口特点
* 兼容小程序、APP和H5的多端 API 插件，提供更加优雅的路由、入参和出参，开箱即用零依赖零设置
* 统一接口数据返回格式：code、data、message，方便前端做统一拦截处理
* 统一分页查询数据，直接在数据中返回总条数和总页，方便前端做分页和判断是否到最后一页
* 统一接口入参、出参变量名，字段名全部统一小驼峰命名
* 去掉接口中无用的数据，官方的 REST API 接口中返回了很多无用的字段，优化数据层级，提供树形数据
* 本插件可实现的前端功能汇总：登录、注册、修改密码、修改个人资料、发表评论、文章列表、文章详情、文章查询、文章归档、轮播(取置顶文章)、分类列表、标签列表、用户列表、页面列表
* 无破坏性，接口功能未使用 Filters，全部新加路由实现，和官方的 WordPress REST API 互不影响
* WordPress REST API 官方部分的请求参数和功能根据自己平时遇到的场景并未提供，可能有遗漏的地方，欢迎大家提 issue 和 纠错

## 计划中的功能
* 按日期范围筛选文章
* 按文章形式筛选文章
* 增删改相关的操作
* 文章详情前一篇和后一篇 (2022-10-15 已完成)
* 用户登录 token 验证

## 相关项目
* [wordpress-uniapp](https://github.com/cafehaus/wordpress-uniapp)
* [wordpress-taro](https://github.com/cafehaus/wordpress-taro)
* [wordpress-flutter](https://github.com/cafehaus/wordpress-flutter)
* [wordpress-ios](https://github.com/cafehaus/wordpress-ios)
* [wordpress-android](https://github.com/cafehaus/wordpress-android)
* [wordpress-vue](https://github.com/cafehaus/wordpress-vue)
* [wordpress-react](https://github.com/cafehaus/wordpress-react)
* [wordpress-angular](https://github.com/cafehaus/wordpress-angular)
* [wordpress-electron](https://github.com/cafehaus/wordpress-electron)
* [wordpress-nuxt](https://github.com/cafehaus/wordpress-nuxt)
* [wordpress-nest](https://github.com/cafehaus/wordpress-nest)

## 加个好友
* 微信：cafe-haus
* [微博](https://weibo.com/u/3503148914)
* [知乎](https://www.zhihu.com/people/ka-fei-jiao-shi)
* [B站](https://space.bilibili.com/25400077/)

## 参考文档
[REST API Handbook](https://developer.wordpress.org/rest-api/)

## 接口文档

### 一、文章模块
#### 1、获取文章列表
* 请求地址：/wp-json/cafe/v1/posts
* 请求方式：GET
* 请求参数

| 名称         | 位置   | 类型           | 必填  | 备注 |
| ----        | ----   | ----          | ---- | ---- |
| page        | query  | number        | N    | 分页页码：默认 1    |
| size        | query  | number        | N    | 分页条数：默认 10              |
| type        | query  | string        | N    | 类型：post, page ，默认 post   |
| status      | query  | string        | N    | 状态：publish, future, draft, pending, private, trash, 默认 publish    |
| orderby     | query  | string        | N    | 排序：date, ID, title, modified, rand, comment_count, 默认 date    |
| order       | query  | string        | N    | 升降序：DESC, ASC  |
| search      | query  | string        | N    | 标题模糊匹配，可用来做搜索   |
| authorId    | query  | number或string | N    | 作者id，多个用英文逗号分隔    |
| cateId      | query  | number或string | N    | 分类id，多个用英文逗号分隔    |
| tagId       | query  | number或string | N    | 标签id，多个用英文逗号分隔    |
| sticky      | query  | boolean        | N    | 是否只查询置顶文章，默认 false，可用来查询所有置顶文章（可用来当轮播用）    |
| year        | query  | number         | N    | 4个数字年份，可用来做文章日期归档    |
| monthnum    | query  | number         | N    | 整数，1-12    |
| day         | query  | number         | N    | 整数，1-31    |
| hour        | query  | number         | N    | 整数，0-23    |
| minute      | query  | number         | N    | 整数，0-59    |
| second      | query  | number         | N    | 整数，0-59    |

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
        "img": "", // 文章特色图
        "imgs": ["https://xxx/wordpres.jpg"], // 文章内容中的所有图片地址
        "format": "standard", // 文章形式：standard, aside, chat, gallery, link, image, quote, status, video, audio
        "sticky": false, // 是否置顶，可用来加个置顶、推荐...角标
        "status": "publish", // 文章状态 publish, future, draft, pending, private
        "type": "post", // 文章类型 post, page
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
* 请求地址1：/wp-json/cafe/v1/post
* 请求地址2：/wp-json/cafe/v1/post/<:id>
* 请求方式：GET
* 请求参数

| 名称   | 位置   | 类型    | 必填  | 备注          |
| ----  | ----   | ----   | ---- | ----          |
| id    | query  | number | Y    | 请求地址1必传   |

注意：同时兼容通过 query 里的 id 传参，或者请求地址2里的再 url 路径里传参

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
    "postDate": "2022-07-01 12:12:34", // 文章发布日期
    "authorId": "123",
    "author": "作者",
    "commentCount": 2, // 评论数
    "commentStatus": "open", // 是否可以评论 open, closed
    "format": "standard", // 文章形式：standard, aside, chat, gallery, link, image, quote, status, video, audio
    "sticky": false, // 是否置顶，可用来加个置顶、推荐...角标
    "status": "publish", // 文章状态 publish, future, draft, pending, private
    "type": "post", // 文章类型 post, page
    "categories": [{ // 文章分类
      "id": 22,
      "name": "web"
    }],
    "tags": [{ // 文章标签
      "id": 44,
      "name": "wordpress"
    }],
    "previousPost": { // 前一篇文章
      "id": 12,
      "title": "我是前一篇文章的标题",
      "img": "https://xxx/logo.png", // 文章特色图，未设置会自动获取内容中解析出的第一张图片
    },
    "nextPost": { // 后一篇文章
      "id": 14,
      "title": "我是后一篇文章的标题",
      "img": "https://xxx/logo.png",
    },
  }
}
```

### 二、分类模块
#### 1、获取分类列表
* 请求地址：/wp-json/cafe/v1/categories
* 请求方式：GET
* 请求参数

| 名称        | 位置   | 类型    | 必填  | 备注               |
| ----       | ----   | ----   | ---- | ----               |
| hideEmpty  | query  | boolean| N    | 是否隐藏空内容分类    |
| tree       | query  | boolean| N    | 是否格式化成树形结构返回，默认 true    |

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
      "children": [{
         "id": 2,
        "name": "我是二级分类",
        "description": "我是二级分类描述",
        "count": 3,
        "parent": "1",
      }]
    }
  ]
}
```

### 三、标签模块
#### 1、获取标签列表
* 请求地址：/wp-json/cafe/v1/tags
* 请求方式：GET
* 请求参数

| 名称         | 位置   | 类型    | 必填   | 备注 |
| ----        | ----   | ----   | ----  | ---- |
| page        | query  | number | N     | 分页页码，默认 1    |
| size        | query  | number | N     | 分页条数，默认 10   |
| hideEmpty   | query  | boolean| N     | 是否隐藏没有文章的标签，默认 false    |
| orderby     | query  | string | N     | 排序：name, slug, count，默认 name    |
| order       | query  | string | N     | 升降序：DESC, ASC，默认 ASC |

* 返回结果
```json
{
  "code": "200",
  "message": "请求成功",
  "data": {
    "list": [
      {
        "id": 1, // 标签 id
        "name": "我是标签名",
        "slug": "我是标签别名",
        "description": "我是标签描述",
        "count": 2, // 标签下的文章数
      }
    ],
    "total": 1,
    "totalPages": 1,
    "page": 1,
    "size": 10,
  }
}
```

### 四、评论模块
#### 1、获取评论列表
* 请求地址：/wp-json/cafe/v1/comments
* 请求方式：GET
* 请求参数

| 名称   | 位置   | 类型    | 必填   | 备注 |
| ----  | ----   | ----   | ----  | ---- |
| page  | query  | number | N     | 分页页码，默认 1    |
| size  | query  | number | N     | 分页条数，默认 10   |

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

#### 2、获取文章的评论
* 请求地址：/wp-json/cafe/v1/post/comments
* 请求方式：GET
* 请求参数

| 名称     | 位置   | 类型    | 必填   | 备注 |
| ----    | ----   | ----   | ----  | ---- |
| postId  | query  | number | Y     | 文章id    |
| tree    | query  | boolean| N     | 是否格式化成树形结构返回，默认 true    |

* 返回结果
```json
{
  "code": "200",
  "message": "请求成功",
  "data": [{
    "id": 1,
    "content": "我是评论内容",
    "date": "2022-07-01 12:12:34", // 评论日期
    "author": "周小黑", // 评论人
    "postId": "1", // 评论所属文章 id
    "parentId": "334", // 父级评论 id
    "children": [{
      "id": 2,
      "content": "我是二级评论内容",
      "date": "2022-07-02 10:12:30",
      "author": "周小白",
      "postId": "1",
      "parentId": "1",
    }]
  }]
}
```

#### 3、发表文章评论
* 请求地址：/wp-json/cafe/v1/comment/add
* 请求方式：POST
* 请求参数

| 名称              | 位置   | 类型    | 必填   | 备注 |
| ----             | ----   | ----   | ----  | ---- |
| commentContent   | query  | string | Y     | 评论内容，最长200个字符    |
| postId           | query  | number | Y     | 文章id    |
| commentParentId  | query  | number | N     | 评论父级id，一般回复某条评论    |
| commentAuthorId  | query  | number | Y     | 评论人id    |
| commentAuthor    | query  | string | Y     | 评论人用户名    |

* 返回结果
```json
{
  "code": "200",
  "message": "评论成功，审核通过后将会显示",
  "data": 5 // 评论成功会返回当前评论的id
}
```

### 五、用户模块
#### 1、获取用户列表
* 请求地址：/wp-json/cafe/v1/users
* 请求方式：GET
* 请求参数

| 名称   | 位置   | 类型    | 必填   | 备注 |
| ----  | ----   | ----   | ----  | ---- |
| page  | query  | number | N     | 分页页码，默认 1    |
| size  | query  | number | N     | 分页条数，默认 10   |

* 返回结果
```json
{
  "code": "200",
  "message": "请求成功",
  "data": {
    "list": [
      {
        "id": 1,
        "userName": "我是用户账号登录名，唯一的，不能改的",
        "nickname": "我是用户昵称，可以随便改的",
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

#### 2、用户注册
* 请求地址：/wp-json/cafe/v1/register
* 请求方式：POST
* 请求参数

| 名称       | 位置   | 类型    | 必填   | 备注 |
| ----      | ----   | ----   | ----  | ---- |
| userName  | query  | string | Y     | 用户名，只能数字、英文和下划线 |
| password  | query  | string | Y     | 密码，只能数字、英文和下划线   |

注意：用户名和密码 wordpress 控制的最长60个字符，具体长度本接口并未做校验，建议自己根据需求在前端控制，此用户名实际上是账号登录名，所以必须数字、字母和下划线，而且要求唯一，wordpress 有额外提供另外的展示名和昵称来给到前端展示，返回的用户信息里有返回 niceName 会先取展示名没有会再去取昵称

* 返回结果
```json
{
  "code": "200",
  "message": "注册成功",
  "data": 2 // 注册成功会返回用户 id
}
```

#### 3、用户登录
* 请求地址：/wp-json/cafe/v1/login
* 请求方式：POST
* 请求参数

| 名称       | 位置   | 类型    | 必填   | 备注 |
| ----      | ----   | ----   | ----  | ---- |
| userName  | query  | string | Y     | ---- |
| password  | query  | string | Y     | ---- |

注意：此登录并未做任何安全验证，如果网站有任何敏感操作不建议使用。如果站点只是简单的文章阅读、发表评论这些，可以用此实现通用的简单用户登录注册功能，至于担心垃圾评论，发表评论的接口已经做了限制，所有评论必须在后台通过后才会展示。

* 返回结果
```json
{
  "code": "200",
  "message": "登录成功",
  "data": { // 登录成功会返回对应的用户信息，失败则返回相应的错误提示
    "id": 1,
    "userName": "我是用户账号登录名，唯一的，不能改的",
    "nickname": "我是用户昵称，可以随便改的",
    "date": "2022-07-01 12:12:34",
    "email": "510878689@qq.com",
    "roleId": "administrator",
    "roleName": "管理员",
  }
}
```

#### 4、修改密码
* 请求地址：/wp-json/cafe/v1/user/password/update
* 请求方式：POST
* 请求参数

| 名称       | 位置   | 类型    | 必填   | 备注 |
| ----      | ----   | ----   | ----  | ---- |
| userId    | query  | string | Y     | 用户id |
| password  | query  | string | Y     | 新密码 |


* 返回结果
```json
{
  "code": "200",
  "message": "修改成功",
  "data": 3 // 修改成功会返回对应的用户id
}
```

#### 5、修改用户信息
* 请求地址：/wp-json/cafe/v1/user/info/update
* 请求方式：POST
* 请求参数

| 名称         | 位置   | 类型    | 必填   | 备注 |
| ----        | ----   | ----   | ----  | ---- |
| userId      | query  | number | Y     | 用户id |
| nickname    | query  | string | Y     | 用户昵称 |
| email       | query  | string | Y     | 用户邮箱，要求唯一 |
| description | query  | string | N     | 个人说明 |


* 返回结果
```json
{
  "code": "200",
  "message": "修改成功",
  "data": 3 // 修改成功会返回对应的用户id
}
```