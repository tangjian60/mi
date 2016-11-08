<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use mi\assets\AppAsset;
use communal\widgets\Alert;
$this->registerCssFile('@credit_s/css/index_2mian.css');  
yii::$app->params['title'] = '76mi导航 -- 首页修复';
$cssString = ".topmid_left{width:20%;}";
$this->registerCss($cssString);
$this->registerJsFile('/assets/9b51c711/jquery.js');
?>
<div class="top">
		<div class="mid">
			<ul class="fl topmid_left">
				<li id="top_mainweb">当前位置：<a href="/">主页</a><首页修复></li>
			</ul>
			<ul class="fr topmid_right">
				<li><a href="#" id="topFirst_a">意见反馈</a></li>
				<li><a href="#">收藏本站</a></li>
				<li><a href="#">设为首页</a></li>
			</ul>
		</div>
	</div> <!-- 顶部 -->
	<div class="dh box_shawdow">
		<div class="dh_main ">
			<div class="dh_logo fl">
				<img src="<?= Url::to('@web/credit_s/img//logo.gif', true) ?>" alt="">
			</div>
			<h3 class="fl dh_h3">帮助中心</h3>
			<div class="dh_search fl">
				<input class="ipt_search" type="text" placeholder="请输入书名/作者/标签">
				<a href="https://www.baidu.com"><button class="btn_search">搜索</button></a>
			</div>
			
		</div>
	</div><!-- 搜索部分 -->
	<div class="xiufu_main">
		<div class="xiufu_left fl">
			<ul>
				<li style="background-color:orange;"><a href="">首页修复</a></li>
				<!-- <li><a href="">广告投放</a></li> -->
				<li><a href="/index/aboutus">关于我们</a></li>
			</ul>
		</div>
		<div class="xiufu_right fl">
			<h3>我的主页改篡改了，不能自己更改怎么办？</h3>
			<h4>一、工具修复：推荐使用安装以下工具修复</h4>
			<p>金山卫士76mi 专版：http://tool.76mi.com/downloads/setup_76mi.exe</p>
			<h4>二、使用修复工具锁定主页</h4>
			<p>使用360软件锁定76mi.com设为主页</p>
			<p>操作路径：打开360安全卫士界面 —> 点击“网盾” —> 点击ie保护开启 —> 输入锁定网址 —> “确定”即可 图文教程↓</p>
			<h4>三、将“76mi 导航”设置为快捷方式放到桌面上</h4>
			<p>打开www.76m.com网址导航首页，在任意空白处右键创建快捷方式到桌面。 图文教程↓</p>
		</div>
	</div><!-- 中间部分 -->
