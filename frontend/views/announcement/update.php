<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = Yii::t('frontend', 'Announce');
?>
<section class="lk-setting">
    <div class="page-header"><?= Html::encode($model->translate->name) ?>
        <span class="text-muted">|</span>
        <small class="text-secondary">
            <?= FA::i('eye') ?> <?= $model->view ?>
        </small>
        <?php if ($model->status == 1 || $model->status == 2) { ?>
            <div class="pull-right" style="font-size: 13px;">
                <a href="<?=Yii::$app->params['domain']?>/product/<?= $model->url ?>" target="_blank"><?=Yii::t('frontend', 'Page on site')?> <i
                            class="fa fa-share"></i></a>
            </div>
        <?php } ?>
    </div>
    <p></p>
    <div class="row">
        <div class="col-lg-12">
            <?php if($model->colored_offer > time()) { ?>
                <div class="alert alert-success" role="alert">
                    <?=Yii::t('frontend', 'Activated boost')?> <strong>"<i class="fa fa-paint-brush" style="color: #6e7bfe"></i> <?=Yii::t('frontend', 'Colored offer')?>"</strong> <?=mb_strtolower(Yii::t('frontend', 'To'))?> <?=date('d.m.Y H:i', $model->colored_offer)?>
                </div>
            <?php } ?>
            <?php if($model->special_offer > time()) { ?>
                <div class="alert alert-success" role="alert">
                    <?=Yii::t('frontend', 'Activated boost')?> <strong>"<i class="fa fa-star" style="color: #ffc720"></i> <?=Yii::t('frontend', 'Special offer')?>"</strong> <?=mb_strtolower(Yii::t('frontend', 'To'))?> <?=date('d.m.Y H:i', $model->special_offer)?>
                </div>
            <?php } ?>

            <?php if(!$model->isNewRecord && $model->status == 0) { ?>
                <div class="alert alert-danger" >
                    <strong><?=Yii::t('frontend', 'Announce is turned off')?></strong>
                </div>
            <?php } ?>
            <?php if(!$model->isNewRecord && $model->status == -1) { ?>
                <div class="alert alert-danger" >
                    <strong><?=Yii::t('frontend', 'Blocked')?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'category' => $category,
        'brand' => $brand,
        'lineup' => $lineup,
        'info' => $info,
        'info_uz' => $info_uz,
        'info_oz' => $info_oz,
        'info_en' => $info_en,
    ]) ?>

</section>
