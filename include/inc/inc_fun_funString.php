<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

//脚本之家 摘自老外之手 unicode码 转 中文
function replace_unicode_escape_sequence($match) {
  return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function SpHtml2Text($str)
{
	
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
	
	$alltext = "";
	$start = 1;
	for($i=0;$i<strlen($str);$i++)
	{
		if($start==0 && $str[$i]==">")
		{
			$start = 1;
		}
		else if($start==1)
		{
			if($str[$i]=="<")
			{
				$start = 0;
				$alltext .= " ";
			}
			else if(ord($str[$i])>31)
			{
				$alltext .= $str[$i];
			}
		}
	}
	$alltext = str_replace("　"," ",$alltext);
	$alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
	$alltext = preg_replace("/[ ]+/s"," ",$alltext);
	//$alltext=replace_unicode_escape_sequence($alltext); //修复html2text转换unicode的截取不全的问题2021/05/06
	return $alltext;
}

?>