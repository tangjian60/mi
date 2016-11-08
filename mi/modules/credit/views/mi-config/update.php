<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiConfig */

$this->title = 'Update Mi Config: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mi Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mi-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
