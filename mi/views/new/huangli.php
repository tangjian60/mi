<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use mi\assets\AppAsset;
use communal\widgets\Alert;
$this->registerCssFile('@credit_s/css/index_2mian.css');  
$this->registerCssFile('@credit_s/css/wnl.css');
yii::$app->params['title'] = '76mi导航 -- 导航';
$this->registerJsFile('/assets/9b51c711/jquery.js');
$this->registerJsFile('@credit_s/js/hul.js');
$cssString = ".topmid_left{width:16%;} *{font-size:12px;} #mohe-rili .mh-rili-widget .mh-month-control .mh-control{width:35px;} #mohe-rili .mh-rili-widget .mh-month{width:30px;}";
$this->registerCss($cssString);
?>

<div class="top">
	<div class="mid">
		<ul class="fl topmid_left">
			<li id="top_mainweb">当前位置：<a href="/">主页</a><万年历</li>
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
			<a href = "/" ><img src="<?= Url::to('@web/credit_s/img//logo.gif', true) ?>" alt=""></a>
		</div>
		<h3 class="fl dh_h3">万年历</h3>
		<div class="dh_search fl">
			<input class="ipt_search" type="text" placeholder="请输入书名/作者/标签">
			<a href="https://www.baidu.com"><button class="btn_search">搜索</button></a>
		</div>
		
	</div>
</div><!-- 搜索部分 -->
<div class="dateNav">
<div id="main">

<ul id="m-result" class="result"><li id="first" class="res-list">
<div id="mohe-rili" class="g-mohe"  data-mohe-type="rili">

<div class="mh-rili-wap mh-rili-only " data-mgd='{"b":"rili-body"}'>
	<div class="mh-tips">
		<div class="mh-loading">
			<i class="mh-ico-loading"></i>正在为您努力加载中...
		</div>
		<div class="mh-err-tips">亲，出了点问题~ 您可<a href="#reload" class="mh-js-reload">重试</a></div>
	</div>
	<div class="mh-rili-widget">
								
<div class="mh-doc-bd mh-calendar">
	<div class="mh-hint-bar gclearfix">
		<div class="mh-control-bar">
			<div class="mh-control-module mh-year-control mh-year-bar">
				<a href="#prev-year" action="prev" class="mh-prev" data-md='{"p":"prev-year"}'></a>
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-year" val=""></div>
				</div>
				<a href="#next-year" action="next" class="mh-next" data-md='{"p":"next-year"}'></a>
				<ul class="mh-list year-list" style="display:none;" data-md='{"p":"select-year"}'></ul>
			</div>
			<div class="mh-control-module mh-month-control mh-mouth-bar">
				<a href="#prev-month" action="prev" class="mh-prev" data-md='{"p":"prev-month"}'></a>
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-month" val=""></div>
				</div>
				<a href="#next-month" action="next" class="mh-next" data-md='{"p":"next-month"}'></a>
				<ul class="mh-list month-list" style="display:none;" data-md='{"p":"select-month"}'></ul>
			</div>
			<div class="mh-control-module mh-holiday-control mh-holiday-bar">
				<div class="mh-control">
					<i class="mh-trigger"></i>
					<div class="mh-field mh-holiday"></div>
				</div>
				<ul class="mh-list" style="display:none;" data-md='{"p":"select-holiday"}'></ul>
			</div>
			<div class="mh-btn-today" data-md='{"p":"btn-today"}'>返回今天</div>
		</div>
		<div class="mh-time-panel">
			<dl class="gclearfix">
				<dt class="mh-time-monitor-title">北京时间:</dt>
				<dd class="mh-time-monitor"></dd>
			</dl>
		</div>
	</div>
	<div class="mh-cal-main">
		<div class="mh-col-1 mh-dates">
			<ul class="mh-dates-hd gclearfix">
				<li class="mh-days-title">一</li>
				<li class="mh-days-title">二</li>
				<li class="mh-days-title">三</li>
				<li class="mh-days-title">四</li>
				<li class="mh-days-title">五</li>
				<li class="mh-days-title mh-weekend">六</li>
				<li class="mh-days-title mh-last mh-weekend">日</li>
			</ul>
			<ol class="mh-dates-bd"></ol>
		</div>
		<div class="mh-col-2 mh-almanac">
			<div class="mh-almanac-base mh-almanac-main"></div>
			<div class="mh-almanac-extra gclearfix" style="display:none;">
				<div class="mh-suited">
					<h3 class="mh-st-label">宜</h3>
					<ul class="mh-st-items gclearfix"></ul>
				</div>
				<div class="mh-tapu">
					<h3 class="mh-st-label">忌</h3>
					<ul class="mh-st-items gclearfix"></ul>
				</div>
			</div>
			
	
		</div>
	</div>
