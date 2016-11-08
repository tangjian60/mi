<?php
use yii\helpers\Url;
use yii\helpers\Html;
use communal\widgets\Alert;
use yii\widgets\ActiveForm;
$this->registerCssFile('@credit_s/css/index_2mian.css');  
yii::$app->params['title'] = '76mi导航 -- 意见';
$this->registerJsFile('/assets/9b51c711/jquery.js');
$this->registerJsFile('@credit_s/js/pagination.js');
?>
<div class="top">
	<div class="mid">
		<ul class="fl topmid_left">
			<li id="top_mainweb">当前位置：<a href="/">主页</a><意见</li>
		</ul>
		<ul class="fr topmid_right">
			<li><a href="#" id="topFirst_a">意见反馈</a></li>
			<li><a href="/index/shouyexiufu">设为首页</a></li>
			<li><a href="/">返回首页</a></li>
		</ul>
	</div>
	
</div> <!-- 顶部 -->
<div class="dh box_shawdow">
	<div class="dh_main ">
		<div class="dh_logo fl">
			<img src="<?= Url::to('@web/credit_s/img/logo.gif', true) ?>" alt="">
		</div>
		<div class="dh_search fl">
			<input class="ipt_search" type="text" placeholder="搜索">
			<a href="https://www.baidu.com"><button class="btn_search">搜索</button></a>
		</div>
		
	</div>
</div><!-- 搜索部分 -->
<div class="yjContent">
<div class="span19">
	<!-- 建议反馈模块 -->
	<div id="feedback" class="gmodule">
	<div class="feedback-bd">
        <div id="tips">
        	<div id="tipcontent"></div>
        </div>
			<h2 class="g-areatit">建议反馈</h2>
			<p class="welcome">您好，欢迎您给我们提出使用中遇到的问题或建议！留下联系方式，将有机会获得精美礼品！</p>
			
		    <?php $form = ActiveForm::begin([
		    		'id' => 'feedbackfrm','method' => 'post','options' => ['class'=>'form','target' => '_self'],
		    		'fieldConfig' => [  //统一修改字段的模板
		    				'template' => '<h3 class="tit">{label}:</h3>
                                                    <div  style="pwidth: 440px">{input}<span class="tip"><span class="adron">*</span>必填</span></div>',
		    		]
		    ]); ?>
		    <?= $form->field($model, 'content')->textarea(['maxlength' => true,'style' => 'width:408px']) ?>
		
		    <?= $form->field($model, 'contact')->textInput(['style' => 'width:440px']) ?>
		
		
		    <div class="item contact">
		        <?= Html::submitButton('马上提交', ['class' =>'submit','id' => 'submit-feedback' ,'value' => "马上提交"]) ?>
		    </div>
		
		    <?php ActiveForm::end(); ?>
		
		
	</div>	
<!-- 	<div class="feedback-ft">
		<h2 class="g-areatit" id="theme">皮肤常见问题</h2>
		<ul class="problem">		
			<h2 class="g-areatit">其他常见问题</h2>
			<li>
				<h4 class="question">1、如何将76mi安全网址导航设为上网主页？</h4>
				<p class="answer">
                答：不同的浏览器有不同的方式，请根据您使用的上网浏览器，查看具体方法。<br>
                1&gt; <a href="">IE浏览器：</a>在菜单栏中选择工具 → Internet选项，弹出属性对话框。在主页内容中输入<a href="http://76mi.com/">http://76mi.com/</a>，然后点击 确定 即可将76mi安全网址设为主页。<a href="">图示帮助</a><br>
                2&gt; <a href="">76mi</a>在菜单栏打开工具 → 主页设置，弹出主页设置对话框。在主页内容中输入<a href="http://hao.360.cn/">http://76mi.com/</a>，然后点击 确定 即可将360安全网址设为主页。<a href="http://hao.360.cn/fqa_for360se.html">图示帮助</a><br>
                3&gt; 其他浏览器： <a href="">360极速浏览器</a> <a href="http://hao.360.cn/fqa_forsougou.html">搜狗浏览器</a> <a href="http://hao.360.cn/fqa_foraoyou.html">遨游浏览器</a> <a href="http://hao.360.cn/fqa_forchrome.html">chrome浏览器</a> <a href="http://hao.360.cn/fqa_forfirefox.html">Firefox浏览器</a>
				</p>
			</li>
			<li>
				<h4 class="question">2、修改了搜索引擎、天气预报的城市、或换了皮肤以后，下次打开又恢复成以前的，怎么办？</h4>
				<p class="answer">
				答：请检查浏览器的设置是否为退出后自动清除cookies。 <a href="">图示帮助</a> 
				</p>
			</li>
            <li>
				<h4 class="question">3、主页字体过大、过小，怎么办？</h4>
				<p class="answer">
				答：您可以尝试以下几种方法。<br>
               	1&gt; CTRL+鼠标滚轮，调节到100% ；<br>
               	2&gt; CTRL+0，直接调整到100%；<br>
               	3&gt; 点击浏览器右下角的放大镜，调节到100%；<br>
               	4&gt; 菜单栏中选择查看  →  网页缩放，选择100%
				</p>
			</li>
            <li>
				<h4 class="question">4、升级浏览器以后收藏夹丢了，怎么办？</h4>
				<p class="answer">
				答： 您可以<a href="">点击这里</a>查看找回方法
				</p>
			</li>
		</ul>
	</div> -->
	</div>
	</div>
	</div><!-- 中间部分 -->
