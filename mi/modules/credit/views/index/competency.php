<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;

use mi\modules\credit\assets\AppAsset;
$this->beginPage();
?>
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
     <form action="" name="form1">  
	<table width="755" cellspacing="0" cellpadding="0" border="0" id="Record_table" class="Com_table">
    	<tbody><tr>
    		<td width="91">id</td>
            <td width="193">岗位名称</td>
            <td width="89">指标</td>
            <td width="198">行为要素</td>
            <td width="132">学习资源</td>
        </tr>
        <?php foreach($XzPost as $key=>$value):?>
         <tr>
          <?php if($key>0 && $XzPost[$key-1]['id']==$value['id']):?> 
            <th ></th>
            <?php else:?> 
            <td><input id="u14_input" type="checkbox" value="<?= $value['id']?>"></td>
            <?php endif;?>
            <?php if($key>0 && $XzPost[$key-1]['name']==$value['name']):?> 
            <th ></th>
            <?php else:?> 
            <td ><?=$value['name']?></td> 
            <?php endif;?>
            <?php if($key>0 && $XzPost[$key-1]['indexnamme']==$value['indexnamme']):?> 
              <th ></th>  
             <?php else:?> 
            <td ><?=$value['indexnamme']?></td> 
            <?php endif;?>
           	<?php if($key>0 && $XzPost[$key-1]['faname']==$value['faname']):?> 
              <th ></th>  
             <?php else:?> 
            <td ><?=$value['faname']?></td> 
            <?php endif;?>
         <?php if($key>0 && $XzPost[$key-1]['title']==$value['title']):?> 
              <th ></th>  
             <?php else:?> 
            <td ><?=$value['title']?></td> 
            <?php endif;?>
            
        </tr>
        <?php endforeach;?>

       <?php if(empty($XzPost)):?>
            <tr>
                <td colspan="5">还没有调整记录</td>
            </tr>
        <?php endif;?>
			</tbody></table>
        <input type="button" name="" value="全选" onClick="selectall()">  
       </form>  
            <?php $this->endPage() ?>