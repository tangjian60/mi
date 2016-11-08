<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use mi\assets\AppAsset;
use communal\widgets\Alert;
$this->registerCssFile('@credit_s/css/index_2mian.css');  
yii::$app->params['title'] = "76mi.com－我的上网主页";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE>闹钟功能</TITLE>
<META content="text/html; charset=gbk" http-equiv=Content-Type>
<STYLE>BODY {
	LINE-HEIGHT: 180%; BACKGROUND: #fff; COLOR: #333; FONT-SIZE: 14px
}
* {
	PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; FONT-FAMILY: "宋体", Arial; WORD-WRAP: break-word; TABLE-LAYOUT: fixed; WORD-BREAK: normal; PADDING-TOP: 0px
}
H1 {
	FONT-SIZE: 100%
}
H2 {
	FONT-SIZE: 100%
}
H3 {
	FONT-SIZE: 100%
}
H4 {
	FONT-SIZE: 100%
}
H5 {
	FONT-SIZE: 100%
}
H6 {
	FONT-SIZE: 100%
}
IMG {
	BORDER-BOTTOM: 0px; BORDER-LEFT: 0px; VERTICAL-ALIGN: middle; BORDER-TOP: 0px; BORDER-RIGHT: 0px
}
LI {
	LIST-STYLE-TYPE: none; LIST-STYLE-IMAGE: none
}
A {
	COLOR: #0e6dbc; TEXT-DECORATION: none
}
#clockT {
	LINE-HEIGHT: 180%; MARGIN: 10px auto 0px; WIDTH: 320px; BACKGROUND: url(<?= Url::to('@web/credit_s/img/bgcon.gif', true) ?>) no-repeat center top; FONT-SIZE: 12px
}
#clockT .close {
	PADDING-BOTTOM: 12px; PADDING-LEFT: 0px; WIDTH: 100%; PADDING-RIGHT: 0px; PADDING-TOP: 6px; HEIGHT:20px
}
#clockT .close IMG {
	DISPLAY: inline; FLOAT: right; MARGIN-RIGHT: 6px
}
#clockT .conBox {
	WIDTH: 100%; MARGIN-BOTTOM: -10px; BACKGROUND: url(<?= Url::to('@web/credit_s/img/bgcon.gif', true) ?>) no-repeat center bottom; CLEAR: both; _margin-bottom: 0px
}
#clockT .cloBox {
	MARGIN: 0px auto; WIDTH: 308px
}
#clockT .point {
	PADDING-BOTTOM: 10px; MARGIN: 6px auto 0px; WIDTH: 278px; COLOR: #0e6dbc
}
#clockT .todays {
	BORDER-BOTTOM: #b4d6f4 1px solid; TEXT-ALIGN: center; BORDER-LEFT: #b4d6f4 1px solid; PADDING-BOTTOM: 3px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; BACKGROUND: #fff; FONT-SIZE: 14px; BORDER-TOP: #b4d6f4 1px solid; BORDER-RIGHT: #b4d6f4 1px solid; PADDING-TOP: 6px
}
#clockT .todays A {
	FONT-SIZE: 14px
}
#clockT .tools {
	BORDER-BOTTOM: #afd2f0 1px solid; BORDER-LEFT: #afd2f0 1px solid; PADDING-BOTTOM: 6px; PADDING-LEFT: 18px; PADDING-RIGHT: 18px; BACKGROUND: #fff; BORDER-TOP: #afd2f0 1px solid; BORDER-RIGHT: #afd2f0 1px solid; PADDING-TOP: 6px
}
#clockT .tools TD {
	PADDING-BOTTOM: 5px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; VERTICAL-ALIGN: top; PADDING-TOP: 5px
}
#clockT .tools TD * {
	VERTICAL-ALIGN: text-bottom
}
#clockT .tools TEXTAREA {
	PADDING-BOTTOM: 6px; PADDING-LEFT: 6px; WIDTH: 178px; PADDING-RIGHT: 6px; HEIGHT: 60px; PADDING-TOP: 6px
}
#clockT .tagBox {
	POSITION: relative; MARGIN-TOP: 5px; WIDTH: 100%; MARGIN-BOTTOM: -1px; CLEAR: both
}
#clockT .tagBox LI {
	TEXT-ALIGN: center; LINE-HEIGHT: 22px; WIDTH: 88px; DISPLAY: inline; BACKGROUND: url(../../static/images/tagimg.gif) no-repeat left top; FLOAT: left; HEIGHT: 22px; MARGIN-RIGHT: 3px; PADDING-TOP: 4px
}
#clockT .tagBox LI.at {
	BACKGROUND-POSITION: right top
}
#clockT .listC {
	WIDTH: 100%; CLEAR: both
}
#clockT .listC LI {
	BORDER-BOTTOM: #f7eeb1 1px solid; BORDER-LEFT: #f7eeb1 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; MARGIN-BOTTOM: 3px; BACKGROUND: #fffce6; BORDER-TOP: #f7eeb1 1px solid; BORDER-RIGHT: #f7eeb1 1px solid; PADDING-TOP: 0px
}
#clockT .listC LI IMG {
	MARGIN-TOP: 6px; FLOAT: right; MARGIN-RIGHT: 5px
}
#clockT .listC LI A {
	COLOR: #004dcc
}
#clockT .time {
	TEXT-ALIGN: center; LINE-HEIGHT: 57px; WIDTH: 100%; FONT-FAMILY: Arial, Helvetica, sans-serif; BACKGROUND: url(../../static/images/bg_orange.gif) no-repeat center 50%; COLOR: #338500; FONT-SIZE: 36px
}
#clockT .tagBox:after {
	DISPLAY: block; HEIGHT: 0px; VISIBILITY: hidden; CLEAR: both; CONTENT: ""
}
#clockT .close:after {
	DISPLAY: block; HEIGHT: 0px; VISIBILITY: hidden; CLEAR: both; CONTENT: ""
}
#clockT .tools:after {
	DISPLAY: block; HEIGHT: 0px; VISIBILITY: hidden; CLEAR: both; CONTENT: ""
}
</STYLE>

