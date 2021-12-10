<?php
/**
 * 管理后台首页主体
 *
 * @version        $Id: index_body.php 1 11:06 2010年7月13日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require(dirname(__FILE__).'/config.php');
require(DEDEINC.'/image.func.php');
require(DEDEINC.'/dedetag.class.php');
$defaultIcoFile = DEDEDATA.'/admin/quickmenu.txt';
$myIcoFile = DEDEDATA.'/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
if(!file_exists($myIcoFile)) $myIcoFile = $defaultIcoFile;

//默认主页
if(empty($dopost))
{
    require(DEDEINC.'/inc/inc_fun_funAdmin.php');
    $verLockFile = DEDEDATA.'/admin/ver.txt';
    $fp = fopen($verLockFile,'r');
    $upTime = trim(fread($fp,64));
    fclose($fp);
    $oktime = substr($upTime,0,4).'-'.substr($upTime,4,2).'-'.substr($upTime,6,2);
    $offUrl = SpGetNewInfo();
    $dedecmsidc = DEDEDATA.'/admin/idc.txt';
    $fp = fopen($dedecmsidc,'r');
    $dedeIDC = fread($fp,filesize($dedecmsidc));
    fclose($fp);
    $myMoveFile = DEDEDATA.'/admin/move-'.$cuserLogin->getUserID().'.txt';
    if(file_exists($myMoveFile))
    {
        $fp = fopen($myMoveFile,'r');
        $movedata= fread($fp,filesize($myMoveFile));
        $movedata = unserialize($movedata);
        $column1 = array();
        $column2 = array();
        foreach ($movedata['items'] as $key => $value) {
            if($value['column'] == 'column1') $column1 = $column1 + array($key => $value['id']);
            else if($value['column'] == 'column2') $column2 = $column2 + array($key => $value['id']);
        }
        include DedeInclude('templets/index_body_move.htm');
    }else{  
        include DedeInclude('templets/index_body.htm');
    }
    exit();
}
/*-----------------------
增加新项
function _AddNew() {   }
-------------------------*/
else if($dopost=='addnew')
{
    if(empty($link) || empty($title))
    {
        ShowMsg("链接网址或标题不能为空！","-1");
        exit();
    }

    $fp = fopen($myIcoFile,'r');
    $oldct = trim(fread($fp, filesize($myIcoFile)));
    fclose($fp);

    $link = preg_replace("#['\"]#", '`', $link);
    $title = preg_replace("#['\"]#", '`', $title);
    $ico = preg_replace("#['\"]#", '`', $ico);
    $oldct .= "\r\n<menu:item ico=\"{$ico}\" link=\"{$link}\" title=\"{$title}\" />";

    $myIcoFileTrue = DEDEDATA.'/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
    $fp = fopen($myIcoFileTrue, 'w');
    fwrite($fp, $oldct);
    fclose($fp);

    ShowMsg("成功增加一个项目！","index_body.php?".time());
    exit();
}
/*---------------------------
保存修改的项
function _EditSave() {   }
----------------------------*/
else if($dopost=='editsave')
{
    $quickmenu = stripslashes($quickmenu);

    $myIcoFileTrue = DEDEDATA.'/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
    $fp = fopen($myIcoFileTrue,'w');
    fwrite($fp,$quickmenu);
    fclose($fp);

    ShowMsg("成功修改快捷操作项目！","index_body.php?".time());
    exit();
}
/*---------------------------
保存修改的项
function _EditSave() {   }
----------------------------*/
else if($dopost=='movesave')
{   
    $movedata = str_replace('\\',"",$sortorder);
    $movedata = json_decode($movedata,TRUE);
    $movedata = serialize($movedata);
    $myIcoFileTrue = DEDEDATA.'/admin/move-'.$cuserLogin->getUserID().'.txt';
    $fp = fopen($myIcoFileTrue,'w');
    fwrite($fp,$movedata);
    fclose($fp);
}
/*-----------------------------
显示修改表单
function _EditShow() {   }
-----------------------------*/
else if($dopost=='editshow')
{
    $fp = fopen($myIcoFile,'r');
    $oldct = trim(fread($fp,filesize($myIcoFile)));
    fclose($fp);
?>
<form name='editform' action='index_body.php' method='post'>
<input type='hidden' name='dopost' value='editsave' />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
     <td height='28' background="images/tbg.gif">
         <div style='float:left'><b>修改快捷操作项</b></div>
      <div style='float:right;padding:3px 10px 0 0;'>
             <a href="javascript:CloseTab('editTab')"><img src="images/close.gif" width="12" height="12" border="0" /></a>
      </div>
     </td>
   </tr>
      <tr><td style="height:6px;font-size:1px;border-top:1px solid #8DA659">&nbsp;</td></tr>
   <tr>
     <td>
         按原格式修改/增加XML项。
     </td>
   </tr>
   <tr>
     <td align='center'>
         <textarea name="quickmenu" rows="10" cols="50" style="width:94%;height:220px"><?php echo $oldct; ?></textarea>
     </td>
   </tr>
   <tr>
     <td height="45" align="center">
         <input type="submit" name="Submit" value="保存项目" class="np coolbg" style="width:80px;cursor:pointer" />
         &nbsp;
         <input type="reset" name="reset" value="重设" class="np coolbg" style="width:50px;cursor:pointer" />
     </td>
   </tr>
  </table>
</form>
<?php
exit();
}
/*---------------------------------
载入右边内容
function _getRightSide() {   }
---------------------------------*/
else if($dopost=='getRightSide')
{
    $query = " SELECT COUNT(*) AS dd FROM `#@__member` ";
    $row1 = $dsql->GetOne($query);
    $query = " SELECT COUNT(*) AS dd FROM `#@__feedback` ";
    $row2 = $dsql->GetOne($query);
    
    $chArrNames = array();
    $query = "SELECT id, typename FROM `#@__channeltype` ";
    $dsql->Execute('c', $query);
    while($row = $dsql->GetArray('c'))
    {
        $chArrNames[$row['id']] = $row['typename'];
    }
    
    $query = "SELECT COUNT(channel) AS dd, channel FROM `#@__arctiny` GROUP BY channel ";
    $allArc = 0;
    $chArr = array();
    $dsql->Execute('a', $query);
    while($row = $dsql->GetArray('a'))
    {
        $allArc += $row['dd'];
        $row['typename'] = $chArrNames[$row['channel']];
        $chArr[] = $row;
    }
?>
    <table width="100%" class="dboxtable">
    <tr>
        <td width='50%' class='nline'  style="text-align:left"> 会员数： </td>
        <td class='nline' style="text-align:left"> <?php echo $row1['dd']; ?> </td>
    </tr>
    <tr>
        <td class='nline' style="text-align:left"> 文档数： </td>
        <td class='nline' style="text-align:left"> <?php echo $allArc; ?> </td>
    </tr>
    <?php
    foreach($chArr as $row)
    {
    ?>
    <tr>
        <td class='nline' style="text-align:left"> <?php echo $row['typename']; ?>： </td>
        <td class='nline' style="text-align:left"> <?php echo $row['dd']; ?>&nbsp; </td>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td style="text-align:left"> 评论数： </td>
        <td style="text-align:left"> <?php echo $row2['dd']; ?> </td>
    </tr>
    </table>
<?php
exit();
} else if ($dopost=='getRightSideNews')
{
    $query = "SELECT arc.id, arc.arcrank, arc.title, arc.channel, ch.editcon  FROM `#@__archives` arc
            LEFT JOIN `#@__channeltype` ch ON ch.id = arc.channel
             WHERE arc.arcrank<>-2 ORDER BY arc.id DESC LIMIT 0, 6 ";
    $arcArr = array();
    $dsql->Execute('m', $query);
    while($row = $dsql->GetArray('m'))
    {
        $arcArr[] = $row;
    }
    AjaxHead();
?>
    <table width="100%" class="dboxtable">
    <?php
    foreach($arcArr as $row)
    {
        if(trim($row['editcon'])=='') {
            $row['editcon'] = 'archives_edit.php';
        }
        $linkstr = "·<a href='{$row['editcon']}?aid={$row['id']}&channelid={$row['channel']}'>{$row['title']}</a>";
        if($row['arcrank']==-1) $linkstr .= "<font color='red'>(未审核)</font>";
    ?>
    <tr>
        <td class='nline'>
            <?php echo $linkstr; ?>
        </td>
    </tr>
    <?php
    }
    ?>
    </table>
<?php
exit;
} else if ($dopost=='showauth')
{
    include('templets/index_body_showauth.htm');
    exit;
} else if ($dopost=='showad')
{
    include('templets/index_body_showad.htm');
    exit;
} else if($dopost=='setskin')
{
	$cskin = empty($cskin)? 1 : $cskin;
	$skin = !in_array($cskin, array(1,2,3,4))? 1 : $cskin;
	$skinconfig = DEDEDATA.'/admin/skin.txt';
	PutFile($skinconfig, $skin);
} elseif ( $dopost=='get_seo' )
{
    if (!function_exists('fsocketopen') && !function_exists('curl_init')) {
        echo '没有支持的curl或fsocketopen组件';
        exit;
    }
    function dedeseo_http_send($url, $limit=0, $post='', $cookie='', $timeout=5)
    {
        $return = '';
        $matches = parse_url($url);
        $scheme = $matches['scheme'];
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].(@$matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;

        if (function_exists('curl_init') && function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $scheme.'://'.$host.':'.$port.$path);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, @$_SERVER['HTTP_USER_AGENT']); 
            if ($post) {
                curl_setopt($ch, CURLOPT_POST, 1);
                $content = is_array($port) ? http_build_query($post) : $post;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            }
            if ($cookie) {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            if ($errno || $status['http_code'] != 200) {
                return;
            } else {
                return !$limit ? $data : substr($data, 0, $limit);
            }
        }

        if ($post) {
            $content = is_array($port) ? http_build_query($post) : $post;
            $out = "POST $path HTTP/1.0\r\n";
            $header = "Accept: */*\r\n";
            $header .= "Accept-Language: zh-cn\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "User-Agent: ".@$_SERVER['HTTP_USER_AGENT']."\r\n";
            $header .= "Host: $host:$port\r\n";
            $header .= 'Content-Length: '.strlen($content)."\r\n";
            $header .= "Connection: Close\r\n";
            $header .= "Cache-Control: no-cache\r\n";
            $header .= "Cookie: $cookie\r\n\r\n";
            $out .= $header.$content;
        } else {
            $out = "GET $path HTTP/1.0\r\n";
            $header = "Accept: */*\r\n";
            $header .= "Accept-Language: zh-cn\r\n";
            $header .= "User-Agent: ".@$_SERVER['HTTP_USER_AGENT']."\r\n";
            $header .= "Host: $host:$port\r\n";
            $header .= "Connection: Close\r\n";
            $header .= "Cookie: $cookie\r\n\r\n";
            $out .= $header;
        }

        $fpflag = 0;
        $fp = false;
        if (function_exists('fsocketopen')) {
            $fp = fsocketopen($host, $port, $errno, $errstr, $timeout);
        }
        if (!$fp) {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => $post ? 'POST' : 'GET',
                    'header' => $header,
                    'content' => $content,
                    'timeout' => $timeout,
                ),
            ));
            $fp = @fopen($scheme.'://'.$host.':'.$port.$path, 'b', false, $context);
            $fpflag = 1;
        }

        if (!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, true);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if (!$status['timed_out']) {
                while (!feof($fp) && !$fpflag) {
                    if (($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                        break;
                    }
                }
                if ($limit) {
                    $return = stream_get_contents($fp, $limit);
                } else {
                    $return = stream_get_contents($fp);
                }
            }
            @fclose($fp);
            return $return;
        }
    }
	function request_by_curl($url) {
	$ip=mt_rand(11, 191).".".mt_rand(0, 240).".".mt_rand(1, 240).".".mt_rand(1, 240); 
    $cookiee="cookie: OTZ=5932987_24_24__24_; 1P_JAR=2021-05-07-02; NID=215=QhygKmDmtG8rITI0JTeVYt93pojUYDouY8-tbUgudM653uMfRJDEegOVJh5TZcaYCJI0t5gqWPCBYfxUg87w1vJw_JGvwUWfxfQF3iKP0wOJz1QSFPN__KkXicZ85LZRfkr9gPCGycigmSDfWBR4BlS-y1FBW2JJO0yg6GjkjZOnJfrrrJscuOrUPJguzHFIwQLVrH_OtPYuql8LjpiJD7d8z1lhqh7XidrOoOKv-YkpdjPZOklYWK1ha6IPOUKnKI9Rvwk7gw; UULE=a+cm9sZTogMQpwcm9kdWNlcjogMTIKdGltZXN0YW1wOiAxNjIwMzUzNzYwNTU3MDAwCmxhdGxuZyB7CiAgbGF0aXR1ZGVfZTc6IDM1ODYxNjYwMAogIGxvbmdpdHVkZV9lNzogMTA0MTk1Mzk3MAp9CnJhZGl1czogMTExODA3MDE4MApwcm92ZW5hbmNlOiA2Cg==";
    $headers = array("Content-Type: multipart/form-data",'Referer:https://www.google.com','CLIENT-IP:'.$ip,'X-FORWARDED-FOR:'.$ip,);
	$useragent="Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36";
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
  curl_setopt($curl, CURLOPT_USERAGENT, $useragent); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
   // curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    //curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	curl_setopt($curl, CURLOPT_COOKIE, $cookiee); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {        
        echo 'Errno'.curl_error($curl);//捕抓异常    
    }

    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}
    $seo_info = array();
    $seo_info = $dsql->GetOne("SELECT * FROM `#@__plus_seoinfo` ORDER BY id DESC");
    $now = time();
   
    if ( empty($seo_info) OR $now - $seo_info['create_time'] > 60*60*6 )
    { 
        $site = str_replace(array("http://",'/'),'',$cfg_basehost);
		$site = str_replace(array("https://",'/'),'',$site); //去除https

       //$alexaRank=getAlexaRank("7799520.com");

      // $seo_info['alexa_num'] = isset($alexaRank)? trim($alexaRank) : 0;
      

      // $url = "https://www.bing.com/search?q=site:7799520.com";
       //$html =strip_tags(request_by_curl($url),"<span>");   
	  
      $site="7799520.com";


        //$url = "http://www.baidu.com/s?wd=site:{$site}";
		$url = "https://www.baidu.com/s?wd=site:zhihu.com";
    	//$html = Html2Text(dedeseo_http_send($url));
		//$html =strip_tags(Html2Text(request_by_curl($url)));
	  //  $html =str_replace('"}','}',$html);
		//$html =str_replace('"',"'",$html);
    	//if ( preg_match("#找到相关结果约([\d]+)个#",$html,$matches) )
		  if ( preg_match("%找到相关结果约(.*?)个%si",$html,$matches) )
        {
            $seo_info['baidu_count'] = isset($matches[1])? $matches[1] : 0;
        }
    	if (empty($seo_info['baidu_count']) AND preg_match("%找到相关结果约(.*?)个%si",$html,$matches) )
        {
            $seo_info['baidu_count'] = isset($matches[1])? trim($matches[1]) : 0;
        }
        $seo_info['baidu_count'] = empty($seo_info['baidu_count'])? 0 : $seo_info['baidu_count'];
		//var_dump($seo_info);die();
        $url = "http://www.sogou.com/web?query=site:zhihu.com";
    	//$html = Html2Text(dedeseo_http_send($url));
		//$html = Html2Text(request_by_curl($url));
	
		
    	if ( preg_match("#找到约([\d]+)条相关结果#",$html,$matches) )
		{
            $seo_info['sogou_count'] = isset($matches[1])? $matches[1] : 0;
        }
    	if (empty($seo_info['sogou_count']) AND preg_match("#找到约([\d, ]+)条相关结果#",$html,$matches) )
        {
            $seo_info['sogou_count'] = isset($matches[1])? trim($matches[1]) : 0;
        }
        $seo_info['sogou_count'] = empty($seo_info['sogou_count'])? 0 : $seo_info['sogou_count'];

        $url = "https://www.google.com/search?q=site:zhenai.com";
    	//$html = Html2Text(dedeseo_http_send($url));
		//$html =strip_tags(Html2Text(request_by_curl($url)));
	    $html =str_replace('"}','}',$html);
		$html =str_replace('"',"'",$html);
	

    	if ( preg_match("%About(.*?)results%si",$html,$matches) )
        {
            $seo_info['haosou360_count'] = isset($matches[1])? $matches[1] : 0;
        }
    	if (empty($seo_info['haosou360_count']) AND preg_match("%About(.*?)results%si",$html,$matches) )
        {
            $seo_info['haosou360_count'] = isset($matches[1])? trim($matches[1]) : 0;
        }
        $seo_info['haosou360_count'] = empty($seo_info['haosou360_count'])? 0 : $seo_info['haosou360_count'];
        
	
	
        $in_query = "INSERT INTO `#@__plus_seoinfo` (`create_time`, `alexa_num`, `alexa_area_num`, `baidu_count`, `sogou_count`, `haosou360_count`) VALUES ({$now}, '{$seo_info['alexa_num']}', '{getBing($site)}', '{$seo_info['baidu_count']}', '{$seo_info['sogou_count']}', '{$seo_info['haosou360_count']}');";
        $dsql->ExecuteNoneQuery($in_query);
    }
    $inff=array(
        'alexa_num'=>'Alexa全球排名',
        'alexa_area_num'=>'必应收录',
        'baidu_count'=>'百度收录',
        'sogou_count'=>'搜狗收录',
        'haosou360_count'=>'360收录',
    );
?>
<table width="100%" class="dboxtable">
    <tbody>
<?php
    foreach( $seo_info as $key => $value )
    {
        if ( $key=='id' OR $key=='create_time' ) continue;
?>

    <tr>
        <td width="50%" class="nline" style="text-align:left"> <?php
            echo $inff[$key];
        ?>
        ： </td>
        <td class="nline" style="text-align:left"> <?php
            echo $value;
        ?>
         </td>
    </tr>
<?php
}
?>

    </tbody></table>
<?php
    
	exit;
}
?>
       
    

