<?php
function litimgurls($imgid=0)
{
    global $lit_imglist,$dsql;
    //获取附加表
    $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c 
                                                            ON a.channel=c.id where a.id='$imgid'");
    $addtable = trim($row['addtable']);
    
    //获取图片附加表imgurls字段内容进行处理
    $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");
    
    //调用inc_channel_unit.php中ChannelUnit类
    $ChannelUnit = new ChannelUnit(2,$imgid);
    
    //调用ChannelUnit类中GetlitImgLinks方法处理缩略图
    $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);
    
    //返回结果
    return $lit_imglist;
}

/**
 *  用来修正后台读取出现少了个逗号的问题，获得某文档的所有tag
 *
 * @param     int     $aid  文档id
 * @return    string
 */
function Get2Tags($aid)
{
        global $dsql;
        $tags = '';
        $query = "SELECT tid,tag FROM `#@__taglist` WHERE aid='$aid' ";
        $dsql->Execute('tag',$query);
        while($row = $dsql->GetArray('tag'))
        {
            $tags .= ($tags=='' ? $row['tag'] : ','.$row['tag']); //20190213
			
        }
        return $tags;
}

/**
 * 获得指定栏目的tag20190213
 *
 * @param      int    $typeid 栏目id
 * @return     string
 */

function GetTypeTag($typeid)
    {
        global $dsql;
        $tags = '';
		//group by 过滤重复tag
        $query = "SELECT tid,tag FROM `#@__taglist` WHERE typeid='$typeid'  Group By tag ORDER BY RAND() LIMIT 6";
        $dsql->Execute('tag',$query);
        while($row = $dsql->GetArray('tag'))
        {
            $tags .= '<a href="/tags/'.$row['tid'].'.html" class="tag_box" target="_blank">'.$row['tag'] .'</a>';
        }
        return $tags;
}

//模板解析标签函数
function pasterTempletDiy($path)
{
  require_once(DEDEINC."/arc.partview.class.php");
  global $cfg_basedir,$cfg_templets_dir;
  $tmpfile = $cfg_basedir.$cfg_templets_dir."/".$path;
  $dtp = new PartView();
  $dtp->SetTemplet($tmpfile);
  $dtp->Display();
}

//彩色标签云所需函数getTagStyle()
function getTagStyle() 
{ 
$minFontSize=12; //最小字体大小,可根据需要自行更改 
$maxFontSize=25; //最大字体大小,可根据需要自行更改 

return 'font-size:'.($minFontSize+lcg_value()*(abs($maxFontSize-$minFontSize))).'px;color:#'.dechex(rand(0,255)).dechex(rand(0,196)).dechex(rand(0,255)); 
} 


//替换图片标签代码为google AMP支持的img标签
function replaceAmpImages($content)
{
    preg_match_all('/<img (.*?)\>/', $content, $images);
    if(!is_null($images)){
        foreach($images[1] as $index => $value){
            $amp_img = str_replace('<img', '<amp-img', $images[0][$index]);
            $amp_img = str_replace('>', '></amp-img>', $amp_img);
            //以下代码可根据需要修改/删除
            //$amp_img = preg_replace('/(width|height)="\d*"\s/', '', $amp_img );//移除图片width|height
            $amp_img = preg_replace('/ style=\".*?\"/', '',$amp_img);//移除图片style
            $amp_img = preg_replace('/ class=\".*?\"/', '',$amp_img);
            $content = str_replace($images[0][$index], $amp_img, $content);
        }
    }
    return $content;
}
//替换图片标签为百度mip支持img标签
function replaceMipImages($content)
{
    preg_match_all('/<img (.*?)\>/', $content, $images);
    if(!is_null($images)){
        foreach($images[1] as $index => $value){
            $mip_img = str_replace('<img', '<mip-img', $images[0][$index]);
            $mip_img = str_replace('>', '></mip-img>', $mip_img);
            //以下代码可根据需要修改/删除
            $mip_img = preg_replace('/(width|height)="\d*"\s/', '', $mip_img );//移除图片width|height
            $mip_img = preg_replace('/ style=\".*?\"/', '',$mip_img);//移除图片style
            $mip_img = preg_replace('/ class=\".*?\"/', '',$mip_img);//移除图片class
            $content = str_replace($images[0][$index], $mip_img, $content);
        }
    }
    return $content;
}
//文章页head段 文章富媒体代码输出 非AMP页面 <代码有问题图片处理部分未完善>2021/4/29
function artMeta($headline,$image,$datePublished){
  $RData = '<script type="application/ld+json">' . "\r\n" ;
  $RData = $RData . "{" ."\r\n" ;
  $RData = $RData . '"@context": "https://schema.org",'."\r\n";
  $RData = $RData . '"@type": "NewsArticle",'."\r\n";
  $RData = $RData . '"headline": "'. $headline .'",';
  $RData = $RData . 'image": [' ."\r\n";
  $RData = $RData . '"https://example.com/photos/1x1/photo.jpg",\r\n' . '"https://example.com/photos/4x3/photo.jpg",\r\n'. '"https://example.com/photos/16x9/photo.jpg\r\n"';
	//nginx伪静态 配合 php实现实时变换图片大小		

  $RData = $RData . "],\r\n";
  $RData = $RData . '"datePublished": '. '"'.$datePublished.",";
  $RData = $RData . '\r\n}</script>'; 
  
  return $RData;

}
//根据文章id返回url
function id2Url($ID)
{
	global $dsql;
	$query = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.isdefault,tp.defaultname,tp.namerule,tp.moresite,tp.siteurl,tp.sitepath from #@__archives arc left join #@__arctype tp on arc.typeid=tp.id where arc.id = ".$ID;
	$row = $dsql->GetOne($query);
	$Url = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],$row['typedir'],$row['money'],$row['filename'],$row['moresite'],$row['siteurl'],$row['sitepath']);
	return $Url;
}
//title 转 url 发布文章时filename 需要的函数 2021/4/15
function GetPinyin($str, $ishead=0, $isclose=1)
{
    global $pinyins;
    $restr = '';
    $str = trim($str);
    $slen = strlen($str);
    if($slen < 2){return $str;}
    for($i=0; $i<$slen; $i++)
    {
        if(ord($str[$i])>0x80)
        {
            $c = $str[$i].$str[$i+1];
            $i++;
            if(isset($pinyins[$c]))
            {
                if($ishead==0)
                {
                    $restr .= $pinyins[$c];
                }
                else
                {
                    $restr .= $pinyins[$c][0];
                }
            }else
            {
                $restr .= "_";
            }
        }else if( preg_match("/[a-z0-9]/i", $str[$i]) )
        {
            $restr .= $str[$i];
        }
        else
        {
            $restr .= "_";
        }
    }
    if($isclose==0)
    {
        unset($pinyins);
    }
    //2021/03/04 add
    $restr=str_replace("___","-",$restr);
    $restr=str_replace("__","-",$restr);
    $restr=rtrim($restr,"-");
	$restr=strtolower($restr);//转换为小写 2021/4/15
    //2021/03/04 end
   
	
    return $restr;
}
/**
 *  获取随机用户名
 *
 * @access    public
 * @return    string
 */