<META name=GENERATOR content="MSHTML 8.00.7600.16671"></HEAD>
<BODY>
<DIV id=clockT>
<DIV class=close></DIV>
<DIV class=conBox>
<DIV class=cloBox>
<DIV class=todays>当前时间:<?= date('y-m-d h:i:s',time());?></DIV>
<UL class=tagBox>
  <LI class=at>定时闹钟</LI></UL>
<DIV class=tools>
<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
  <TBODY>
  <TR>
    <TD width="28%">提醒时间：</TD>
    <TD width="72%"><LABEL><SELECT name=select> <OPTION 
      selected>23</OPTION></SELECT> </LABEL>时 <LABEL><SELECT name=select2> 
        <OPTION selected>45</OPTION></SELECT> </LABEL>分 </TD></TR>
  <TR>
    <TD>闹钟铃声：</TD>
    <TD><LABEL><SELECT name=select3><OPTION selected 
        value=../media/0001.MID>梦里水乡</OPTION><OPTION 
        value=../media/0002.MID>思念</OPTION><OPTION 
        value=../media/0023.MID>爱的奉献</OPTION><OPTION 
        value=../media/0024.MID>纤夫的爱</OPTION><OPTION 
        value=../media/0005.MID>走西口</OPTION><OPTION 
        value=../media/0030.MID>飘流</OPTION><OPTION 
        value=../media/0038.MID>祝你平安</OPTION><OPTION 
        value=../media/0008.MID>回到拉萨</OPTION><OPTION 
        value=../media/0049.MID>一路上有你</OPTION><OPTION 
        value=../media/0010.MID>雾里看花</OPTION><OPTION 
        value=../media/0011.MID>中华民谣</OPTION><OPTION 
        value=../media/0050.MID>爱拼才会赢</OPTION><OPTION 
        value=../media/0046.MID>戏梦</OPTION><OPTION 
        value=../media/0014.MID>九佰九拾九朵玫瑰</OPTION><OPTION 
        value=../media/0015.MID>我被青春撞了一下腰</OPTION><OPTION 
        value=../media/0016.MID>我很丑 可是我很温柔</OPTION><OPTION 
        value=../media/0047.MID>真心英雄</OPTION><OPTION 
        value=../media/0018.MID>同桌的你</OPTION></SELECT><BUTTON id=alarm_music_button 
      type=submit>试听</BUTTON></LABEL></TD><BGSOUND id=alarm_player loop=1 
    autostart="true"></TR>
  <TR>
    <TD>提示文字：</TD>
    <TD><INPUT value=休息，休息一下！ maxLength=40 name=alarm_textarea> </TD></TR>
  <TR>
    <TD>重复提醒：</TD>
    <TD><LABEL for=noRepeat><INPUT id=noRepeat value=1 CHECKED type=radio 
      name=alarm_is_single> 不重复</LABEL><LABEL for=repeatDaily> <INPUT 
      id=repeatDaily value=0 type=radio name=alarm_is_single> 每天提醒</LABEL> 
