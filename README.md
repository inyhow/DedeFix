#基于dedecms v5.7 最新版本相应的功能开发，SEO优化

> 安装好后，请把data/dede.sql文件导入mysql库

 
* 2015/05/15 修正dede/catalog_edit.php`顶级目录`编辑后，会导致`下级栏目`隐藏栏目自动恢复`未隐藏`状态
* 2015/05/15 include/dedetag.class.php SaveTo调整为第二页开始title加入页码 +canonical标签 模板中遇 此类占位关键词###link_canonical###会自动替换 
* 2016/01/15 include/extend.func.php 增加GetTypeTag  pasterTempletDiy getTagStyle replaceAmpImages artMeta id2Url GetPinyin函数
* 2018/04/25 include/inc/inc_fun_funAdmin.php ` SpGetNewInfo()`注释获取更新信息的代码
* 2018/06/07 给专题页面增加了伪静态插件，`系统配置-核心设置-开启伪静态`专题详情页面url改为/topic/{aid}.html 修改于文件/include/helpers/channelunit.helper.php   增加一段代码 //start `文章页面完美伪静态化`  (列表伪静态化页面还有些bug)
* 2018/06/11 /include/arc.archives.class.php中function _highlight函数修复关键词替换的bug
* 2018/06/20 后台增加一个全站伪静态规则生成的插件.htacess.php，增加生成apache和nginx伪静态生成规则(iis可使用apache伪静态文件)

* 2018/8/23  inlude/helper/extend.helper.php 增加RturnFor函数 解决view.php文章和list.php列表页面不存在时，返回错误软 非404状态码问题

* 2021/4/13  dede/article.php 发布文章随机赋予英文昵称
* 2021/4/15  dede/article_add.php 增加检测文档是否重复 关键参数$cfg_mb_cktitle
* 2021/4/29 精简删除百度新闻、文件管理器、德得广告模块、挑错管理、畅言评论、广告管理、百度站内搜索、投票 、百度结构化数据等模块。
* 2021/4/29 新增/dede/htaccess.php 伪静态规则生成文件
* 2021/4/29 /dede/article_add.php 中 $filename为空值 自动将title处理转为$filename,解决DedeCMS自定义文件名可能重复问题
* 2021/5/6 /dede/index_body.php 修复dedeseo_http_send 无法采集https资源 收录数的问题
* 2021/5/6 /include/inc/inc_funString.php中的SpHtml2Text 修复过滤html过滤不完全导致unicode编码无法正确转换为中文的bug
* 2021/5/11 后台增加自动SEO频道功能，只需批量导入关键词，就能生成大量seo频道页面 改动/dede/index_menu.php /dede/templets/index_menu2.htm


* 2021/05/11 新增`字母列表序列A-Z的文章SEO页面索引`功能+`city页面，用以索引会员资料页面 改进中(此节代码未传)
* 2021/06/01 后台发布文章模块新增 删除tags和keywords中停止词的函数 改动/include/data include/extend.func.php  clean_tags()

* 删除m member special plus/guestbook 删除系统菜单 帮助模块
* 增加一个ccie认证企业展示模板
* 根目录增加一个tags_cn.php 具体针对中文搜索引擎

## nginx配置https支持HSTS
### 1.证书必须泛域名证书，http需要301重定向到https域; 
### 2.add_header "Strict-Transport-Security: max-age=63072000; includeSubDomains; preload")

##Bug

* 目前发现的dedecms伪静态后，分页无法使用bug，需要改几处文件，暂时先记在这里

