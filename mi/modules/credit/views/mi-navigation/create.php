<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiNavigation */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '名站', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mi-navigation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
