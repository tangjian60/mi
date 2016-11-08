<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiCategory */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
