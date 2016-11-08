<?php
use yii\helpers\Url;
use mi\modules\credit\models\MiAdvertising;
use mi\modules\credit\models\MiNavigation;
use mi\modules\credit\models\MiCategory;
use mi\assets\AppAsset;
AppAsset::register($this);
/* $this->beginBlock('appCss');
echo Html::cssFile('@web/credit_s/css/main.css');
$this->endBlock('appCss');
$cssString = '';
$this->registerCss($cssString); */
$cssString = ".header_time{ height:70px;} .searchbox{ margin-top:10px;} #qingtiancms_middle_ul_3{
border-bottom:none;} #qingtiancms_middle_ul_0{border-bottom:none;} #qingtiancms_middle_ul_1{border-bottom:none;} #qingtiancms_middle_ul_2{
border-bottom:none;}";
$this->registerCss($cssString);
?>
<?/* =time() */?>
<div class="top"> <!-- 顶部 -->
	<div class="mid">
		<ul class="fl topmid_left">
			<li id="top_mainweb">76mi设为首页</li>
			<div id="top_maindiv">
				<span class="sanjiao"></span>
				<div class="top_miandiv_content">
					<h4>亲，想把76mi导航设为首页？</h4>
					<a href="./shouyexiufu">如何手动设置76mi导航为主页？</a>
				</div>
			</div>
		</ul>
		<ul class="fr topmid_right">
			<li><a href="#" id="topFirst_a">主页</a></li>
			<li><a href="index/shouyexiufu">主页修复</a></li>
			<li><a href="index/yijian">提建议</a></li>
		</ul>
	</div>
