<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>最佳东方</title>
<link href="http://static.v.veimg.cn/css/job/my/css.css" rel="stylesheet" type="text/css" />
<style type=text/css>
.bar1208{
	Z-INDEX: 99; LEFT: 650px; WIDTH: 300px; POSITION: absolute; TOP: 30px; HEIGHT: 60px;
	;background:#fff;
	ALPHA(OPACITY=50)
	font-size:12px;border:1px solid #999
}
.bar1208_2{
	Z-INDEX: 99; LEFT: 776px; WIDTH: 300px; POSITION: absolute; TOP: 30px; HEIGHT: 60px;
	;background:#fff;
	ALPHA(OPACITY=50)
	font-size:12px;border:1px solid #999
}
.bar1208 ul,
.bar1208_2 ul{
	position: absolute;
	list-style:none;
	margin:0 0 0 10px;
	padding:0;
	float:left;
	z-index:100;
}
.bar1208 ul li,
.bar1208_2 ul li{
	float:left;
	width:40px;
	line-height:30px;
	text-align:center;
	color:#2871BA;
}
.errorCon{ width:500px; margin:0 auto;
}
.errorCon .title { background:url(http://static.v.veimg.cn/image/index/sorryIco.jpg) no-repeat left; padding-left:70px; line-height:60px; font-size:25px; 
}
.errorCon .txt{ padding-left:70px; font-size:14px; line-height:230%; font-size:13px;
}
</style>
</head>

<body>
<div id="wrap">
  <!--header begin-->
  <div id="header">
    <div class="inner_head"><a href="http://www.veryeast.cn/"><img class="logo" src="http://static.v.veimg.cn/image/index/logo2.jpg" border="0" /></a>
      <div class="top">
        <div class="sitenav1"><a href="http://www.veryeast.cn">首页</a> | <a href="http://my.veryeast.cn/searchjob.asp">找工作</a> | <a href="http://vip.veryeast.cn">企业服务</a> | <a href="http://www.jobbon.cn/">猎头</a> | <a href="http://campus.veryeast.cn/">校园招聘</a> | <a href="#" class="last" onmouseover="popdiv()">地区招聘</a><img src="http://static.v.veimg.cn/image/job/my/images/arrow.gif" /></div>
      </div>
    </div>
  </div>

<div class="bar1208" id = "HeadAreaPopDv" onmouseover="popdiv()" onmouseout="cleanDiv()" style="display:none">
<ul>
	<li><a href="http://bj.veryeast.cn/" onclick='cleanDiv()' target="_blank">北京</a></li>
	<li><a href="http://sh.veryeast.cn/" onclick='cleanDiv()' target="_blank">上海</a></li>
	<li><a href="http://zj.veryeast.cn/" onclick='cleanDiv()' target="_blank">浙江</a></li>
	<li><a href="http://gd.veryeast.cn/" onclick='cleanDiv()' target="_blank">广东</a></li>
	<li><a href="http://sd.veryeast.cn/" onclick='cleanDiv()' target="_blank">山东</a></li>
	<li><a href="http://ln.veryeast.cn/" onclick='cleanDiv()' target="_blank">辽宁</a></li>
	<li><a href="http://hn.veryeast.cn/" onclick='cleanDiv()' target="_blank">海南</a></li>
	<li><a href="http://hun.veryeast.cn/" onclick='cleanDiv()' target="_blank">湖南</a></li>
	<li><a href="http://hb.veryeast.cn/" onclick='cleanDiv()' target="_blank">湖北</a></li>
	<li><a href="http://heb.veryeast.cn/" onclick='cleanDiv()' target="_blank">河北</a></li>
	<li><a href="http://fj.veryeast.cn/" onclick='cleanDiv()' target="_blank">福建</a></li>
	<li><a href="http://js.veryeast.cn/" onclick='cleanDiv()' target="_blank">江苏</a></li>
	<li><a href="http://sc.veryeast.cn/" onclick='cleanDiv()' target="_blank">川渝</a></li>
	<li>[<a href="#" onclick='cleanDiv()'><font color="#ff9900">关闭</font></a>]</li>
</ul><div class="clear"></div>
</div>
<script>
var flag=true;
function popdiv()
{
    if(flag)
    {
	if(window.screen.width >1024 ){
		document.getElementById("HeadAreaPopDv").className =  "bar1208_2";
	}else if (window.screen.width <1025){
		document.getElementById("HeadAreaPopDv").className =  "bar1208";
	}
	document.getElementById("HeadAreaPopDv").style.display = "block";
    flag=false;
    }
    
}
function cleanDiv()
{
    //清除节点
	document.getElementById("HeadAreaPopDv").style.display = "none";
    flag=true;
    
}
</script>

  <!--Login begin-->
  <div class="line"></div>
  <div id="LoginBody1">
      <div class="regiShow">
        <div class="errorCon">
          <div class="title">对不起，没有找到你所请求的页面 </div>
          <div class="txt">
            <p>你要查看的网页可能已被删除、名称已被更改，或者暂时不可用。</p>
            <p>错误类型：HTTP 404 - 未找到文件</p>
            <p>请尝试以下操作：</p>
            <p>如果你已经在地址栏中输入该网页的地址，请确认其拼写正确。</p>
            <p>打开 <a href="http://www.veryeast.cn/" target="_blank">www.veryeast.cn</a> 主页，然后查找指向你感兴趣信息的链接。</p>
            <p>单击后退按钮，尝试其他链接。</p>
          </div>
        </div>
      </div>
  </div>
  
  <!--footer begin-->
  <div class="clear"></div>
  <div id="footer">
    <p><a href="http://corp.veryeast.cn/">关于我们</a> |
			<a href="http://corp.veryeast.cn/contact/">联系我们</a> |
			<a href="http://corp.veryeast.cn/link/">友情链接</a> |
			<a href="http://corp.veryeast.cn/ads/">广告服务</a> |
			<a href="http://corp.veryeast.cn/product/">产品与服务</a> |
			<a href="http://corp.veryeast.cn/question/">常见问题</a> |
			<a href="http://corp.veryeast.cn/service/" target="_blank">客服中心</a></p>
    <p>Copyright &copy;2003-2011 VeryEast.Cn All Rights Reserved </p>
    <p>浙B2-20090034</p>
  </div>
</div>
</body>
</html>