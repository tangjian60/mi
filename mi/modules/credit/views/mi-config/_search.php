<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mi-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sitetitle') ?>

    <?= $form->field($model, 'sitetitle2') ?>

    <?= $form->field($model, 'sitedescription') ?>

    <?= $form->field($model, 'siteurl') ?>

    <?php // echo $form->field($model, 'sitekeywords') ?>

    <?php // echo $form->field($model, 'sitetcp') ?>

    <?php // echo $form->field($model, 'sitelx') ?>

    <?php // echo $form->field($model, 'sitelogo') ?>

    <?php // echo $form->field($model, 'pingoff') ?>

    <?php // echo $form->field($model, 'postovertime') ?>

    <?php // echo $form->field($model, 'bookoff') ?>

    <?php // echo $form->field($model, 'mood') ?>

    <?php // echo $form->field($model, 'ishits') ?>

    <?php // echo $form->field($model, 'iscopyfrom') ?>

    <?php // echo $form->field($model, 'isauthor') ?>

    <?php // echo $form->field($model, 'artlistnum') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
