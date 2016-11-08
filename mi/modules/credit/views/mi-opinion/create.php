<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiOpinion */

$this->title = 'Create Mi Opinion';
$this->params['breadcrumbs'][] = ['label' => 'Mi Opinions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-opinion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