</div>
<div class="all">
	<div class="header">
		<div class="logo fl"></div>
		<div class="login fl">
			<div id="joke">
			<?= $joke;?>
			</div>
			<div id="loginDiv">
				<span class="glyphicon glyphicon-envelope fl" id="email_logo"></span>
				<input type="text" placeholder="邮箱账号" id="email_input" class="fl">
				<div id="login_hidDiv">
					<ul>
						<li><select name="" id="login_select">
							<option value="">@163.com网易</option>
							<option value="">@126.com网易</option>
							<option value="">@sina.com</option>
							<option value="">@sohu.com</option>
							</select>
						</li>
						<li>
							<input type="password" id="login_input">
							<button id="login_button">登录</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php $MiAdvertising = MiAdvertising::find()->where('type = 1 and status = 1 and align = 1')->one();
		?>
		<div class="header_ad fl"><a href='<?=$MiAdvertising->url ?>'><img src=".<?=$MiAdvertising->picture ?>"></a></div>
		<div class="header_time fl">
			<ul id="cal">
				<li class="date">
					<a href="http://tool.76mi.com/wnl/" target="_blank">
						<span id="todayNow"></span>
					</a><?= date('y-m-d h:i:s',time());?>
				</li>
				<li class="m">
					<a class=hl href="/new/huangli" target="_blank">黄历</a>
					<a class=yc href="/new/huangli" target="_blank">运程</a>
					<a class="clock"  href="./new/clock" target="_blank" style="width:50px;*width:45px;">闹钟</a>
				</li><iframe allowtransparency="true" frameborder="0" width="148" height="28" scrolling="no" src="//tianqi.2345.com/plugin/widget/index.htm?s=3&z=3&t=1&v=0&d=1&bd=0&k=&f=&q=1&e=0&a=1&c=54511&w=148&h=28&align=center"></iframe></ul>
		</div>
	</div> <!-- 头部 -->
	<div class="searchbox">
		<div class="searchbox_logo">
			<div class="logo_div box_shawdow">
				<img src="<?= Url::to('@web/credit_s/img/hao123.png', true) ?>" alt="" id="logo_img">
				<!-- <span class="glyphicon glyphicon-triangle-bottom fr logo_span"></span> -->
			</div>
			
			<!-- <ul id="logodiv_ul">
				<li><img src="<?= Url::to('@web/credit_s/img/soso.gif', true) ?>" alt="" value="搜搜"></li>
				<li><img src="<?= Url::to('@web/credit_s/img/youdao.gif', true) ?>" alt="" value="有道搜索"></li>
				<li><img src="<?= Url::to('@web/credit_s/img/gougou.gif', true) ?>" alt="" value="狗狗搜索"></li>
				<li><img src="<?= Url::to('@web/credit_s/img/google.gif', true) ?>" alt="" value="谷歌搜索"></li>
				<li><img src="<?= Url::to('@web/credit_s/img/taobaoo.gif', true) ?>" alt="" value="淘宝网"></li>
				<li><img src="<?= Url::to('@web/credit_s/img/sogou.gif', true) ?>" alt="" value="搜狗搜索"></li>
			</ul> -->
		</div>
		<div class="searchbox_right">
			<div class="search fl" id="search_div">
				 <div class="input-group" id="srhbox_div">
				    <div class="searchipt fl">
				    	<div class="search_selct ">网页</div>
				    	<ul id="selectUl">
				    			<li>音乐</li>
				    			<li>视频</li>
				    			<li>图片</li>
				    			<li>贴吧</li>
				    			<li>知道</li>
				    			<li>新闻</li>
				    			<li>地图</li>
				    			<li>网页</li>
				    			<li>更多</li>
				    	</ul>
				    	<input type="text" name="ca" id="searchIpt" placeholder="Search for...">
				    </div>
				    <div class="search_xiala">▼</div>
				    <span class="input-group-btn"  id="searchBtn">
				      <button class="btn btn-default search_btn" type="button" style="height:45px; border:none;background-color:#6caf6c; color:#fff;">百度一下</button>
				     </span>
				</div>
			</div>
			<div class="search_text">
				<?= $hotsearchtop;?>
			</div>
			
			<div class="search_hiddendiv">
				<ul id="search_value">
				<?php $url='https://www.hao123.com/sugdata_s4.json';
				$lines_string=file_get_contents($url);
				$key_string = json_decode($lines_string, true)['keywords'];
				foreach ($key_string as $k=>$valu):
				if($k<11):?>
					<li><a href="https://www.baidu.com/s?word=<?=$valu['keyword']?>" target="_blank"><?=$k+1?>.<?=$valu['keyword']?></a></li>
					<?php endif;endforeach;?>
				</ul>
			</div>
		</div>
	</div> <!-- 搜索框 -->
