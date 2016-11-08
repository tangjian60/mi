$(document).ready(function()
	{
	
		$(".search_xiala").click(function()
		{
			$(".search_hiddendiv").fadeToggle();
		})
		$(".content_all").click(function()
		{
			$(".search_hiddendiv").fadeOut();
		})
		//搜索框点击下拉/隐藏；
		$(".foot_totop").click(function(){
			var speed=150;
			$('html').animate({scrollTop:0},speed);
			$('body').animate({scrollTop:0},speed);
		})//点击按钮返回顶部	

		$("#search_value li").click(function()
		{
			$("#search_value li").click(function()
			{
				$(this).text();
			})
			$("#search_ipt").attr("placeholder",$(this).text());
			$(".search_hiddendiv").fadeOut();
		});//点击下拉内容input获取值；
		$(window).scroll(function(){
			if($(window).scrollTop()>$(".header").height()-30)
			{
				$(".searchbox").css('position','fixed');
				$(".searchbox").css('opacity','0.9');
				$(".searchbox").css('top','0');
				$(".searchbox").css('width','100%');
			}
			else
			{
				$(".searchbox").css('position','');
				$(".searchbox").css('opacity','');
				$(".searchbox").css('top','');
				$(".searchbox").css('width','');
				$(".searchbox").css('padding-left',"15%");
				$(".searchbox").css('padding-right',"");

			}
		});//滚动拉下条搜索框固定在顶部；
		$("#top_mainweb").click(function(){
			$("#top_maindiv").fadeToggle(500);
		});
		$(".all").click(function(){
			$("#top_maindiv").fadeOut(500);
		});//点击显示/隐藏div
		$("#email_input").click(function(){
			$("#login_hidDiv").fadeToggle();
		});//登录div点击显示
		dt=setInterval('autoScroll("#joke")',3000);
		//dl=setInterval('autoScroll(".activity_top")',3000);
		dl=setInterval('autoImg(".activityBox")',3000);
		$(".slide-item").css("opacity",'1');

		$(".logo_span").click(function(){
			$("#logodiv_ul").fadeToggle();//点击下拉logo；
		});
		$(".left_guanbi").click(function(){
			$(".left_guanggao").hide();
		});
		$(".right_guanbi").click(function(){
			$(".right_guanggao").hide();
		});//点击关闭两侧广告
		$(".search_selct").click(function(){
			$("#selectUl").fadeToggle();
		})//搜索内容点击下拉；
			$(".left_guanggao").slideDown(1000);
			$(".right_guanggao").slideDown(1000);
		var l =$("#selectUl li");
		l.click(function(){
			var i=$(this).text();
			$(".search_btn").attr('vall',i);
			$(".search_selct").html(i);
			$("#selectUl").hide();
		})//搜索内容点击切换
		$("#logodiv_ul li img").click(function(){
			var url=$(this).attr('src');
			$("#logo_img").attr('src',url);
			$("#logodiv_ul").hide();
			var value=$(this).attr('value');
			$(".search_btn").html(value);
			$(".search_btn").attr('val',value);
		})//搜索logo点击切换
		$(".bottom_div").mouseover(function(){
			$(".bottom_guanbi").show();
		})
		$(".bottom_div").mouseout(function(){
			$(".bottom_guanbi").hide();
		})
		$(".bottom_guanbi").click(function(){
			$(".bottom_div img").hide();
		})
		$(".btm_close").click(function(){
			$(".bottom_guanggao").fadeOut(500);
		})
		$(".bottom_guanggao").mouseover(function(){
			$(".btm_close").show();
		})
		$(".bottom_guanggao").mouseout(function(){
			$(".btm_close").hide();
		})//点击关闭广告;
		$(".fcolnavi li ").css('border','none');
		$(".sortSite li a").css('margin-right','0');
		var rand = Math.random(0,1);
		$(".search_btn").click(function(){
			var search = $("input#searchIpt").val();
			if($(".search_btn").attr('vall') == "网页"){
				var url=rand>0.5?"https://www.baidu.com/s?tn=91618837_hao_pg&word="+search:"https://www.baidu.com/s?tn=90027296_hao_pg&word="+search;
			}else if($(".search_btn").attr('vall') == "音乐"){
				var url=rand>0.5?"http://music.hao123.com/search?tn=91618837_hao_pg&key="+search+"&fr=hao123&ie=utf-8":"http://music.hao123.com/search?tn=90027296_hao_pg&key="+search+"&fr=hao123&ie=utf-8";
			}else if($(".search_btn").attr('vall') == "视频"){
				var url=rand>0.5?"http://v.baidu.com/v?tn=91618837_hao_pg&word="+search+"&fr=video&ie=utf-8":"http://v.baidu.com/v?tn=90027296_hao_pg&word="+search+"&fr=video&ie=utf-8";
			}else if($(".search_btn").attr('vall') == "图片"){
				var url=rand>0.5?"http://image.baidu.com/search/index?tn=91618837_hao_pg&word="+search+"&tn=baiduimage":"http://image.baidu.com/search/index?tn=91618837_hao_pg&word="+search+"&tn=baiduimage";
			}else if($(".search_btn").attr('vall') == "贴吧"){
				var url=rand>0.5?"http://tieba.baidu.com/f?tn=91618837_hao_pg&kw="+search+"&ie=utf-8&sc=hao123":"http://tieba.baidu.com/f?tn=91618837_hao_pg&kw="+search+"&ie=utf-8&sc=hao123";
			}else if($(".search_btn").attr('vall') == "知道"){
				var url=rand>0.5?"http://zhidao.baidu.com/search?tn=91618837_hao_pg&word="+search:"http://zhidao.baidu.com/search?tn=91618837_hao_pg&word="+search;
			}else if($(".search_btn").attr('vall') == "新闻"){
				var url=rand>0.5?"http://news.baidu.com/ns?tn=91618837_hao_pg&word="+search+"tn=news&ie=utf-8":"http://news.baidu.com/ns?tn=91618837_hao_pg&word="+search+"tn=news&ie=utf-8";
			}else if($(".search_btn").attr('vall') == "地图"){
				var url=rand>0.5?"http://map.baidu.com/?tn=91618837_hao_pg&newmap=1&ie=utf-8&s=s%26wd%3D"+search+"%26c%3D1":"http://map.baidu.com/?tn=91618837_hao_pg&newmap=1&ie=utf-8&s=s%26wd%3D"+search+"%26c%3D1";		
			}else{
				var url=rand>0.5?"https://www.baidu.com/more?tn=91618837_hao_pg":"https://www.baidu.com/more?tn=91618837_hao_pg";		
				}
		/*	if($(".search_btn").attr('val') == "有道搜索"){
				var url=rand>0.5?"http://www.youdao.com/search?tn=91618837_hao_pg&q="+search:"http://www.youdao.com/search?tn=90027296_hao_pg&q="+search;
			}else if($(".search_btn").attr('val') == "搜狗搜索"){
				var url=rand>0.5?"http://www.sogou.com/sogou?tn=91618837_hao_pg&query="+search:"http://www.sogou.com/sogou?tn=90027296_hao_pg&query="+search;
			}else if($(".search_btn").attr('val') == "淘宝网"){
				var url=rand>0.5?"https://s.taobao.com/search?tn=91618837_hao_pg&q="+search:"https://s.taobao.com/search?tn=90027296_hao_pg&q="+search;
			}else if($(".search_btn").attr('val') == "搜搜"){
				var url=rand>0.5?"http://news.soso.com/n.q?tn=91618837_hao_pg&query="+search:"http://news.soso.com/n.q?tn=90027296_hao_pg&query="+search;
			}else if($(".search_btn").attr('val') == "狗狗搜索"){
				var url=rand>0.5?"http://www.gougou.hk/web/?tn=91618837_hao_pg&q="+search:"http://www.gougou.hk/web/?tn=90027296_hao_pg&q="+search;
			}else{
				var url=rand>0.5?"https://www.baidu.com/s?tn=91618837_hao_pg&wd="+search:"https://www.baidu.com/s?tn=90027296_hao_pg&wd="+search;	
			}*/
			window.open(url,"_top").opener = null;     
		})
	
	});


	function autoScroll(divObj){
		var n=$(divObj).find("li").height();
		$(divObj).find("ul:first").animate({
			marginTop:-n,
		},500,function(){
			$(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
		})
	}//滚动函数;
	function autoImg(imgObj){
		var n=$(imgObj).find(".slide-item").height();
		$(imgObj).find("div:first").animate({
			marginTop:-n,
		},500,function(){
			$(this).css({marginTop:"0px"}).find("div:first").appendTo(this);
			//console.log('321321');
		})
	}//滚动函数;  

		

	