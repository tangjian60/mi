<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use mi\assets\AppAsset;
use communal\widgets\Alert;
use mi\modules\credit\models\MiCategory;
$this->registerCssFile('@credit_s/css/index_2mian.css');  
yii::$app->params['title'] = "$MiCategory->title-76mi.com－我的上网主页";

?>
<div class="top">
	<div class="mid">
		<ul class="fl topmid_left">
			<li id="top_mainweb">当前位置：<a href="/">主页</a><<?=$MiCategory->title?></li>
		</ul>
		<ul class="fr topmid_right">
			<li><a href="/yijian" id="topFirst_a">意见反馈</a></li>
			<li><a href="/index/shouyexiufu">设为首页</a></li>
			<li><a href="/">返回首页</a></li>
		</ul>
	</div>
</div> <!-- 顶部 -->
<div class="dh box_shawdow">
	<div class="dh_main ">
		<div class="dh_logo fl">
			<a href = "/" ><img src="<?= Url::to('@web/credit_s/img/logo.gif', true) ?>" alt=""></a>
		</div>
		<div class="dh_search fl">
			<input class="ipt_search" type="text" placeholder="请输入">
			<a href="https://www.baidu.com"><button class="btn_search">搜索</button></a>
		</div>
		<div class="dh_right fr">
			<a class="right_nav" href=""> <span class="glyphicon glyphicon-star"></span>网络小说大全</a>
			<a class="right_read" href=""> <span class="glyphicon glyphicon-time"></span>阅读记录</a>
			<div class="down_manu" style="display:none;">
				<ul>
					<li>暂无阅读记录</li>
				</ul>
				<p>
					<a href="">清楚历史记录</a>
				</p>
			</div>
		</div>
	</div>
</div><!-- 搜索部分 -->
<div class="mid_content">
	<div class="mid_main">
		<h4><?=$MiCategory->title?>网站</h4>
		<ul>
		<?php 
			$MiCategory = MiCategory::find()->where('category_id='.$MiCategory->id)->all();
			foreach($MiCategory as $key=>$value):?>
			<li><a href="<?=$value['url']?>"><?=$value['title']?></a></li>
			<?php endforeach;?>
		<?php ?>
			
		</ul>
		
	</div>
</div><!-- 中间部分 -->