<div class="content_all" style="width:100%; ">
	<div class="content">
		<div class="leftvar fl">
		<!-- 两个切换箭头 -->
			<div class="activityBox box_shawdow fl ">
					<?= $slide;?>
			</div>
			<div class="leftnav box_shawdow fl">
			<?php
			$MiCategory = MiCategory::find()->where('level=0')->all();
			foreach($MiCategory as $key=>$val):?>
			<div class="leftnav_title"><?= $val['title']?></div>
			<ul class="leftnav_ul">
			<li>
			<?php 		
			$MiCategoryl = MiCategory::find()->where('category_id='.$val['id'])->all();
			$arryl[] = $MiCategoryl;
			foreach($MiCategoryl as $key=>$value):?>
			<a href= '/new?id=<?= $value['id']?>'><?= $value['title']?></a>
			<?php endforeach;?>
				</li>
			</ul>
			<?php endforeach;?>
			</div>
		</div>
		<div class="rightvar fl">
			<div class="right_top box_shawdow">
				<h5 id="daohang">名站导航</h5>
					<?/* = mb_convert_encoding($data,'utf-8','gb2312'); */?>
					<div class="right_topfirst">
					<ul class="cf fmSite bb">
					<?php $navigation = MiNavigation::find()->all();
					foreach ($navigation as $key=>$val){?>
					<li>
					<a href="<?=$val->url ?>" style="background: url(<?= $val->picture ?>) no-repeat 1px;padding-left:20px;"><?= $val->title ?></a>
					</li>
					<?php }?>
					</ul>
					</div>
					<?php $guang = MiAdvertising::find()->where('type = 1 and status = 1 and align = 4')->one();
					?>
				<div><a href='<?=$guang->url ?>'><img src=".<?=$guang->picture ?>"></a></div>
			</div>
			<?/* = mb_convert_encoding($qingtiancms,'utf-8','gb2312'); */ ?>
			<div block="cool-site" class="col">
				<?php 
				foreach($arryl as $key=>$value):if($key==0):?>
				<ul class="colnavi">
				<li><span class="g01"></span><a href="/new?id=18">玩游戏</a><i>|</i></li><li><span class="g02"></span><a href="/new?id=19">看电影</a><i>|</i></li><li><span class="g03"></span><a href="/new?id=21">小说</a><i>|</i></li><li><span class="g04"></span><a href="/new?id=20">新闻</a><i>|</i></li><li><span class="g05"></span><a href="/new?id=16">音乐</a><i>|</i></li><li><span class="g06"></span><a href="/new?id=5">购物</a><i>|</i></li><li><span class="g07"></span><a href="">团购</a><i>|</i></li>
				<li><span style="position: relative;" class="g08"></span> <a href="http://union.click.jd.com/jdc?e=&amp;p=AiIBZRprFDJWWA1FBCVbV0IUEEULRFRBSkAOClBMW0srOUEKcFkrbV52UmwAE10sTFARbCtuOxkOIgZlHVMVAxcAVStrdHAi&amp;t=W1dCFBBFC0RUQUpADgpQTFtL">京东<ins style="display: inline-block;position: absolute;width: 90px;height: 29px;margin-top: 3px;margin-left: 3px;overflow: hidden;" class="tmkt"><img src="/upload/pic/2015422203115778.jpg"></ins></a></li>
				</ul>
				<?php elseif ($key==1):?>
				<ul class="colnavi"><li><a href="/new?id=5">购物</a><i></i></li><li><a href="">图片</a><i></i></li><li><a href="/new?id=26">军事</a><i></i></li><li><a href="">汽车</a><i></i></li><li><a href="/new?id=20">新闻</a><i></i></li><li><a href="/new?id=19">电视</a><i></i></li><li><a href="">动漫</a><i></i></li><li><a href="">体育</a></li></ul>
				<?php elseif ($key==2):?>
				<ul class="colnavi"><li><a href="">天气</a><i></i></li><li><a href="">彩票</a><i></i></li><li><a href="">股票</a><i></i></li><li><a href="">基金</a><i></i></li><li><a href="">银行</a><i></i></li><li><a href="">手机</a><i></i></li><li><a href="">地图</a><i></i></li><li><a href="">健康</a></li></ul>
				<?php else:?>
				<ul class="colnavi"><li><a href="">软件</a><i></i></li><li><a href="">邮箱</a><i></i></li><li><a href="">房产</a><i></i></li><li><a href="">菜谱</a><i></i></li><li><a href="">大学</a><i></i></li><li><a href="">人才</a><i></i></li><li><a href="">交友</a><i></i></li><li><a href="">星座</a></li></ul>
				<?php endif;?>
				<ul id="qingtiancms_middle_ul_<?=$key?>" class="sortSite">
				<?php foreach($value as $k=>$val):if($k<9):?>
				<li class="alt"><h4 class="tit fl"><a href='/new?id=<?= $val['id']?>'><?= $val['title']?></a></h4>
				<span class="more fr"><a target="_blank" href="/new?id=<?= $val['id']?>">更多&gt;&gt;</a></span>
				<?php 
				$MiCate = MiCategory::find()->where('category_id='.$val['id'])->all();
				foreach ($MiCate as $ke=>$vall):if($ke<5):?>
				<a href="<?= $vall['url']?>"><?= $vall['title']?></a>
				<?php endif;endforeach;?>
				</li>
				<?php endif;endforeach;?>
				</ul>
				<?php endforeach;?>
				</div>
		
		</div>
	</div> <!-- 中间部分 -->
	<div class="bottom box_shawdow">
		<ul class="bottom_ul">
			<a href=""><h5>游戏专题:</h5></a>
			<a href="http://www.warcraftchina.com/"><li>魔兽世界</li></a>
			<a href="http://cf.qq.com/"><li>穿越火线</li></a>
			<a href="http://dnf.qq.com/"><li>地下城与勇士</li></a>
			<a href="http://www.7k7k.com/tag/404/"><li>斗地主</li></a>
			<a href="http://www.2144.cn/html/85/"><li>连连看</li></a>
			<a href="http://17roco.qq.com/"><li>洛克王国</li></a>
			<a href="http://77.198game.com/"><li>幻想三国</li></a>
			<a href="http://xyq.163.com/"><li>梦幻西游</li></a>
			<a href="http://www.4399.com/flash/18012.htm"><li>植物大战僵尸</li></a>
			<a href="http://popkart.tiancity.com/homepage/v2/"><li>跑跑卡丁车</li></a>
			<a href="http://bnb.sdo.com/web5/home/home.asp"><li>泡泡堂</li></a>
			<a href="http://www.76mi.com/game.htm"><li style="color:#777; font-size: 12px; width:40px;text-align:right;">更多>></li></a>
		</ul>
		<ul class="bottom_ul">
			<a href=""><h5>好站推荐:</h5></a>
			<a href="http://www.pcauto.com.cn/"><li>太平洋汽车</li></a>
			<a href="http://www.mogujie.com/"><li>蘑菇街女性</li></a>
			<a href="http://t.dianping.com/citylist"><li>团购大全</li></a>
			<a href="http://www.appchina.com/"><li>掌上应用汇</li></a>
			<a href="http://www.xilu.com/"><li>西陆军事</li></a>
			<a href="http://www.kimiss.com/?dh8866"><li>闺蜜网</li></a>
			<a href="http://www.liketry.com/"><li>喜试网</li></a>
			<a href="http://www.kaba365.com/0.asp"><li>卡巴斯基</li></a>
			<a href="http://www.iautos.cn/city-hangzhou/?dh8866"><li>第一车网</li></a>
			<a href="http://www.chinapet.net/"><li>中国宠物</li></a>
			<a href="http://www.docin.com/"><li>豆丁网</li></a>
			<a href="http://www.76mi.com/qtwz.htm"><li style="color:#777; font-size: 12px; width:40px;text-align:right;">更多>></li></a>
		</ul>
		
		<?php $Under = MiAdvertising::find()->where('type = 1 and status = 1 and align = 5')->one();
					?>
		<div class="bottom_div"><span class='bottom_guanbi'>关闭</span><img src=".<?=$Under->picture ?>"></div>
	</div> <!-- 底部 -->
	
</div>
</div>
<!-- 两边浮动广告 -->
<div class="left_guanggao" style="display:none;">
	<?php $left = MiAdvertising::find()->where('type = 2 and status = 1 and align = 2')->one();
		?>
	<img src=".<?=$left->picture ?>">
	<span class="left_guanbi">
		<img src="<?= Url::to('@web/credit_s/img/close.png', true) ?>" alt="">
	</span><!-- 关闭按钮 -->
</div>
<div class="right_guanggao" style="display:none;">
	<?php $right = MiAdvertising::find()->where('type = 2 and status = 1 and align = 3')->one();
		?>
	<img src=".<?=$right->picture ?>">
	<span class="right_guanbi">
		<img src="<?= Url::to('@web/credit_s/img/close.png', true) ?>" alt="">
	</span><!-- 关闭按钮 -->
</div>
<!-- 底部固定广告位 -->
<?php $bottom = MiAdvertising::find()->where('type = 1 and status = 1 and align = 6')->one();
?>
<div class="bottom_guanggao"><a href='<?=$bottom->url ?>'><img src=".<?=$bottom->picture ?>"></a>
<span class="btm_close">关闭</span>
</div>
