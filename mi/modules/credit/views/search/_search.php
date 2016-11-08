<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model vecrm\modules\credit\models\CreditGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credit-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'goods_name') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'is_delete') ?>

    <?= $form->field($model, 'goods_sort') ?>

    <?php // echo $form->field($model, 'exchange') ?>

    <?php // echo $form->field($model, 'goods_job') ?>

    <?php // echo $form->field($model, 'goods_resume') ?>

    <?php // echo $form->field($model, 'goods_college') ?>

    <?php // echo $form->field($model, 'goods_ops') ?>

    <?php // echo $form->field($model, 'thumb') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'add_time') ?>

    <?php // echo $form->field($model, 'modify_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
