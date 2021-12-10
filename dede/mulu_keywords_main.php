<?php
/**
 * 文档关键词管理
 *
 * @version        $Id: mulu_keywords_main.php 1 14:12 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 * author 　　　　　 Abner
 * Update time      2017-01-07

 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Keyword');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC.'/customfields.func.php');
require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
if(empty($dopost)) $dopost = '';



//保存批量更改
if($dopost=='saveall')
{
    $ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "mulu_keywords_main.php" : $_COOKIE['ENV_GOBACK_URL'];
    if(!isset($aids))
    {
        ShowMsg("你没有选择要更改的内容！",$ENV_GOBACK_URL);
        exit();
    }
    foreach($aids as $aid)
    {
        $rpurl = ${'rpurl_'.$aid};
        $rpurlold = ${'rpurlold_'.$aid};
        $keyword = ${'keyword_'.$aid};

        //删除项目
        if(!empty(${'isdel_'.$aid}))
        {
            $dsql->ExecuteNoneQuery("DELETE FROM `#@__keywords_mulu` WHERE aid='$aid'");
            continue;
        }

        //禁用项目
        $staold = ${'staold_'.$aid};
        $sta = empty(${'isnouse_'.$aid}) ? 1 : 0;
        if($staold!=$sta)
        {
            $query1 = "UPDATE `#@__keywords_mulu` SET sta='$sta',rpurl='$rpurl' WHERE aid='$aid' ";
            $dsql->ExecuteNoneQuery($query1);
            continue;
        }

        //更新链接网址
        if($rpurl!=$rpurlold)
        {
            $query1 = "UPDATE `#@__keywords_mulu` SET rpurl='$rpurl' WHERE aid='$aid' ";
            $dsql->ExecuteNoneQuery($query1);
        }
    }
    ShowMsg("完成指定的更改！",$ENV_GOBACK_URL);
    exit();
}
//增加关键字
else if($dopost=='add')
{
    $ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "-1" : $_COOKIE['ENV_GOBACK_URL'];
    $keyword = trim($keyword);
    $rank = preg_replace("#[^0-9]#", '', $rank);
    if($keyword=='')
    {
        ShowMsg("关键字不能为空！",-1);
        exit();
    }
    $row = $dsql->GetOne("SELECT * FROM `#@__keywords_mulu` WHERE keyword LIKE '$keyword'");
    if(is_array($row))
    {
        ShowMsg("关键字已存在库中！","-1");
        exit();
    }
    $inquery = "INSERT INTO `#@__keywords_mulu`(keyword,rank,sta,rpurl) VALUES ('$keyword','$rank','1','$rpurl');";
    $dsql->ExecuteNoneQuery($inquery);
    ShowMsg("成功增加一个关键字！",$ENV_GOBACK_URL);
    exit();
}
//文件导入式 批量添加关键词
elseif ( $dopost == 'fileKeywords') {

    if ( is_uploaded_file( $filename ) ) 
    {
        $newFileName    = date('YmdHis', time() ) .'.txt';
        $newDirPath     = $cfg_basedir . '/uploads/keywords';
        $newFilePath    =  $newDirPath .'/'.$newFileName;

        
        //目录不存在则创建
        if ( !file_exists( $newDirPath ) ) 
        {   
            mkdir( $newDirPath, 0777, true );
        }

        move_uploaded_file( $filename, $newFilePath);

        //读取上传的文件
        $handle = fopen( $newFilePath, 'r' ) or ShowMsg("上传文件读取失败",-1); 

        $keyArr = array();

        if ($handle) 
        {
            while (!feof($handle)) 
            {
                $buffer = fgets($handle, 4096);
                $buffer = str_replace('，', ',', $buffer);
                $buffer = str_replace(array("\n"), '', $buffer);
                $buffer = trim($buffer);
                if(!empty($buffer))
                {
                    $keyArr[] = explode(',', $buffer);
                }
            }
     
            fclose($handle);
            @unlink( $newFilePath );
        }



        //取出数据库所有的关键词,和新添加关键词比较，如果新添加的关键字，数据中存在则过滤
        $dsql->SetQuery( "SELECT `keyword` FROM `#@__keywords_mulu` " );//将SQL查询语句格式化
        $dsql->Execute();//执行SQL操作
        //通过循环输出执行查询中的结果
        $dataKeyArr = array();
        while( $row = $dsql->GetArray() )
        {
            $dataKeyArr[] = $row['keyword'];
        }
        //拼接mysql 语句
        $i = 0;
        foreach ( $keyArr as $k => $v ) 
        {
            $key = trim( $v[0] );
			
		
            //判断数据是否为utf8的。如果不是转utf-8编码
		      
		   if(!is_utf8($key)) {$key =  mb_convert_encoding($key, 'utf-8', 'gbk');}  
      
		  
			
            //如果数据库中存在该关键词，则跳出本次循环。 注意： keyword字段在数据库中长度16字符，需要更更长点
            if( in_array( $key, $dataKeyArr ) )
                continue;
            //拼接mysql语句
	
			
            $rpurl  =   trim($v[1], ' ');
			
	      //修复导入失败的问题,并转化为拼音首字母的规则 用来后期生成泛目录url结构
	  
			$rpurl  =   GetPinyin(mb_convert_encoding($rpurl, 'utf-8', 'gbk'),1);
		
          //比如好时尚,则转为hss
				
	
          // $rank   =   trim($v[2], ' ');
			 $rank   =   1;
            $sql = "INSERT INTO `#@__keywords_mulu`  ( keyword, rank, sta, rpurl ) VALUES  ( '{$key}', {$rank}, 1, '{$rpurl}')";
		
            $res = $dsql->ExecuteNoneQuery2($sql);
            if( $res != -1)
                $i ++;
        }
	
		
        //如果导入的文件所有的关键词都添加过了，则停止执行；
        if( empty( $i ) )
        {   
            ShowMsg("警告：你提交的关键词，早已经添加过了！！！",-1); 
            die();
        }
        else
        {
            ShowMsg("你成功导入{$i}条数据",-1); 
        }

    }
    
}

//清空关键字表里的所有数据 
else if ( $dopost == 'delAll')
{
   $dsql->ExecuteNoneQuery('TRUNCATE TABLE `#@__keywords_mulu`') ? ShowMsg("你已成功清除所有的关键词！！", $ENV_GOBACK_URL ) : ShowMsg("清除关键词失败",-1) ;

}

if(empty($keyword))
{
    $keyword = '';
    $addquery = '';
}
else
{
    $addquery = " WHERE keyword LIKE '%$keyword%' ";
}

$sql = "SELECT * FROM `#@__keywords_mulu` $addquery ORDER BY rank DESC";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetTemplate(DEDEADMIN."/templets/mulu_keywords_main.htm");
$dlist->SetSource($sql);
$dlist->Display();

function GetSta($sta)
{
    if($sta==1) return '';
    else return ' checked="1" ';
}
function is_utf8($str)  
{  
    $len = strlen($str);  
    for($i = 0; $i < $len; $i++){  
        $c = ord($str[$i]);  
        if ($c > 128) {  
            if (($c > 247)) return false;  
            elseif ($c > 239) $bytes = 4;  
            elseif ($c > 223) $bytes = 3;  
            elseif ($c > 191) $bytes = 2;  
            else return false;  
            if (($i + $bytes) > $len) return false;  
            while ($bytes > 1) {  
                $i++;  
                $b = ord($str[$i]);  
                if ($b < 128 || $b > 191) return false;  
                $bytes--;  
            }  
        }  
    }  
    return true;  
} // end of check_utf8  