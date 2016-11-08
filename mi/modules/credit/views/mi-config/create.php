<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiConfig */

$this->title = 'Create Mi Config';
$this->params['breadcrumbs'][] = ['label' => 'Mi Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
