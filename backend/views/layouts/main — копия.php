<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\models\Admin;

$requestedRoute = explode('/', Yii::$app->requestedRoute);
$requestedRoute = $requestedRoute[0];

$product_count = \common\models\Product::find()->where(['is_moderated' => 0])->count('id');
$reviews = \common\models\ShopReview::find()->where(['is_moderated' => 0])->count('id');
$questions = \common\models\Question::find()->where(['is_moderated' => 0])->count('id');
$answers = \common\models\Answer::find()->where(['is_moderated' => 0])->count('id');
$admin = Admin::find()->one();
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <link rel="shortcut icon" href="/uploads/site/favicon.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <title><?= Html::encode($this->title) ?></title>


<!--    <script src="https://api-maps.yandex.ru/2.1/?apikey=--><?//=Yii::$app->params['ya_key']?><!--&lang=ru_RU" type="text/javascript">-->
    </script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0"></script>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed" disabled="">

    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">

                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="<?= Url::to(['site/index']) ?>" class="site_title"><i class="fa fa-car"></i> <span>GetCar</span></a>
                    </div>
                    <?php if(Yii::$app->user->isGuest) { ?>



                        <div class="row">
                            <div class="col-md-12">
                                <div class="right-content">
                                    <div class="row">
                                        <div class="col-12">
                                            <?= Alert::widget() ?>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                    <?php } else { ?>



                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="/uploads/site/img.jpg" alt="admin" class="img-circle profile_img">

                        </div>
                        <div class="profile_info" >
                            <span>Добро пожаловть</span>
                            <h2 class="custom-inline"><?php echo "$admin->name"?></h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <h3>Меню</h3>
                            <ul class="nav side-menu">
                                <li><a><i class="fa fa-home"></i> Главная <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="<?= Url::to(['site/index']) ?>">Админ GetCar</a></li>
                                        <li><a href="http://wallet.clickbox.uz/#/">Сайт getCar</a></li>
                                    </ul>
                                </li>
                                <li <?=($requestedRoute == 'shop')? 'class="active"': '';?>><a href="<?=Url::to(['shop/index', 'sort' => '-id'])?>"> <i class="fa fa-building"></i>Прокатные организации</a></li>
                                <li><a><i class="fa fa-sitemap"></i> Справочники <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li  <?=($requestedRoute == 'category')? 'class="current-page"': '';?>><a href="<?=Url::to(['category/index', 'sort' => '-id'])?>"> Категории</a></li>
                                        <li <?=($requestedRoute == 'brand')? 'class="current-page"': '';?>><a href="<?=Url::to(['brand/index', 'sort' => '-id'])?>">Марки</a></li>
                                        <li <?=($requestedRoute == 'lineup')? 'class="current-page"': '';?>><a href="<?=Url::to(['lineup/index', 'sort' => '-id'])?>">Модели</a></li>
                                        <li <?=($requestedRoute == 'option-group')? 'class="current-page"': '';?>><a href="<?=Url::to(['option-group/index', 'sort' => '-id'])?>">Группы</a></li>
                                        <li <?=($requestedRoute == 'option-value')? 'class="current-page"': '';?>><a href="<?=Url::to(['option-value/index', 'sort' => '-id'])?>">Опции</a></li>
                                        <li <?=($requestedRoute == 'city')? 'class="active"': '';?>><a href="<?=Url::to(['city/index', 'sort' => 'order'])?>"> Города</a></li>


                                    </ul>
                                </li>

                                <li <?=($requestedRoute == 'review')? 'class="active"': '';?>><a href="<?=Url::to(['review/index', 'sort' => '-id'])?>"> <i class="fa fa-comment-o"></i>Отзывы клиентов</a></li>
                                <li <?=($requestedRoute == 'news')?     'class="active"': '';?>><a href="<?=Url::to(['news/index', 'sort' => '-id'])?>">  <?=FA::i('newspaper-o')?> Новости</a></li>


                            </ul>
                        </div>          
              
                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Settings">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Lock">
                            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                    <?php } ?>
                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>
                    <?php if (!Yii::$app->user->isGuest) { ?>

                        <nav class="nav navbar-nav">
                                <ul class=" navbar-right">
                                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 user-name text-bold-700">Настройки <?= $admin->name?></span><span class="avatar avatar-online"><i></i></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="<?= Url::to(['admin/update', 'id' => Yii::$app->user->id]) ?>" class="dropdown-item custom-btn logout-btn">Профиль</a>
                                            </li>
                                            <li>
                                                <?= Html::beginForm(['/site/logout'], 'post') .
                                                Html::submitButton(' Выйти',
                                                    ['class' => 'dropdown-item']
                                                )
                                                . Html::endForm()
                                                ?>

                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                        </nav>
                        <?php } ?>

                </div>
            </div>
            <!-- /top navigation -->

            <!-- page content -->
            <div class="right_col" role="main">
                <!-- top tiles -->
                <div class="row">
                    <div class="col-md-12">
                        <?= Alert::widget() ?>
                        <?= Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? ($this->params['breadcrumbs']) : [],
                            'homeLink' => false
                        ]) ?>
                    </div>
                </div>
                <?= $content ?>
            </div>
            <!-- /page content -->

            <!-- footer content -->
            <?php if (Yii::$app->user->isGuest) { ?>
                <footer class="footer border-top copyright">
                    <div class="container ">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                            </div>
                            <div class="col-sm-12 text-center">
                                <p>
                                    <?= Yii::t('common', 'powered') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </footer>
            <?php } else { ?>
                <footer class="footer border-top ">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="pull-right">
                                    &copy; <?= Yii::$app->params['appName'] ?> <span class="hidden-xs"><?= ((date('Y') > 2021) ? '2021-' : '') . date('Y') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </footer>
            <?php } ?>
            <!-- /footer content -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>










<?php $this->registerJs('
    $(function () {
        $(\'[data-toggle="tooltip"]\').tooltip();
    });
'); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
