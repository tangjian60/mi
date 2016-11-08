<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel mi\modules\credit\models\MiAdvertisingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '广及公告';//yii::$app->params['title']
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-advertising-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'content:ntext',
            'description:ntext',
            [
            'attribute' => 'status',
            'value'=>function ($model){
            	return $model->status==1?'显示':'否';
            },
            ],
            'url',
            // 'type',
            // 'align',
            // 'addtime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
