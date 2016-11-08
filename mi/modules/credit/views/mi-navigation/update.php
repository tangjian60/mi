<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiNavigation */

$this->title = 'Update Mi Navigation: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Mi Navigations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mi-navigation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
