<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model vecrm\modules\credit\models\CreditGoods */

$this->title = 'Create Credit Goods';
$this->params['breadcrumbs'][] = ['label' => 'Credit Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
