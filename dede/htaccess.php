<?php
/**
 * 伪静态
 *
 * 建站宝  400-66-4165  QQ:858859319
 */
 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_伪静态管理');
$a=isset($_REQUEST["a"])?$_REQUEST["a"]:"";

$tpl = new DedeTemplate();

if($a=="do"){
	if($cfg_rewrite != 'Y'){
		showMsg("请先开启伪静态","-1");	
		exit();
	}
	$sql="select * from #@__arctype";
	//栏目规则
	$db->Execute('me',$sql);
	$dedecms_htaccess="";
	$htaccess_type=array();
	while($arr = $db->GetArray()){
		if(strstr($arr["typedir"],"http://")||($arr["typedir"]=="{cmspath}")){
			continue;	
		}
		$arr["typedir"]=str_replace("{cmspath}/",$cfg_cmspath,$arr["typedir"]);
		$arr["typedir"].="/*";
		if(in_array($arr["typedir"],$htaccess_type)){
			continue;	
		}
		$htaccess_type[]=$arr["typedir"];
		$dedecms_htaccess.="RewriteRule ^".$arr["typedir"]."$"." \t \t \t/plus/list.php?tid=".$arr["id"]."\r\n";
	}
	
	//列表规则
	$htaccess_list=array();
	$db->Execute('arctyp',$sql);
	while($arr = $db->GetArray("arctyp")){
		if(strstr($arr["typedir"],"http://")){
			continue;	
		}
		$namerule2=str_replace("{typedir}","",$arr["namerule2"]);
		$namerule2=str_replace("{tid}","(\d+)",$namerule2);
		$namerule2=str_replace("{page}","(\d+)",$namerule2);
		$arr["typedir"]=str_replace("{cmspath}/",$cfg_cmspath,$arr["typedir"]).$namerule2;
		$arr["typedir"]=str_replace(".","\.",$arr["typedir"]);
		if(in_array($arr["typedir"],$htaccess_list)){
			continue;	
		}
		$htaccess_list[]=$arr["typedir"];
		$dedecms_htaccess.="RewriteRule ^".$arr["typedir"]."$"." \t \t \t/plus/list.php?tid=$1&PageNo=$2\r\n";
	}
	//文章规则
	$htaccess_show=array();
	$db->Execute('arctyp1',$sql);
	while($arr = $db->GetArray("arctyp1")){
		if(strstr($arr["typedir"],"http://")){
			continue;	
		}
		$num=substr_count($arr["namerule"],"{");
		$namerule=str_replace("{typedir}/","",$arr["namerule"]);
		$namerule=str_replace("{Y}","(\d{4})",$namerule);
		$namerule=str_replace("{M}","(\d{2})",$namerule);
		$namerule=str_replace("{D}","(\d{2})",$namerule);
		$namerule=str_replace("{aid}","(\d+)",$namerule);
		$namerule=str_replace(".html","_*(\d*).html",$namerule);
		$arr["typedir"]=str_replace("{cmspath}/",$cfg_cmspath,$arr["typedir"]);
		$arr["typedir"].="/";
		$arr["typedir"].=$namerule;
		if(in_array($arr["typedir"],$htaccess_show)){
			continue;	
		}
		$htaccess_show[]=$arr["typedir"];
		$dedecms_htaccess.="RewriteRule ^".$arr["typedir"]."$"." \t \t \t/plus/view.php?aid=$".($num-1)."&pageno=$".$num."\r\n";
	}
	
	
	
	$dedecms_htaccess_head="RewriteEngine On \r\nRewriteBase / \r\n#dedecms#\r\n";
	
	if(!file_exists(dirname(__FILE__).'/../.htaccess')){
		$dedecms_htaccess=$dedecms_htaccess_head.$dedecms_htaccess."#dedecms#";
	}else{
		$dedecms_htaccess_old=file_get_contents(dirname(__FILE__)."/../.htaccess");
		if(strstr($dedecms_htaccess_old,"#dedecms#")){
			$dedecms_htaccess=ereg_replace("#dedecms#(.*)#dedecms#","#dedecms#\r\n".$dedecms_htaccess."#dedecms#",$dedecms_htaccess_old);
		}else{
			$dedecms_htaccess=$dedecms_htaccess_old."\r\n#dedecms#\r\n#".$dedecms_htaccess."#dedecms#";		
		}
	}
	
	file_put_contents(dirname(__FILE__)."/../.htaccess",$dedecms_htaccess);
	ShowMsg("伪静态规则生成完成！","-1");

}else{
	$tpl->LoadTemplate('templets/htaccess.htm');
	$tpl->Display();
}
?>