</TD></TR>
  <TR>
    <TD>&nbsp;</TD>
    <TD><A onclick="qihoo.alarm.confirmed();return false;" 
      href="./clock.htm#confirmed"><IMG 
      alt=确定添加闹钟 src="../../static/images/btn.gif" width=142 
height=41></A></TD></TR></TBODY></TABLE>
<UL class=listC></UL></DIV>
<DIV class=point>
<H1>温馨提示：</H1>
<P>·如果关闭这个页面，闹钟功能将失效。</P>
<P>·需要打开音响或佩戴耳机，以便听到提示铃声。</P></DIV></DIV></DIV></DIV>
<SCRIPT type=text/javascript>
function getCookie(name){var arr; var reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg)){return unescape(arr[2]);}else{return null;}}
function setCookie(name,value){var expire = arguments[2] ? arguments[2] : 365*24*60*60*1000;var exp = new Date();exp.setTime(exp.getTime() + expire);document.cookie = name + "="+ escape (value) + ";path=/;expires=" + exp.toGMTString();}</SCRIPT>

<SCRIPT type=text/javascript src="jquery-clock.js"></SCRIPT>

<SCRIPT type=text/javascript>
var qihoo = qihoo || {};
qihoo.alarm = qihoo.alarm || {};
jQuery.extend(qihoo.alarm , {
    second_handler_timeshow : null,//一秒一次的把手
    external_time_handler:null,//一秒一秒地外部把手
    unique_id:'xxadboo',//唯一 id Cookie
    alarm_array: new Array(),
    
    init : function(){
        //从 Cookie 中得到闹钟信息，之后结构化后赋值
        qihoo.alarm.unserialize_from_cookie();

        //关闭按钮
        $('#clockT').children('.close').children().bind('click' , function(){
            qihoo.alarm.close();
            return false;
        });

		/*
       if(qihoo.alarm.alarm_array.length == 0){
            $('a[href=#displayClock] b').removeClass('clo');
       }else{      
           $('a[href=#displayClock] b').addClass('clo');
       };*/
       qihoo.alarm.bind_music();//绑定时间 


       $('select[name=select3]').bind('change' , function(){
                //qihoo.alarm.stop_music();
                qihoo.alarm.playing_music();

                /*
                $('#alarm_music_button').text('停止');
                $('#alarm_player').attr('src' , '');
                $('#alarm_player').attr('src' , $('select[name=select3]').val());
                */

                return false;
       });


    },
    
    //显示功能
    display:function(){
        //开始跑一秒一次，显示时间用
        try{
            clearInterval(qihoo.alarm.second_handler_timeshow);
        }catch(e){}
        
 
        qihoo.alarm.second_handler_timeshow = setInterval('qihoo.alarm.sht_hook();',1000);
       
        qihoo.alarm.sht_hook();//当前时间
        qihoo.alarm.set_alarm_time();//设置闹铃时间
        qihoo.alarm.bind_event();//绑定事件

        $('#clockT').show();
    },

    bind_event:function(){
        //qihoo.alarm.alarm_array.push(kk);    
       $('ul.listC').empty();
	   /*
       if(qihoo.alarm.alarm_array.length == 0){
            $('a[href=#displayClock] b').removeClass('clo');
            return false;
       }*/
       
       //$('a[href=#displayClock] b').addClass('clo');

        $.each(qihoo.alarm.alarm_array , function(k,v){
            //时间  歌曲    提示语  是否单次提醒
            k++;
            
            var hour = new Date(parseInt(v[0])).getHours();
            var minute = new Date(parseInt(v[0])).getMinutes();
            
            if(hour < 10)  {hour  = '0' + hour.toString()}
            if(minute < 10){minute= '0' + minute.toString()}
            
            var is_single = v[3] ? '单次提醒':'重复提醒';
            
            v[2] = decodeURIComponent(v[2]);
            var content = '<li><a href="#" onclick="qihoo.alarm.cancel_alarm('+(k-1)+');return false;"><img src="../../static/images/clo_ico.gif" width="8" height="8" border="0" /></a><strong>'+k+'</strong> '+is_single+'：'+hour+':'+minute+' <a href="#" onclick="return false;" alt="'+qihoo.alarm.htmlspecialchar(v[2])+'">'+qihoo.alarm.htmlspecialchar(v[2].substr(0,9))+'</a></li>';

            $('ul.listC').append(content);
        });
    },

    cancel_alarm:function(id){
        id = parseInt(id);

        var ee = new Array();

        $.each(qihoo.alarm.alarm_array , function(k,v){
            if(k != id){
                ee.push(v);
            }
        });

        qihoo.alarm.alarm_array = ee;

        qihoo.alarm.bind_event();//绑定事件
        qihoo.alarm.serialize_to_cookie();//存储
    },

    htmlspecialchar:function(ee){//山寨 PHP 的 htmlspecialchar 。囧rz
        ee = $.trim(ee);
        var zz = document.createElement('div');
        zz.appendChild(document.createTextNode(ee))
        
        return zz.innerHTML;
    },

    confirmed:function(){//确认添加
        if(qihoo.alarm.alarm_array.length > 7){
            alert('最多只能添加 8 个闹钟提醒');
            return false;
        }

        //得到时间
        var time = new Date().getTime();
        var hour   = parseInt($('select[name=select]').val());
        var minute = parseInt($('select[name=select2]').val());
        
        if(hour < new Date().getHours()){//如果小时小于现在时,就是明天
              time += 60*60*24*1000;
        }else if((hour == new Date().getHours()) && (minute <= new Date().getMinutes())){//如果时间等于现在时间，但是分钟小于现在时，就是明天
              time += 60*60*24*1000; 
        }else{//否则一概是
        }
        

        var ee =  new Date(time);
        ee.setHours(hour,minute,0);

        time = ee.getTime();
        //得到歌曲地址
        music = $('select[name=select3]').val();

        //得到提示内容
        var alarm_prompt = $.trim($('input[name=alarm_textarea]').val());

        if(alarm_prompt == ''){
            alarm_prompt='休息，休息一下！';
            return false;
        }
        
        var is_single = 1;
        //是否重复
        $('input[name=alarm_is_single]').each(function(k,v){
            if(v.checked){
                is_single = parseInt(v.value);
            }
        });
        qihoo.alarm.alarm_array.push(new Array(time,music,alarm_prompt,is_single ,0));//最后那个 0 用来处理显示临界补偿

        qihoo.alarm.bind_event();//绑定事件 
        qihoo.alarm.serialize_to_cookie();//存储
    },  

    one_second_over_again:function(){//外部的钩子，跟艾丁宝一起干活 function 名都 xx 化了
        if(qihoo.alarm.alarm_array.length == 0){
            return false;
        }

        var write_back = new Array();
        $.each(qihoo.alarm.alarm_array , function(k,v){
            v[2] = decodeURIComponent(v[2]);

            if(v[0] <= new Date().getTime()){//如果到时候
                if((parseInt(v[0])+5000) > new Date().getTime()){//如果 5 秒钟内就提示，过 5 秒钟就 pass
                    $('#alarm_player').attr('src' , v[1]);
                    alert(v[2]);
                    $('#alarm_player').attr('src' , '');
                }
			

				v[0] += 60*60*24+1000;//明天这个时候继续
				if(v[3]){//如果是单次执行
					v = null;//否则永不执行
				}
			}

            if(v != null)
                write_back[k] = v;
        });


        if(qihoo.alarm.alarm_array.toString() != write_back.toString()){
            qihoo.alarm.alarm_array = write_back;
            qihoo.alarm.serialize_to_cookie();//存储
        
            qihoo.alarm.bind_event();//绑定事件 
        }

    },

    external_time_hook_activation:function(){//外部的闹钟
       try{
            clearInterval(qihoo.alarm.external_time_handler);
        }catch(e){}
        
 
        qihoo.alarm.external_time_handler = setInterval('qihoo.alarm.one_second_over_again();',1000);
    },

    bind_music:function(){
        $('#alarm_music_button').bind('click' , function(){
           if($('#alarm_music_button').text() == '试听'){//播放音乐
                qihoo.alarm.playing_music();
           }else{//停止音乐
                qihoo.alarm.stop_music();
           }

           return false;
        });  
    },

    playing_music:function(){
        $('#alarm_player').attr('src', $('select[name=select3]').val());
        $('#alarm_music_button').text('停止');
    },
    
    stop_music:function(){
        $('#alarm_player').attr('src', ''); 
        $('#alarm_music_button').text('试听');
    },

    //设置提醒时间
    set_alarm_time:function(){
        var offset = 60*10*1000+(new Date().getTime());//5 分钟之后
        var offset_date = new Date(offset);

        var year   = offset_date.getFullYear();
        var month  = (offset_date.getMonth())+1;
        var day    = offset_date.getDate();
        var hour   = offset_date.getHours();
        var minute = offset_date.getMinutes();
        var second = offset_date.getSeconds(); 

        /*
        if(month < 10) {month = '0' + month.toString()}
        if(day < 10)   {day   = '0' + day.toString()}
        if(hour < 10)  {hour  = '0' + hour.toString()}
        if(minute < 10){minute= '0' + minute.toString()}
        if(second < 10){second= '0' + second.toString()}
        */

        var i =1;
        var option = '';
        var writeOption = '';
    
        $('select[name=select]').empty();
        for(i=0;i<24;i++){//小时
            writeOption = i;
            //if(i < 10)  {writeOption  = '0' + i.toString()}

            if(i == hour){
                option = '<option selected>' + writeOption + '</option>';
            }else{
                option = '<option>' + writeOption + '</option>';
            }

            $('select[name=select]').append(option); 
        }


        var i =1;
        var option = '';
        var writeOption = '';
        
        $('select[name=select2]').empty();
        for(i=0;i<60;i++){//分钟
            writeOption = i;
            //if(i < 10)  {writeOption  = '0' + i.toString()}

            if(i == minute){
                option = '<option selected>' + writeOption + '</option>';
            }else{
                option = '<option>' + writeOption + '</option>';
            }

            $('select[name=select2]').append(option); 
        }
   },



    //往 Cookie 中写
    serialize_to_cookie:function(){
        var ee = new Array();
        $.each(qihoo.alarm.alarm_array , function(k,v){
            v[0] = parseInt(v[0]);
            v[2] = encodeURIComponent(v[2]);
            ee[k] = v.join('=');
        });

        ee = ee.join('|');
        setCookie(qihoo.alarm.unique_id , ee);
        return true;
        //=====================================
    },


    //从 Cookie 中读取，反序列化之后　ＸＸ
    unserialize_from_cookie:function(){
        var ee = getCookie(qihoo.alarm.unique_id);
        if(ee == null)  return false;
        
        //用这个替换
        //replace(/\r|\n/gi,'[br]')
        var zz;
        zz = ee.split('|');//拿出 N 个 alarm

        if(zz == null)  return false;//没有闹钟

        $.each(zz,function(k,v){
            var kk = new Array();
            
            kk = v.split('=');
            if(kk.length == 5){
                kk[0] = kk[0].toString();
                kk[2] = decodeURIComponent(kk[2]);
                qihoo.alarm.alarm_array.push(kk);

            }
        });

    },

    //关闭
    close:function(){
        qihoo.alarm.stop_music();

        $('#clockT').hide();
    
        //关闭跑的时钟
        clearInterval(qihoo.alarm.second_handler_timeshow);
        qihoo.alarm.serialize_to_cookie();//存储
    },

    set_default_prompt_from_cookie:function(){
       qihoo.alarm.set_prompt(qihoo.alarm.clock_prompt);
    },

    //设置留言
    set_prompt:function(msg){
        $('input[name=alarm_textarea]').val(msg);
    }, 

    //得到留言
    get_prompt:function(msg){
        return $('input[name=alarm_textarea]').val();
    }   

});

//开始邦定
qihoo.alarm.init();
qihoo.alarm.external_time_hook_activation();//一秒一秒跑啊跑
window.onload=function(){qihoo.alarm.display();}
</SCRIPT>
</BODY></HTML>
