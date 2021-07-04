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



<!-- Preloader -->
<!-- <div class="preloader flex-column justify-content-center align-items-center">
  <img class="animation__shake" src="/backend/web/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
</div> -->

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
<!--    <li class="nav-item d-none d-sm-inline-block">-->
<!--      <a href="--><?//= Url::to(['site/index']) ?><!--" class="nav-link">Home</a>-->
<!--    </li>-->


      <?php if (!Yii::$app->user->isGuest) {?>
    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 text-bold-700">Настройки</span><span class="avatar avatar-online"><i></i></span></a>
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
      <?php }?>
    <!-- <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Contact</a>
    </li> -->
  </ul>


</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->

  

  <!-- Sidebar -->
    <a href="<?= Url::to(['site/index']) ?>" class="brand-link">
        <img src="/backend/web/dist/img/AdminLTELogo.png" alt="GetCar" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">GetCar</span>
    </a>
  <?php if (!Yii::$app->user->isGuest) { ?>


  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="/uploads/users/5be164a3ca101.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="<?= Url::to(['admin/update', 'id' => Yii::$app->user->id]) ?>" class="d-block"> <?= $admin->name?></a>
      </div>
    </div>


    <?php } ?>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
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



        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Главная
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= Url::to(['site/index']) ?>" class="nav-link ">
                <i class="far fa-circle nav-icon"></i>
                <p>Админ GetCar</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="http://wallet.clickbox.uz/#/" class="nav-link ">
                <i class="far fa-circle nav-icon"></i>
                <p>Сайт GetCar</p>
              </a>

            </li>
            <li class="nav-item">
              <a href='/../../../store/web' class="nav-link ">
                <i class="far fa-circle nav-icon"></i>
                <p>Вход в прокатные организации</p>
              </a>

            </li>
           
          </ul>
        </li>
        <li class="nav-item">
          <a href="<?=Url::to(['shop/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'shop')? 'active': '';?>">
            <i class="nav-icon fas fa-building"></i>
            <p>
            Прокатные организации
<!--              <span class="right badge badge-danger">1</span>-->
            </p>
          </a>
        </li>

         <li class="nav-item">
             <a href="<?=Url::to(['reservation/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'reservation')? 'active': '';?>">
                 <i class="nav-icon fas fa-exchange-alt"></i>
                 <p>
                     Брони
                     <!--              <span class="right badge badge-danger">1</span>-->
                 </p>
             </a>
         </li>
        
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>
            Справочники 
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" >

        

            <li  class="nav-item">
              <a href="<?=Url::to(['category/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'category')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Категории</p>
              </a>
            </li>
            <li  class="nav-item <?=($requestedRoute == 'brand')? 'active': '';?>">
              <a href="<?=Url::to(['brand/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'brand')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Марки</p>
              </a>
            </li>
            <li  class="nav-item <?=($requestedRoute == 'lineup')? 'active': '';?>">
              <a href="<?=Url::to(['lineup/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'lineup')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Модели</p>
              </a>
            </li>


              <li class="nav-item">
                  <a href="<?=Url::to(['body-style/index', 'sort' => 'order'])?>" class="nav-link <?=($requestedRoute == 'city')? 'active': '';?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Типы машин</p>
                  </a>
              </li>


            <li  class="nav-item <?=($requestedRoute == 'option-group')? 'active': '';?>">
              <a href="<?=Url::to(['option-group/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'option-group')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Группы</p>
              </a>
            </li>
            <li  class="nav-item <?=($requestedRoute == 'option-value')? 'active': '';?>">
              <a href=" <?=Url::to(['option-value/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'option-value')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Опции</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=Url::to(['city/index', 'sort' => 'order'])?>" class="nav-link <?=($requestedRoute == 'city')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Города</p>
              </a>
            </li>




          </ul>
        </li>
        
        <li class="nav-item">
          <a href="<?=Url::to(['review/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'review')? 'active': '';?>">
            <i class="nav-icon fas fa-comment"></i>
            <p>
              Отзывы клиентов
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=Url::to(['news/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'news')? 'active': '';?>">
            <i class="nav-icon fas fa-newspaper-o"></i>
            <p>
              Новости
            </p>
          </a>
        </li>



        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>
              Пользователи 
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" >

        

            <li  class="nav-item">
              <a href="<?=Url::to(['seller/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'seller')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Юр Лица</p>
              </a>
            </li>

            <li  class="nav-item">
              <a href="<?=Url::to(['user/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'user')? 'active': '';?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Пользователи</p>
              </a>
            </li>

          </ul>
        </li>

 
        <?php } ?>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<div class="right_col" role="main">
                <!-- top tiles -->
                <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <?= Alert::widget() ?>
                        <?= Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? ($this->params['breadcrumbs']) : [],
                            'homeLink' => false
                        ]) ?>
                    </div>
                </div>
                        <div class="content">
                        <?= $content ?>
                        </div>
                </div>
           
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
