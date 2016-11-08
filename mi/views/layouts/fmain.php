<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use communal\widgets\Alert;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(yii::$app->params['title']) ?></title>
    <meta name="description" content="<?= Html::encode(yii::$app->params['description']) ?>" />
    <meta name="keywords" content="<?= Html::encode(yii::$app->params['keywords']) ?>" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

 <?= $content ?>
<!-- <footer class="footer">
			<div class="footer_first">
				<ul>
					<li>关于我们</li>
					<li>网站提交</li>
					<li>意见反馈</li>
					<li>团购资讯</li>
					<li>广告合作</li>
					<li>投放QQ:</li>
					<li style="border:none; padding-bottom:4px;"><a href=""><img src="<?= Url::to('@web/credit_s/img/qq.gif', true) ?>" alt=""></a></li>
				</ul>
			</div>
			<div class="footer_last"><?= yii::$app->params['copy']?>
				</div>
			<div class="foot_totop">返回顶部</div>
		</footer>  --> <!-- 页脚 -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
