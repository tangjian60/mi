<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel mi\modules\credit\models\MiConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mi Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Mi Config', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sitetitle:ntext',
            'sitetitle2:ntext',
            'sitedescription:ntext',
            'siteurl:ntext',
            // 'sitekeywords:ntext',
            // 'sitetcp:ntext',
            // 'sitelx:ntext',
            // 'sitelogo:ntext',
            // 'pingoff',
            // 'postovertime:datetime',
            // 'bookoff',
            // 'mood',
            // 'ishits',
            // 'iscopyfrom',
            // 'isauthor',
            // 'artlistnum',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
