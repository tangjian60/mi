<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiNavigation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mi-navigation-form">

   	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    
    <div class="form-group field-minavigation-picture">
	<label class="control-label" for="minavigation-picture">图片</label>
	<input type="hidden" name="MiNavigation[picture]" value="<?= $model->picture;?>">
	<input type="file" id="minavigation-picture" name="MiNavigation[picture]" value="<?= $model->picture;?>">
	<div class="help-block"></div>
	</div>
	
	<?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
	
	<div class="form-group field-minavigation-status has-success">
	<label for="minavigation-status" class="control-label">是否显示</label>
	<select size=1 name="MiNavigation[status]" id="minavigation-status"  class="form-control">
	<option <?php if($model->attributes['status']== '1'){ echo 'selected';};?> value="1">显示</option>
	<option <?php if($model->attributes['status']== '0'){ echo 'selected';};?> value="0">否</option>
	</select>
	<div class="help-block"></div>
	</div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
