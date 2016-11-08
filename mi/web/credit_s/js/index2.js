var scache = new Array();var ccache = new Array();var mingzhan = new Array();var indextab="mingzhan";var stat=1;var timer=null;function qhshow(a){timer=setTimeout("newshow("+a+")",500)}
function newshow(b){
	if(b==0){
		$("#tabname > li").removeClass("cur_main");
		$("#s10").addClass("cur_main");
		$("#mzdh_other").hide();
		$("#s_s10").show();
	}else{
		$("#s_s10").hide();
		$("#mzdh_other > div").hide();
		if(mingzhan[b]!=undefined){
			$("#mz"+b).show();
		}else{
			$.getJSON('getdata.php?act=getTab&iid='+b,function(json){
				str='<div id="mz'+b+'">'+json+'</div>';
				$("#mzdh_other").append(str);
				mingzhan[b]=str;
			});
		}
		$("#tabname > li").removeClass("cur_main");
		$("#s1"+b).addClass("cur_main");
		$("#mzdh_other").show();
	}
}
function getCookie(a){var b=a+"=";var c="";if(document.cookie.length>0){offset=document.cookie.lastIndexOf(b);if(offset!=-1){offset+=b.length;end=document.cookie.indexOf(";",offset);if(end==-1){end=document.cookie.length}c=unescape(document.cookie.substring(offset,end))}}return c}function SetCookie(c,e,a,f,d){var b=new Date();b.setTime(b.getTime()+30*24*60*60);a=b.toGMTString();f="/";d="";document.cookie=c+"="+escape(e)+("; expires="+a)+("; path="+f)+";"}
function toolChange(c){
	var u=new Array('http://www.76mi.com/data/html/youxi.htm','http://www.76mi.com/data/html/jipiao.htm','http://www.76mi.com/data/html/jiudian.htm');
	var b=4;
	for(var a=1;a<=b;a++){
		document.getElementById("tool-tab"+a).className='';
		document.getElementById("tb"+a).style.display='none';
		if(a>=2 && $('#tbif'+a).attr('src')!=u[a-2])
			$('#tbif'+a).attr('src',u[a-2]);
	}
	document.getElementById("tool-tab"+c).className="active";
	document.getElementById("tb"+c).style.display='';
}
function colorOn(b){var c=getCookie("pcss");var a=flag2=flag3="";if(c=="green"){flag2="On"}else{if(c=="pink"){flag3="On"}else{a="On"}}$("#blue").addClass("blue"+a);$("#green").addClass("green"+flag2);$("#pink").addClass("pink"+flag3)}
function setHomePage(c,d){if(document.all){document.body.style.behavior="url(#default#homepage)";document.body.setHomePage(d)}else{if(window.sidebar){if(window.netscape){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")}catch(b){alert("\u60a8\u7684firefox\u5b89\u5168\u9650\u5236\u9650\u5236\u60a8\u8fdb\u884c\u526a\u8d34\u677f\u64cd\u4f5c\uff0c\u8bf7\u5728\u6d4f\u89c8\u5668\u5730\u5740\u680f\u8f93\u5165\u2019about:config\u2019\u5e76\u56de\u8f66\uff0c\u7136\u540e\u5c06\u2019signed.applets.codebase_principal_support\u2019\u8bbe\u7f6e\u4e3a\u2019true\u2019\u4e4b\u540e\u91cd\u8bd5\u3002");return}}var a=Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefBranch);a.setCharPref("browser.startup.homepage",d)}}}function getNowFormatDate(){var b=new Date();var a=0;var c=0;var e=0;var d="";a=b.getFullYear();c=b.getMonth()+1;e=b.getDate();d+=a+"-";if(c>=10){d+=c+"-"}else{d+="0"+c+"-"}if(e>=10){d+=e}else{d+="0"+e}$("#jp_today").val(d)};
function toLoginCheck(){var tbUserName = document.getElementById("tbUserName").value;var tbUserPwd = document.getElementById("tbUserPwd").value;if (tbUserName == '') {alert('请输入用户名！');return false;}if (tbUserPwd == '') {alert('请输入密码！');return false;}return true;}
function gotosearch(){
	var objfrm = document.sform;
	if(objfrm.backurl !=undefined){
		var re;
		re = /key=(.*)/g;             // 创建正则表达式模式。
		var keys = objfrm.key.value;
		keys = 'key='+keys;
		var url = objfrm.backurl.value;
		objfrm.backurl.value = url.replace(re, keys);
	}
	return true;
}
function change(f){
	var key = $("#kw").val();
	$("#s_ul > li").removeClass("cur");
	$("#so_"+f).addClass("cur");
	if(scache[f]!=undefined){
		$("#sform").html(scache[f]);
		getfocus(key);
	}else{
		$.getJSON('getdata.php?act=getsearch&sid='+f,function(json){
			var action = json.def.action,onsub='',inputid='kw';
			if(action.indexOf('taobao')!=-1){
				inputid='q';
			}else if(action.indexOf('dangdang')!=-1){
				onsub=' onsubmit="return gotosearch();"';
			}
			var str='';
			str+='<div id="sf"><div id="search_form"><form name="sform" action="'+json.def.action+'" method="get"'+onsub+'>';
			str+='<p><a href="'+json.def.url+'"><img src="'+json.def.img_url+'" border="0"/></a>';
			str+='<input type="text" onblur="this.className=\'int\';" onmouseover="this.className=\'int_on\';this.focus();" size="42" class="int" autocomplete="off" name="'+json.def.name+'" id="'+inputid+'"/>';
			var i=json.i;
			for(var j=0;j<i;j++){
				str+='<input type="hidden" name="'+json.params[j][0]+'" value="'+json.params[j][1]+'"/>';
			}
			str+='<input type="submit" value="'+json.def.btn+'" id="bdbutton" class="searchint"></p></form></div>';
			str+='<div class="ctrl">';
			var l=json.search.length;
			for(var z=0;z<l;z++){
				var c="";
				if(json.search[z].is_default == 1){
					c = " checked='checked'";
				}
				str+='<label><input class="radio" onclick="changesearch('+json.search[z].id+')" type="radio" name="search_select"'+c+'/>'+json.search[z].search_select+'</label>';
			}
			str+='</div></div><div id="hot_words"><ul>';
			var l=json.keywords.length;
			for(var z=0;z<l;z++){
				if(json.keywords[z].namecolor !=''){
					str+='<li><a href="'+json.keywords[z].url+'" style="color:'+json.keywords[z].namecolor+'">'+json.keywords[z].name+'</a></li>';
				}else{
					str+='<li><a href="'+json.keywords[z].url+'">'+json.keywords[z].name+'</a></li>';
				}
			}
			str+='</ul></div>';
			$("#sform").html(str);
			scache[f]=str;
			getfocus(key);
		})
	}
}
function changesearch(id){
	var key = $("#kw").val();
	if(ccache[id]!=undefined){
		$("#search_form").html(ccache[id]);
		getfocus(key);
	}else{
		$.getJSON('getdata.php?act=getcon&id='+id,function(json){
			var action = json.con.action,onsub='',inputid='kw';
			if(action.indexOf('taobao')!=-1){
				inputid='q';
			}else if(action.indexOf('dangdang')!=-1){
				onsub=' onsubmit="return gotosearch();"';
			}
			var str='';
			str+='<form name="sform" action="'+json.con.action+'" method="get"'+onsub+'>';
			str+='<p><a href="'+json.con.url+'"><img src="'+json.con.img_url+'" border="0"/></a>';
			str+='<input type="text" size="42" class="int" autocomplete="off" name="'+json.con.name+'" id="'+inputid+'"/>';
			var i=json.i;
			for(var j=0;j<i;j++){
				str+='<input type="hidden" name="'+json.params[j][0]+'" value="'+json.params[j][1]+'"/>';
			}
			str+='<input type="submit" value="'+json.con.btn+'" id="bdbutton" class="searchint"></p></form>';
			$("#search_form").html(str);
			ccache[id] = str;
			getfocus(key);
		})
	}
}
function neichange(f){
	var key=$("#kw").val();
	$("#s_ul_detail > li").removeClass("cur");
	$("#so_"+f).addClass("cur");
	$.getJSON('../getdata.php?act=getneisearch&sid='+f,function(json){
		var action = json.def.action,inputid='kw';
		if(action.indexOf('taobao')!=-1){
			inputid='q';
		}
		var str='';
		str+='<form name="f" action="'+json.def.action+'" method="get">';
		str+='<p><a href="'+json.def.url+'"><img src="../'+json.def.img_url+'" border="0"/></a>';
		str+='<input type="text" style="width:262px" class="int" autocomplete="off" name="'+json.def.name+'" id="'+inputid+'"/>';
		var i=json.i;
		for(var j=0;j<i;j++){
			str+='<input type="hidden" name="'+json.params[j][0]+'" value="'+json.params[j][1]+'"/>';
		}
		str+='<input type="submit" value="'+json.def.btn+'" id="bdbutton" class="searchint"></p></form>';
		$("#sform_detail").html(str);
		getfocus(key);
	})
}
var bcache =new Array();
bcache[4]='<form method="get" action="http://www.baidu.com/s" name="f"><p><a href="http://www.baidu.com/"><img border="0" src="http://www.76mi.com/static/images/s/baidu.gif"></a><input type="text" id="b_kw" name="wd" autocomplete="off" class="int" size="42"><input type="hidden" value="zjggws_pg" name="tn"><input type="submit" class="btn" id="bdbutton" value="百度一下"></p></form>';
bcache[13]='<form method="get" action="http://www.google.com.hk/cse" name="f"><p><a href="http://www.google.com.hk/webhp?prog=aff&amp;client=pub-9678158397263530&amp;channel=3192690012"><img border="0" src="http://www.76mi.com/static/images/s/google.gif"></a><input type="text" id="b_kw" name="q" autocomplete="off" class="int" size="42"><input type="hidden" value="partner-pub-9678158397263530:kpt3byy801c" name="cx"><input type="hidden" value="GB2312" name="ie"><input type="submit" class="btn" id="bdbutton" value="谷歌搜索"></p></form>';
bcache[22]='<form method="get" action="http://www.soso.com/q" name="f"><p><a href="http://www.soso.com/?unc=y400300&amp;cid=union.s.wh"><img border="0" src="http://www.76mi.com/static/images/s/soso.gif"></a><input type="text" id="b_kw" name="w" autocomplete="off" class="int" size="42"><input type="hidden" value="y400300" name="unc"><input type="hidden" value="union.s.wh" name="cid"><input type="hidden" value="gb2312" name="ie"><input type="submit" class="btn" id="bdbutton" value="搜 索"></p></form>';
bcache[36]='<form method="get" action="http://www.sogou.com/sogou" name="f"><p><a href="http://www.sogou.com/index.php?pid=sogou-site-"><img border="0" src="http://www.76mi.com/static/images/s/sogou.gif"></a><input type="text" id="b_kw" name="query" autocomplete="off" class="int" size="42"><input type="hidden" value="sogou-site-" name="pid"><input type="submit" class="btn" id="bdbutton" value="搜狗搜索"></p></form>';
bcache[44]='<form method="get" action="http://www.youdao.com/search" name="f"><p><a href="http://www.youdao.com/n2/?keyfrom=dh&amp;vendor=dh"><img border="0" src="http://www.76mi.com/static/images/s/youdao.gif"></a><input type="text" id="b_kw" name="q" autocomplete="off" class="int" size="42"><input type="hidden" value="gbk" name="ue"><input type="hidden" value="dh" name="keyfrom"><input type="hidden" value="dh." name="vendor"><input type="submit" class="btn" id="bdbutton" value="搜 索"></p></form>';
bcache[45]='<form method="get" action="http://movie.gougou.com/search" name="f"><p><a href="http://movie.gougou.com/search"><img border="0" src="http://www.76mi.com/static/images/s/gougou.gif"></a><input type="text" id="b_kw" name="search" autocomplete="off" class="int" size="42"><input type="hidden" value="10000000" name="id"><input type="submit" class="btn" id="bdbutton" value="搜 索"></p></form>';
bcache[46]='<form method="get" action="http://search8.taobao.com/browse/search_auction.htm" name="f"><p><a href="http://www.taobao.com/go/chn/tbk_channel/onsale.php?pid=mm_29047304_2449683_9382978"><img border="0" src="http://www.76mi.com/static/images/s/taobao.gif"></a><input type="text" id="b_kw" name="q" autocomplete="off" class="int" size="42" ><input type="hidden" value="mm_29047304_2449683_9382978" name="pid"><input type="hidden" value="all" name="commend"><input type="hidden" value="action" name="search_type"><input type="submit" class="btn" id="bdbutton" value="淘宝搜索"></p></form>';
function b_changesearch(id){var key = $("#b_kw").val();$("#search_bottom").html(bcache[id]);bgetfocus(key)}
function bgetfocus(key){$("#b_kw").focus();$("#b_kw").val(key);};
function getfocus(key){	$("#kw").focus();$("#kw").val(key);taobaoready();}
$(document).ready(function(){
	var timer = 500; //下拉菜单延时
	var activeContent;
	var hideState = true;
	var delayInterval;
	var hide = function(){
		if(hideState && activeContent){
			activeContent.style.display = "none";			
		}
	}
	$("#topsite em").each(function(el){
		el=this;
		el.onmouseover = function(){
			hide();
			var box = el.parentNode.getElementsByTagName("div")[0];
			delayInterval = window.setTimeout(function(){$(box).show()},timer);
			activeContent = box;
			hideState = false;
			if(!box.onmouseover){
				box.onmouseover = function(){
					hideState = false;
				}
				box.onmouseout = function(){
					hideState = true;
					window.setTimeout(hide,timer);
				}
			}
		}
		el.onmouseout = function(){
			hideState = true;
			window.setTimeout(hide,timer);
			if(delayInterval!=undefined){
				window.clearTimeout(delayInterval);
			}

		}
	});
var st,addsiteurl,addsitename;
	$("#tab_s td a").hover(
		function(){var offset = $(this).offset();var w = $(this).width()+1;addsiteurl = $(this).attr("href");addsitename= $(this).text();if(st){window.clearTimeout(st);}$("#addmyfav").css({left:offset.left+w+"px",top:offset.top+"px"}).show();},
		function(){st = window.setTimeout(function(){$("#addmyfav").hide();},50)});
	$("#addmyfav").hover(
		function(){if(st){window.clearTimeout(st);}$("#addmyfav").show();$("#addmyfav").attr("href","http://www.76mi.com/i/share.php?url="+addsiteurl+"&title="+addsitename);},
		function(){st = window.setTimeout(function(){$("#addmyfav").hide();},50);});
});
