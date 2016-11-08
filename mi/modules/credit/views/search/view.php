<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model vecrm\modules\credit\models\CreditGoods */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_name',
            'status',
            'is_delete',
            'goods_sort',
            'exchange',
            'goods_job',
            'goods_resume',
            'goods_college',
            'goods_ops',
            'thumb',
            'description:ntext',
            'add_time',
            'modify_time',
        ],
    ]) ?>

</div>
