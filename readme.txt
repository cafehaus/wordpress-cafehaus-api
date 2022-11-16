=== CAFEHAUS API ===
Contributors: cafehaus
Tags: api, restful, json, app
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==

兼容小程序、APP和H5的多端 API 插件，提供更加优雅的路由、入参和出参，开箱即用零依赖零设置，让前端用着更省心

接口特点:
* 兼容小程序、APP和H5的多端 API 插件，提供更加优雅的路由、入参和出参，开箱即用零依赖零设置
* 统一接口数据返回格式：code、data、message，方便前端做统一拦截处理
* 统一分页查询数据，直接在数据中返回总条数和总页，方便前端做分页和判断是否到最后一页
* 统一接口入参、出参变量名，字段名全部统一小驼峰命名
* 去掉接口中无用的数据，官方的 REST API 接口中返回了很多无用的字段，优化数据层级，提供树形数据
* 本插件可实现的前端功能汇总：登录、注册、修改密码、修改个人资料、发表评论、文章列表、文章详情、文章查询、文章归档、轮播(取置顶文章)、分类列表、标签列表、用户列表、页面列表
* 无破坏性，接口功能未使用 Filters，全部新加路由实现，和官方的 WordPress REST API 互不影响
* WordPress REST API 官方部分的请求参数和功能根据自己平时遇到的场景并未提供，可能有遗漏的地方，欢迎大家提 issue 和 纠错