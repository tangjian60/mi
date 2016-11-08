<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel mi\modules\credit\models\MiOpinionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '建议';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-opinion-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Mi Opinion', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'content',
            'contact',
            'addtime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
