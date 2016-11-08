<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mi-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sitetitle')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitetitle2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitedescription')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'siteurl')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitekeywords')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitetcp')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitelx')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sitelogo')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pingoff')->textInput() ?>

    <?= $form->field($model, 'postovertime')->textInput() ?>

    <?= $form->field($model, 'bookoff')->textInput() ?>

    <?= $form->field($model, 'mood')->textInput() ?>

    <?= $form->field($model, 'ishits')->textInput() ?>

    <?= $form->field($model, 'iscopyfrom')->textInput() ?>

    <?= $form->field($model, 'isauthor')->textInput() ?>

    <?= $form->field($model, 'artlistnum')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
