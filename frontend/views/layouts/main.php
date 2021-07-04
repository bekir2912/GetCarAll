<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);

$banners = \common\models\Banner::find()->where(['status' => 1, 'type' => 0])->andWhere(['>', 'expires_in', time()])->orderBy('order')->all();
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
    <title><?= Html::encode($this->title) ?></title>

    <script src="https://api-maps.yandex.ru/2.1/?apikey=<?=Yii::$app->params['ya_key']?>&lang=ru_RU" type="text/javascript">
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div id="content-wrapper">
    <?= $this->render('header.php') ?>
    <main class="middle">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <?= $content ?>
                </div>
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="banner-block">
                        <?php for ($i = 0; $i < count($banners); $i++) { ?>
                            <div class="banner__item">
                                <?php if ($banners[$i]->translate->url != '') { ?> <a href="<?=Url::to(['site/away', 'url' => $banners[$i]->translate->url])?>" target="_blank"> <?php } ?>
                                    <img src="<?=$banners[$i]->translate->image?>" class="banner__img" title="<?=$banners[$i]->translate->name?>">
                                    <?php if ($banners[$i]->translate->url != '') { ?> </a> <?php } ?>
                            </div>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?= $this->render('footer.php') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