function RandomUser()
{
	$male_names = array("Wallace Lopez","Albert Bradley","Bob Mason","Hugh Barrett","George Vargas","Danny Flores","Hunter Barnett","Danny Medina","Kelly Mitchell","Dave Harvey","Martin Obrien","Lucas Lawrence","Ritthy Watson","Joseph Lucas","Christian Alexander","Aiden Obrien","Ken Barrett","Arron Montgomery","Dylan Wheeler","Ivan Berry","Carlos Curtis","Leo Gonzalez","Aiden Fowler","Soham Stephens","Gene Payne","Raymond Moreno","Jacob Parker","Darren Alexander","Larry Johnston","Tim James","Jeff Martinez","Shane Lee","Jimmy James","Isaac Stone","Erik Jones","Ben Bennett","Albert Wilson","Ryan Mills","Earl Nelson","Vincent Soto","Clayton Frazier","Tomothy Beck","Sergio Carroll","Jonathan Neal","Brennan Soto","Floyd Crawford","Kyle Lowe","Franklin Gonzalez","Isaiah Simmons","Brennan Jacobs");
	$famale_names = array("Sylvia Duncan","Annette Lynch","Evelyn Johnston","Hailey Simmmons","Sharlene Barrett","Joyce Fleming","Isobel Hansen","Eva Black","Sharlene Rivera","Rose Mitchell","Laurie Ortiz","Rita Cook","Bernice Graham","Arianna Baker","Christine Ortiz","Sylvia Elliott","Sarah Owens","Phyllis Johnson","Rachel Butler","Myrtle Clark","Lynn Wright","Lillian Graham","Daisy Hanson","Sofia Mitchelle","Marjorie Alexander","Marlene Fletcher","Christina Diaz","Tammy Craig","Elsie Hill","Jessie Gonzales","Anne Horton","Brandie Woods","Rebecca Cunningham","Cindy Green","Ellen Alexander","Terra Mason","Frances Grant","Misty Rose","Bertha Crawford","Tina Simmons","Tracy Olson","Jackie Robinson","Alma Mitchell","Kylie Peterson","Louise Hayes","Colleen Olson","Myrtle Chapman","Georgia Coleman","Kim Sanchez","Harper Johnson");

	$frist_num = mt_rand(0,19); //随机种子
	$type = rand(0,1);
	if($type==0){
		$username=$male_names[$frist_num];
	} else {
		$username=$famale_names[$frist_num];
	}
	return $username;
}
/**
 *  获取Aleax全球排名 后台index_body.php获取seo信息使用 
 *
 * @access    public
 * @return    string
 */
function getAlexaRank($Domain) {
    $line = "";
    $data = "";
    $URL = "http://data.alexa.com/data/?cli=10&dat=snba&url=" . $Domain;
    $fp = fopen($URL, "r");
    if ($fp) {
        while (!feof($fp)) {
            $line = fgets($fp);
            $data.= $line;
        }
        $p = xml_parser_create();
        xml_parse_into_struct($p, $data, $vals);
        xml_parser_free($p);
        for ($i = 0;$i < count($vals);$i++) {
            if ($vals[$i]["tag"] == "POPULARITY") {
                return $vals[$i]["attributes"]["TEXT"];
            }
        }
    }
}
/**
 *  获取bing收录数 后台index_body.php获取seo信息使用 
 *
 * @access    public
 * @return    string
 */
function getBing($site){
	$caiji=file_get_contents("http://www.bing.com/search?q=site%3A{$site}&qs=ds&form=QBRE&mkt=zh-CN");
//var_dump($caiji);exit;
	if(preg_match('%<span class="sb_count">(.*)条结果</span>%is',$caiji ,$matches))
	{
	
//$bingindex =intval($matches[1]);
		
	//	print_r(is_string($bingindex));exit;
		
		//$seo_info = isset($bingindex)? trim($bingindex) : 0; 
		   
	}
		
		$seo_info=str_replace(",","",$matches[1]);
	
	//echo  strval($seo_info);
	  return trim(strval($seo_info));
}
