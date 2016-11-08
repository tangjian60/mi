
<?php foreach($XzPost as $key=>$value):?>
<?= $value->name?><br>
<?php endforeach;?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">  
    <html xmlns="http://www.w3.org/1999/xhtml">  
    <head>  
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
    <title>无标题文档</title>  
    <link rel="stylesheet" type="text/css" href="./test.css"/>  
         <script>  
      function selectall() {  
        var a = document.all;  
        for(var i = 0;i<a.length;i++) {  
           if(typeof a[i]!="undefind" && a[i].type=='checkbox') {  
              a[i].checked=true;  
           }  
        }  
      }  
    </script>  
    </head>  
        
      <body>  
      <hr>  
      <center>   
      <form action="" name="form1">  
        
       <table border="1"  class="t1">  
         <tr><th>编号</th><th>姓名</th><th>性别</th><th>电话号码</th><th>邮箱</th><th>删除</th><th>修改</th></tr>  
         <tr>  
          <td>123</td><td>王五</td><td>男</td><td>123456</td><td>123</td><td><input type="checkbox" ></td><td><a href="updataEmp.html">修改</a></td></tr>  
          </table>  
        <input type="button" name="" value="全选" onClick="selectall()">  
       </form>  
       

<table border="1">
<tr><th>姓名</th><td>Bill Gates</td></tr>
<tr><th rowspan="2">电话</th><td>555 77 854</td></tr>
<tr><td>555 77 855</td></tr>
</table>

       
       </center>  
      </body>  
    </html>  