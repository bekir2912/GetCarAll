<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 14.10.2017
 * Time: 2:34
 */

use common\models\Category;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = Yii::t('frontend', 'Wiki');

$cats = Category::find()->where(['status' => 1, 'parent_id' => null, 'on_main' => 0, 'spec' => 0])->orderBy('order')->all();
?>
<?php if(!$filter_cat->activeCategories) { ?>
<form class="form-inline">
    <div class="form-group" style="width: 100%;">
        <label class="sr-only" for="exampleInputAmount"><?=Yii::t('frontend', 'Brand')?></label>
        <div class="input-group" style="width: 100%;">
            <input type="text" name="q" class="form-control" value="<?=$q?>" placeholder="<?=Yii::t('frontend', 'Brand')?>">
            <div class="input-group-addon" style="padding: 0;border: 0;"><button type="submit" style="width: 100%" class="btn btn-success"><i class="fa fa-search"></i></button></div>
        </div>
    </div>
</form>
<p></p>
<?php } ?>

<section class="announcements" style="margin-bottom: 25px;">
    <?php if (!empty($cats)) { ?>
        <div class="announcements__category">
            <ul class="category__list">
                <?php foreach ($cats as $cat) { ?>
                    <li class="category__item">
                        <a href="<?= Url::current(['cat_id' => $cat->id]) ?>"
                           class="category__link <?= ($filter_cat && ($filter_cat->id == $cat->id)) ? ' category__link--active' : '' ?>">
                            <?= $cat->translate->name ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <div class="clearfix add-announcement-block"></div>
</section>

<?php if($filter_cat->activeCategories) { ?>
    <div class="SubcategoriesList Subcategories">
        <?php for ($s = 0; $s < count($filter_cat->activeCategories); $s++) { ?>
            <div class="SubcategoriesList-item">
                <a class="navigation__dropdown-link" href="<?=Url::current(['cat_id' => $filter_cat->activeCategories[$s]->id])?>"><?=$filter_cat->activeCategories[$s]->translate->name?></a>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <?php if($filter_cat->parent_id) { ?>
        <div class="row">
            <div class="col-lg-12">
                <a href="<?=Url::current(['cat_id' => $filter_cat->parent_id])?>">
                    <i class="fa fa-chevron-left"></i> <?=Yii::t('frontend', 'Back')?>
                </a> <span>| <?=$filter_cat->translate->name?></span>
            </div>
        </div>
    <?php } ?>
    <div class="row">
    <?php if (!empty($brands)) { ?>
        <?php for ($i = 0; $i < count($brands); $i++) { ?>
            <div class="col-lg-2 col-md-3 col-6">
                <a href="<?=Url::to(['wiki/index', 'id' => $brands[$i]->id])?>" class="">
                    <div style="margin-bottom: 20px;">
                        <img src="<?= $brands[$i]->logo ?>"
                             alt="<?= $brands[$i]->name ?>" class="img-fluid" >
                        <p class="shell_news_title text-center"><?= $brands[$i]->name ?></p>
                    </div>
                </a>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-lg-12 text-center">
            <span class="text-muted">
                <?=Yii::t('frontend', 'Nothing to show')?>
            </span>
        </div>
    <?php } ?>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <?= LinkPager::widget([
                'pagination' => $pages,
            ]); ?>
        </div>
    </div>
<?php } ?>
