<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model vecrm\modules\credit\models\CreditGoods */

$this->title = 'Update Credit Goods: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="credit-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
