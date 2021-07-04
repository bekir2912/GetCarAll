<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $brand->name;


?>
<a href="<?= Url::to(['wiki/list', 'cat_id' => $brand->category_id]) ?>"
   class="all_news_link"><?= Yii::t('frontend', 'All brands') ?></a>
<div class="news_block">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="news_block_title"><?= $brand->name ?>
            </h4>

            <form class="form-inline" >
                <div class="form-group" style="width: 100%;">
                    <label class="sr-only" for="exampleInputAmount"><?=Yii::t('frontend', 'Lineup')?></label>
                    <div class="input-group" style="width: 100%;">
                        <input type="hidden" name="id" value="<?=$brand->id?>">
                        <input type="text" name="q" class="form-control" value="<?=$q?>" placeholder="<?=Yii::t('frontend', 'Lineup')?>">
                        <div class="input-group-addon" style="padding: 0;border: 0;"><button type="submit" style="width: 100%" class="btn btn-success"><i class="fa fa-search"></i></button></div>
                    </div>
                </div>
            </form>
            <p></p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php if (!empty($brand->lineups)) {
                $lineups = $brand->lineups;
                usort($lineups, function ($a, $b) {
                    return strcmp($a->translate->name, $b->translate->name);
                });
                ?>
                <div class="SubcategoriesList Subcategories">
                    <?php
                    $q = mb_strtolower($q);
                    for ($i = 0; $i < count($lineups); $i++) {
                        if ($q != '' && (mb_strpos(mb_strtolower($lineups[$i]->translate->name), $q) === false)) {
                            continue;
                        }
                        ?>
                        <div class="SubcategoriesList-item">
                            <a class="navigation__dropdown-link"
                               href="<?= Url::to(['wiki/show', 'id' => $lineups[$i]->id]) ?>"><?= $lineups[$i]->translate->name ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="col-lg-12 text-center">
                    <span class="text-muted">
                        <?= Yii::t('frontend', 'Nothing to show') ?>
                    </span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>