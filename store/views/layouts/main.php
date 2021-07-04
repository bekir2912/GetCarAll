<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\Shop;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use store\assets\AppAsset;
use common\widgets\Alert;

$selected_shop = Shop::findOne(['id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0]);
$shops = Shop::find()->where(['seller_id' => Yii::$app->user->id, 'deleted_at' => 0])->all();

$requestedRoute = explode('/', Yii::$app->requestedRoute);
$requestedRoute = $requestedRoute[0];

$new_chat = \common\models\Chat::find()->where(['shop_id' => Yii::$app->session->get('shop_id'), 'direction' => 1, 'is_read' => 0, 'type' => 'shop'])->count('id');

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

    <!-- <script src="https://api-maps.yandex.ru/2.1/?apikey=<?=Yii::$app->params['ya_key']?>&lang=ru_RU" type="text/javascript"> -->
    <!-- </script> -->
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <script type = "text / javascript" src = "http://maps.googleapis.com/maps/api/js? libraries = places & sensor; = true_or_false " > </script>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js" crossorigin="anonymous"></script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class = "hold-transition sidebar-mini layout-fixed" onload="init()">
<?php $this->beginBody() ?>

<div class="wrapper">

  <!-- Preloader -->
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">

    <?php if (Yii::$app->user->isGuest) { ?>
        <?php if (Yii::$app->requestedAction->id == 'login') { ?>
<!--        <a class="btn btn-top" href="--><?//= Url::to(['site/signup']) ?><!--" style="background: #1b8ad9; !important; ">-->
<!--            --><?//= FA::i('user-plus') ?><!-- <span-->
<!--                    class="hidden-xs">Подать заявку</span>-->
<!--        </a>-->
    <?php } else { ?>
        <a class="btn btn-top" href="<?= Url::to(['site/login']) ?>">
            <?= FA::i('user-circle-o') ?> <span
                    class="hidden-xs">Вход</span>
        </a>
    <?php } ?>
    <?php }  ?>

    <?php if(!Yii::$app->user->isGuest) { ?>


        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?=Url::to(['balance/fill'])?>" class="nav-link">Баланс
                <span class="text-secondary">
                    <?=number_format(Yii::$app->getUser()->identity->balance, 0, '', ' ')?> <?=Yii::t('frontend', 'uzs')?>
                </span>
            </a>

        </li>
        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 user-name text-bold-700">Личный кабинет</span><span class="avatar avatar-online"><i></i></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="<?= Url::to(['seller/update', 'id' => Yii::$app->user->id]) ?>" class="dropdown-item custom-btn logout-btn">Профиль</a>
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

      <?php if(!Yii::$app->user->isGuest && $shops) { ?>
        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown"><span class="mr-1 user-name text-bold-700">Компании </span><span class="avatar avatar-online"><i></i></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <?php foreach ($shops as $shop) { ?>
                    <li><a class ="dropdown-item custom-btn logout-btn" href="<?=Url::to(['site/change-shop', 'id' => $shop->id])?>" <?=($shop->id == $selected_shop->id)? ' style="color: #fa7c0d"':''?>><?=$shop->name?></a></li>
                <?php } ?>
            </ul>
        </li>
      <?php } ?>
  <?php } ?>




    </ul>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href=""<?= Url::to(['site/index']) ?>" class="brand-link">
        <img src="/backend/web/dist/img/AdminLTELogo.png" alt="GetCar" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">GetCar</span>
    </a>

      <?php if (!Yii::$app->user->isGuest) {?>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <span style = "color: white; font-size: 1.4rem" class = "bold">Компания:</span>  <a   href="<?= Url::to(['shop/update', 'id' => $shop->id]) ?>" style = "display:inline !important; color: #1a8535; font-size: 1.5rem;" class="d-block"><?=($selected_shop)? mb_substr($selected_shop->name, 0, 10): ''?></a>
        </div>
      </div>
        <?php }?>

      <!-- SidebarSearch Form -->


      <!-- Sidebar Menu -->
      <nav class="mt-2">

      <?php if(!Yii::$app->user->isGuest) { ?>

            <?php if(!empty($selected_shop)) { ?>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">



                <li class="nav-item">
                <a href="<?=Url::to(['site/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'site')? 'active': '';?>">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                    Главная
                    </p>
                </a>
                </li>



              <li class="nav-item">
                  <a href="<?=Url::to(['booking/index'])?>" class="nav-link <?=($requestedRoute == 'reservation')? 'active': '';?>">
                      <i class="nav-icon fas fa-exchange-alt"></i>
                      <p>
                          Бронирования
                          <!--              <span class="right badge badge-danger">1</span>-->
                      </p>
                  </a>
              </li>

                <li class="nav-item">
                <a href="<?=Url::to(['product/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'product')? 'active': '';?>">
                    <i class="nav-icon fas fa-car"></i>
                    <p>
                    Добавить Авто
                    </p>
                </a>
                </li>


              <li class="nav-item">
                  <a href="<?=Url::to(['user/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'user')? 'active': '';?>">
                      <i class="nav-icon fas fa-users"></i>
                      <p>
                          Пользователи
                      </p>
                  </a>
              </li>

              <li class="nav-item">
                  <a href="<?=Url::to(['user-black/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'user-black')? 'active': '';?>">
                      <i class="nav-icon fas fa-user-times"></i>
                      <p>
                          Черный список
                      </p>
                  </a>
              </li>


                <li class="nav-item">
                <a href="#<?php //Url::to(['messages/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'messages')? 'active': '';?>">
                    <i class="nav-icon fas fa-comment-dots"></i>
                    <p>
                    Сообщения   <?=($new_chat > 0)? '<span class="right badge badge-danger">'.$new_chat.'</span>':''?>
                    </p>
                </a>
                </li>


                <li class="nav-item">
                <a href="<?=Url::to(['review/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'review')? 'active': '';?>">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>
                    Отзывы
                    </p>
                </a>
                </li>

              <li class="nav-item">
                  <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-book"></i>
                      <p>
                          Настройки компании
                          <i class="fas fa-angle-left right"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview" >


                        <li  class="nav-item <?=($requestedRoute == 'pickup_and_return')? 'active': '';?>">
                            <a href="<?=Url::to(['location/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'location')? 'active': '';?>">
                                <i class="fa fa-map-marked-alt nav-icon"></i>
                                <p>Возврат и прием авто</p>
                            </a>
                            <a href="<?=Url::to(['booking-status/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'booking-status')? 'active': '';?>">
                                <i class="fa fa-credit-card nav-icon"></i>
                                <p>Статус брони</p>
                            </a>
                            <a href="<?=Url::to(['product-status/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'product-status')? 'active': '';?>">
                                <i class="fa fa-car-crash nav-icon"></i>
                                <p>Статус авто</p>
                            </a>

                            <a href="<?=Url::to(['product-type/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'product-type')? 'active': '';?>">
                                <i class="fa fa-taxi nav-icon"></i>
                                <p>Тип авто</p>
                            </a>
                        </li>

<!--                      <li  class="nav-item --><?//=($requestedRoute == 'lineup')? 'active': '';?><!--">-->
<!--                          <a href="--><?//=Url::to(['lineup/index', 'sort' => '-id'])?><!--" class="nav-link --><?//=($requestedRoute == 'lineup')? 'active': '';?><!--">-->
<!--                              <i class="far fa-circle nav-icon"></i>-->
<!--                              <p>Модели</p>-->
<!--                          </a>-->
<!--                      </li>-->
<!--                      <li  class="nav-item --><?//=($requestedRoute == 'option-group')? 'active': '';?><!--">-->
<!--                          <a href="--><?//=Url::to(['option-group/index', 'sort' => '-id'])?><!--" class="nav-link --><?//=($requestedRoute == 'option-group')? 'active': '';?><!--">-->
<!--                              <i class="far fa-circle nav-icon"></i>-->
<!--                              <p>Группы</p>-->
<!--                          </a>-->
<!--                      </li>-->
<!--                      <li  class="nav-item --><?//=($requestedRoute == 'option-value')? 'active': '';?><!--">-->
<!--                          <a href=" --><?//=Url::to(['option-value/index', 'sort' => '-id'])?><!--" class="nav-link --><?//=($requestedRoute == 'option-value')? 'active': '';?><!--">-->
<!--                              <i class="far fa-circle nav-icon"></i>-->
<!--                              <p>Опции</p>-->
<!--                          </a>-->
<!--                      </li>-->
<!--                      <li class="nav-item">-->
<!--                          <a href="--><?//=Url::to(['city/index', 'sort' => 'order'])?><!--" class="nav-link --><?//=($requestedRoute == 'city')? 'active': '';?><!--">-->
<!--                              <i class="far fa-circle nav-icon"></i>-->
<!--                              <p>Города</p>-->
<!--                          </a>-->
<!--                      </li>-->

                  </ul>
              </li>

                <?php } ?>

                <li  class="nav-item" >
                    <a href="<?=Url::to(['shop/index', 'sort' => '-id'])?>" class="nav-link <?=($requestedRoute == 'shop' && Yii::$app->requestedRoute != 'shop/update')? 'active': '';?>">
                        <i class="nav-icon fas fa-info"></i>
                        <p>
                        О компании
                        </p>
                    </a>
                </li>

            </ul>



        <?php } ?>

      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->

  <div class="right_col" role="main">
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
            <?php if( Yii::$app->session->hasFlash('success') ): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo Yii::$app->session->getFlash('success'); ?>
                </div>
            <?php endif;?>
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

</div>?>

<!--<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>-->
<!--    <script src="https://api-maps.yandex.ru/2.1/?apikey=52dd34b7-84ab-45ed-bedc-24023fdfa601&lang=ru_RU"-->
<!--            type="text/javascript">-->
<!--    </script>-->
<!--<script src="https://api-maps.yandex.ru/2.1/?apikey=--><?//=Yii::$app->params['ya_key']?><!--&lang=ru_RU" type="text/javascript">-->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>