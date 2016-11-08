<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiConfig */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mi Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-config-view">

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
            'sitetitle:ntext',
            'sitetitle2:ntext',
            'sitedescription:ntext',
            'siteurl:ntext',
            'sitekeywords:ntext',
            'sitetcp:ntext',
            'sitelx:ntext',
            'sitelogo:ntext',
            'pingoff',
            'postovertime:datetime',
            'bookoff',
            'mood',
            'ishits',
            'iscopyfrom',
            'isauthor',
            'artlistnum',
        ],
    ]) ?>

</div>
