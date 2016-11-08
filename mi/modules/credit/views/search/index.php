<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel vecrm\modules\credit\models\CreditGoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Credit Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credit-goods-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Credit Goods', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'goods_name',
            'status',
            'is_delete',
            'goods_sort',
            // 'exchange',
            // 'goods_job',
            // 'goods_resume',
            // 'goods_college',
            // 'goods_ops',
            // 'thumb',
            // 'description:ntext',
            // 'add_time',
            // 'modify_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
