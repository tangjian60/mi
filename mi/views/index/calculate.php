<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJsFile('/assets/9b51c711/jquery.js');
?>
<?php $this->beginBlock('test') ?>
	$(document).ready(function()
  	{
	  $("#btn1").click(function(){
	   document.getElementById("text").value = "";
	  });

});
<?php $this->endBlock() ?>  
<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?> 

	<?php $form = ActiveForm::begin([
'action' => ['index/calculate'],
'id' => 'login-form',
'options' => ['class' => 'form-horizontal'],
'fieldConfig' => [
'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
'labelOptions' => ['class' => 'col-lg-2 control-label'],
], 
]); ?>

<input id="baobduser-username" class="form-control" name="BaoBduser[username]" maxlength="50" type="text">

<?= Html::submitButton('添加',['class' => 'btn btn-success']); ?>
<?= Html::resetButton('清除', ['class' => 'btn btn-default']); ?>
<?php ActiveForm::end(); ?>

<!-- <form action="./calculate" method="post">
	<label class="control-label" for="baobduser-username">输入</label>
    <input id="text" type="text" name="text" />
    <button type="submit" >确认</button>
    <button type="button" onclick="clear();" id="btn1">清除</button>
</form>
      -->
<div class="help-block">展示数据列表</div>
</div>
