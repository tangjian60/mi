<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiAdvertising */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mi-advertising-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
	
	<?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
	
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	
	<div class="form-group field-baoapkname-picture">
	<label class="control-label" for="miadvertising-picture">图片</label>
	<input type="hidden" name="MiAdvertising[picture]" value="<?= $model->picture;?>">
	<input type="file" id="miadvertising-picture" name="MiAdvertising[picture]" value="<?= $model->picture;?>">
	<div class="help-block"></div>
	</div>
	
    <div class="form-group field-miadvertising-status has-success">
	<label for="miadvertising-status" class="control-label">是否显示</label>
	<select size=1 name="MiAdvertising[status]" id="miadvertising-status"  class="form-control">
	<option <?php if($model->attributes['status']== '1'){ echo 'selected';};?> value="1">显示</option>
	<option <?php if($model->attributes['status']== '0'){ echo 'selected';};?> value="0">否</option>
	</select>
	<div class="help-block"></div>
	</div>
	
	
	<div class="form-group field-miadvertising-type has-success">
	<label for="miadvertising-type" class="control-label">类型</label>
	<select size=1 name="MiAdvertising[type]" id="miadvertising-type"  class="form-control">
	<option <?php if($model->attributes['status']== '1'){ echo 'selected';};?> value="1">广告</option>
	<option <?php if($model->attributes['status']== '2'){ echo 'selected';};?> value="2">公告</option>
	</select>
	<div class="help-block"></div>
	</div>

	<div class="form-group field-miadvertising-align has-success">
	<label for="miadvertising-align" class="control-label">投放位置</label>
	<select size=1 name="MiAdvertising[align]" id="miadvertising-align"  class="form-control">
	<?php 
	$align=array('1'=>"顶部",'2'=>"左",'3'=>"右",'4'=>"中",'5'=>"下",'6'=>"底");
	foreach ($align as $key=>$val){
		if(isset($model->attributes["align"]) && $model->attributes["align"]== $key){		
				echo <<<Eof
'<option selected value="$key">$val</option>'
Eof;
		}else{
			echo <<<Eof
'<option  value="$key">$val</option>'
Eof;
		}
	}
	?>
	</select>
	<div class="help-block"></div>
	</div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
