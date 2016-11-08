<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use mi\modules\credit\assets\AppAsset;
use mi\modules\credit\widgets\Alert;
use yii\helpers\Url;
/**
 * @var \yii\web\View $this
 * @var string $content
 */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => '后台系统',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
       
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
                $menuItems[] = ['label' => '登陆', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                    'label' => '退出 (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/credit/index/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }

           
            echo Nav::widget([
            		'options' => ['class' => 'navbar-nav navbar-right'],
            		'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container-fluid" style="padding: 70px 15px 20px;">
            <div class="col-lg-2">

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    	导航
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item active">
                                       		管理列表
                                    </a>
                                     <?php
					                    $menu = [
					                    		['url' => 'mi-advertising/index', 'name' => '广告管理'],
					                    		['url' => 'mi-navigation/index', 'name' => '名站导航管理'],
					                    		['url' => 'mi-category/index', 'name' => '分类'],
					                    		['url' => 'mi-opinion/index', 'name' => '意见']
					                    ];
					
					                    foreach($menu as $v):
					                        $arr = explode('/', $v['url']);
					                ?>
					                <a href="<?= Url::to([$v['url']]) ?>" class="list-group-item">
					                   <?=$v['name']?>
					                </a>
					                <?php endforeach;?>
                               
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-10">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
            </div>
        </div>

    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; 后台系统 <?= date('Y') ?></p>

        </div>
    </footer>
 
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
