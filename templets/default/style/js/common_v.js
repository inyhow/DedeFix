$(function(){

/*list tab*/
function clicktabs(tit_id,box_id,cur){
		var g_tags=$(tit_id),
			g_conts=$(box_id),
			g_current=cur;
		g_tags.mouseover(function(){
			var g_index=g_tags.index(this);
			$(this).addClass(g_current).siblings().removeClass(g_current);
			g_conts.eq(g_index).show().siblings().hide();
		})
	}

	clicktabs("#labels_id li","#labels_con>div","current");
	clicktabs("#labels_id01 li","#labels_con01>div","current");
	clicktabs("#labels_id02 li","#labels_con02>div","current");
	clicktabs("#labels_id03 li","#labels_con03>div","current");
	clicktabs("#labels_id04 li","#labels_con04>ol","current");
	clicktabs("#labels_id05 li","#labels_con05>div","current");

	/*fav*/
	$("#fav").click(function() { 
		var ctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL'; 
		if (document.all){ 
		  window.external.addFavorite('http://www.leiting001.com/?fav','军事网-中国军事网-军事新闻尽在雷霆军事网'); 
		  }else if (window.sidebar){ 
		 window.sidebar.addPanel('军事网-中国军事网-军事新闻尽在雷霆军事网',  'http://www.leiting001.com/?fav', ""); 
	   }else { 
			   alert('您可以尝试通过快捷键' + ctrl + ' + D 加入到收藏夹!'); 
	   } 
	});

	$("#input_id").bind('click',function(){
		var url = window.location;
		var sitename = document.title;
			var vctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL'; 
			if (document.all){ 
			  window.external.addFavorite(url,sitename); 
			  }else if (window.sidebar){ 
			 window.sidebar.addPanel(sitename,url, ""); 
		   }else { 
				 alert('您可以尝试通过快捷键' + vctrl + ' + D 加入到收藏夹!'); 
		   } 
	});

});