</div>

<span id="mh-date-y" style="display:none;">2016</span>

			</div>
</div>

<div class="mh-rili-foot">
	</div>
<select class="mh-holiday-data" style="display:none;">
	<option value="0" data-desc="" data-gl="">放假安排</option>
			<option value="抗战胜利纪念日" data-desc="9月3日至5日放假调休，共3天。9月6日（星期日）上班。" data-gl="">抗战胜利纪念日</option>
			<option value="国庆节" data-desc="10月1日至7日放假调休，共7天。10月10日（星期六）上班。" data-gl="">国庆节</option>
			<option value="中秋节" data-desc="9月27日放假。" data-gl="">中秋节</option>
			<option value="端午节" data-desc="6月20日放假，6月22日（星期一）补休。" data-gl="">端午节</option>
			<option value="劳动节" data-desc="5月1日放假，与周末连休。" data-gl="">劳动节</option>
			<option value="清明节" data-desc="4月5日放假，4月6日（星期一）补休。" data-gl="">清明节</option>
			<option value="春节" data-desc="2月18日至24日放假调休，共7天。2月15日（星期日）、2月28日（星期六）上班。" data-gl="">春节</option>
			<option value="元旦" data-desc="1月1日至3日放假调休，共3天。1月4日（星期日）上班。" data-gl="">元旦</option>
	</select>
      <!--value获取当前PHP服务器时间-->
<input type="hidden" id="mh-rili-params" value="action=query&year=<?= date('Y');?>&month=<?= date('m');?>&day=<?= date('d');?>" />

