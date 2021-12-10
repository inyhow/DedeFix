<?php
/**
 * @version        $Id: tags.php 1 2010-06-30 11:43:09Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once (dirname(__FILE__) . "/include/common.inc.php");
require_once (DEDEINC . "/arc.taglist.class.php");

// 2019/02/13 用于百度中文搜索引擎中使用，将/tags.php?/关键词/ 调整为/tags/id.html形式，便于页面收录和统计 ，针对百度，讲此文件改为tags.php 

//$PageNo = 1;//调整为url为tag.php?/id形式
/* 
if(isset($_SERVER['QUERY_STRING']))
{
    $tag = trim($_SERVER['QUERY_STRING']);
    $tags = explode('/', $tag);
    if(isset($tags[1])) $tag = $tags[1];
    if(isset($tags[2])) $PageNo = intval($tags[2]);
}
else
{
    $tag = '';
}

$tag = FilterSearch(urldecode(strtolower($tag))); //修复字符串大写出现找不到文章的bug
if($tag != addslashes($tag)) $tag = '';
if($tag == '') $dlist = new TagList($tag, 'tag.htm');
else $dlist = new TagList($tag, 'taglist.htm');
$dlist->Display();
exit(); */
//201900213 add
$tagid = (isset($tagid) && is_numeric($tagid)) ? $tagid : 0;
$PageNo = (isset($PageNo) && is_numeric($PageNo)) ? $PageNo : 1;
if ($tagid =="0") {
    $dlist = new TagList($tag, 'tag.htm');
    $dlist->Display();
}

else{
	$row = $dsql->GetOne("SELECT tag FROM `#@__tagindex` WHERE id ={$tagid}");
    
	if (!is_array($row)) {
	    ShowMsg('系统无此tag', '-1');
		//后期这边必须返回404
	    exit();
	}
	$tag=$row['tag'];
	$tag = FilterSearch(urldecode(strtolower($tag))); //修复字符串大写出现找不到文章的bug
	$dlist = new TagList($tag, 'taglist.htm');
	$dlist->Display();
}
exit();

