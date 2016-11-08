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
				<li id="top_mainweb">当前位置：<a href="/">主页</a><关于我们</li>
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
			<li><a href="/index/shouyexiufu">首页修复</a></li>
			<!-- <li><a href="_block">广告投放</a></li> -->
			<li style="background-color:orange;"><a href="">关于我们</a></li>
		</ul>
	</div>
	<div class="aboutus_right fl">
		<h3>关于我们</h3>
		<h4>76mi 是什么</h4>
		<p>　76mi.com网址导航，是一家专注于中国网民的实用网址导航，它提供了国内最齐全的网址导航信息，同时每天收录更新各类新兴的网站，把最便捷有效的网址快速的提供给用户。除此之外，它提供多<a href="">搜索引擎</a>入口、<a href="">生活服务</a>、天气预报、<a href="">邮箱登录</a>等上网常用服务，为用户提供最快捷高效的导航帮助，是您上网的首选导航。76mi导航网站发展至今，已覆盖千万网民，日均数百万访问人次，包含网址10万余个，各类查询和<a href="">生活服务</a>功能500余款，导航内容从网址、电影到音乐、小说等基本涵盖了您上网冲浪的方方面面。除此之外，76mi还支持用户自己定制您的上网首页，用户只要<a href="">注册76mi</a>并登陆后，即可定制自己的首页并<a href="">设置上网首页</a>。创新进取，不断提升用户体验是76mi网址导航的发展理念，用户对76mi有任何疑问或者建议，请<a href="">点击这里提交</a>。</p>
	</div>
</div>
</div><!-- 中间部分 -->
<?php $this->beginBlock('test') ?>  
		$(".foot_totop").click(function(){
			var speed=150;
			$('body').animate({scrollTop:0},speed);
		})//点击按钮返回顶部
<?php $this->endBlock() ?>  
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?> 