<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiAdvertising */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '广告公告', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-advertising-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