</div>
<?php $this->beginBlock('test') ?>  
<!-- /** -->
<!--  * 描述：本脚本是从360好搜扒下来的，别说我如何如何无耻，360扒的百度，百度扒的谷歌，就是这么屌！ -->
<!--     rili-widget 所包含的JS文件们 -->
<!--  * 共包含15个JS文件，由于彼此间存在依赖关系，它们的顺序必须依次是： -->
<!--  *		1.jquery-ui-1.10.3.custom -->
<!--  *		2.msg_config	// 配置事件消息 -->
<!--  * -->
<!--  *		3.mediator	  //库，基于事件的异步编程 -->
<!--  *		4.calendar    //日历类 -->
<!--  *		5.lunar       //农历 -->
<!--  * -->
<!--  *		6.cachesvc    //window. appdata依赖它 -->
<!--  *		7.appdata     //window. 时间矫正 -->
<!--  *		8.timesvc     //window.TimeSVC  时间同步服务 -->
<!--  * -->
<!--  *		9.huochepiao    //购票（无用） -->
<!--  * -->
<!--  *		10.fakeSelect    //$-ui  年份月份下拉选择器 -->
<!--  *		11.speCalendar   //$-ui 日历单元格的特殊内容 -->
<!--  *		12.webCalendar   //$-ui 日历单元格 -->
<!--  *		13.dayDetail     //$-ui 日历右侧的详情（黄历 忌宜） -->
<!--  * -->
<!--  *		14.xianhao      //注册事件：日历上方的操作工具条：年月日节假日 返回今天 -->
<!--  *		15.dispatcher   //提取参数，初始化日历 -->
<!--  * -->
<!--  * 最后拼接的顺序是 jquery-ui-1.10.3.custom,msg_config,mediator,calendar,lunar,cachesvc,appdata,timesvc,huochepiao,fakeSelect,speCalendar,webCalendar,dayDetail,xianhao,dispatcher -->
<!--  * -->
<!--  * edit by @gaosong 2015-08-31 -->
<!--  * -->
<!--  * 代码从导航日历迁移过来， -->
<!--  */ -->
_loader.remove && _loader.remove("rili-widget");
_loader.add("rili-widget", "http://76mi.com/credit_s/js/wnl.js");//上述JS文件们已让我压缩成wnl.js
_loader.use("jquery, rili-widget", function(){

	var RiLi = window.OB.RiLi;

	var gMsg = RiLi.msg_config,
		dispatcher = RiLi.Dispatcher,
		mediator = RiLi.mediator;

	var root = window.OB.RiLi.rootSelector || '';

	// RiLi.AppData(namespace, signature, storeObj) 为了解决"In IE7, keys may not contain special chars"
	//'api.hao.360.cn:rili' 仅仅是个 namespace
	var timeData = new RiLi.AppData('api.hao.360.cn:rili'),
		gap = timeData.get('timeOffset'),
		dt = new Date(new Date() - (gap || 0));

	RiLi.action = "default";

	var $detail = $(root+'.mh-almanac .mh-almanac-main');
	$detail.dayDetail(dt);

	RiLi.today = dt;

	var $wbc = $(root+'.mh-calendar'); 

	mediator.subscribe(gMsg.type.actionfestival , function (d){
		var holi = RiLi.dateFestival,
			val = d.val ? decodeURIComponent(d.val) : "",
			holiHash = {},
			el,
			node = {};

		for (var i = 0 ; i < holi.length ; ++i){
			el = holi[i];
			el = $.trim(el).split("||");
			if (el.length == 2){
				node = {};
				node.year = el[0].substr(0 , 4);
				node.month = el[0].substr(4 , 2);
				node.day = el[0].substr(6 , 2);
				holiHash[el[1]] = node;
			}
		};

		RiLi.action = "festival";
		
		if (holiHash[val]){
			node.year = holiHash[val].year;
			node.month = holiHash[val].month;
			node.day = holiHash[val].day;

			RiLi.needDay = new Date(parseInt(node.year , 10) , parseInt(node.month ,10) - 1 , node.day);
			$wbc.webCalendar({
				time : new Date(parseInt(node.year , 10) , parseInt(node.month ,10) - 1 , node.day),
				onselect: function(d, l){
					$detail.dayDetail('init', d , l);
				}
			}); 
		}
		else{
			RiLi.action = "default";
		}
	});	

	mediator.subscribe(gMsg.type.actionquery , function (d){
		var strDate;

		if (!d.year || d.year > 2100 || d.year < 1901){
			RiLi.action = "default";
			return 0;
		}
		
		d.month = parseInt(d.month , 10);

		if (d.month &&  (d.month > 12 || d.month < 1)){
			RiLi.action = "default";
			return 0;
		}

		if (!d.month){
			d.month = 1 ;
		}
		
		d.day = parseInt(d.day , 10);

		if (!d.day){
			d.day = 1;
		} 

		RiLi.action = "query";    	
		RiLi.needDay = new Date(parseInt(d.year , 10) , parseInt(d.month ,10) - 1 , d.day);

		$wbc.webCalendar({
			time : new Date(parseInt(d.year , 10) , parseInt(d.month ,10) - 1 , d.day),
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	});

	mediator.subscribe(gMsg.type.actiongoupiao, function (d){
		RiLi.action = "goupiao";
		$wbc.webCalendar({
			time : dt,
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	   
	});

	mediator.subscribe(gMsg.type.actiondefault , function (d){
		RiLi.needDay = dt;
		$wbc.webCalendar({
			time : dt,
			onselect: function(d, l){
				$detail.dayDetail('init', d , l);
			}
		}); 
	});    

	dispatcher.dispatch();

	mediator.subscribe(gMsg.type.dch , function (d){
		// if (RiLi.needDay){
		// 	$wbc.webCalendar("initTime" , RiLi.needDay);
		// }
		// else{
		// 	$wbc.webCalendar("initTime" , RiLi.today);
		// }
		$wbc.webCalendar("initTime" , RiLi.needDay||RiLi.today);
	});   
	
	mediator.publish(gMsg.type.dch ,  dt);

	var nowDate = (new Date()).getTime() ;

	/* 时间矫正 */
	RiLi.TimeSVC.getTime(function(d){
		var trueTime = d.getTime();
		var timeData = new RiLi.AppData('api.hao.360.cn:rili') , isFirst = true;

		if(Math.abs(nowDate - trueTime) > 300000){
			timeData.set('timeOffset', nowDate - trueTime);
		}
		else {
			timeData.remove('timeOffset');
		}

		if (typeof gap == undefined || !isFirst){
			RiLi.today = d;
			mediator.publish(gMsg.type.dch , d);
		}

		isFirst = false;
	});

	//日历初始完后的回调
	if(typeof RiLi.CallBack.afterInit === "function"){
		RiLi.CallBack.afterInit();
	}

});
$(".foot_totop").click(function(){
			var speed=150;
			$('body').animate({scrollTop:0},speed);
		})//点击按钮返回顶部
<?php $this->endBlock() ?>  
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>  

    </li></ul></div>
</div><!-- 中间部分 -->

