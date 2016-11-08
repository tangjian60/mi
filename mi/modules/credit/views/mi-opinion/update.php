<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiOpinion */

$this->title = '更新建议: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '建议', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mi-opinion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
