<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 26.09.2017
 * Time: 8:01
 */

use common\models\Callback;
use common\models\Lineup;
use common\models\User;
use frontend\widgets\WProduct;
use newerton\fancybox3\FancyBox;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = $product->translate->name;

$currency = Yii::$app->session->get('currency', 'uzs');

$this->registerMetaTag([
    'name' => 'description',
    'content' => Html::encode(strip_tags($product->translate->description)),
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Html::encode(strip_tags($product->translate->description)),
]);

$breads = [$product];
if($product->lineup_id) {
    $lineup = Lineup::findOne(['id' => $product->lineup_id, 'status' => 1]);
    if ($lineup) {
        $breads[] = [
            'name' => $lineup->translate->name,
            'url' => Url::to(['category/index', 'id' => $product->category->url,
                'brands[]' => $product->brand_id,
                'lineups[]' => $product->lineup_id,
            ]),
        ];
    }
}
if($product->brand_id) {
    $brand = \common\models\Brand::findOne(['id' => $product->brand_id, 'status' => 1]);
    if ($brand) {
        $breads[] = [
            'name' => $brand->name,
            'url' => Url::to(['category/index', 'id' => $product->category->url, 'brands[]' => $product->brand_id]),
        ];
    }
}
$temp_parent = $product->category;
while($temp_parent){
    $breads[] = $temp_parent;
    if(!$temp_parent->parent) break;
    $temp_parent = $temp_parent->parent;
}
$breads = array_reverse($breads);


if (Yii::$app->user->id) {
    $userFav = \common\models\UserFavorite::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC')->all();
    $product_ids = array();
    if (!empty($userFav)) {
        for ($i = 0; $i < count($userFav); $i++) {
            $product_ids[] = $userFav[$i]->product_id;
        }
    }
} else {
    $product_ids = !empty(Yii::$app->session->get('product_ids')) ? Yii::$app->session->get('product_ids') : array();
}

if (Yii::$app->user->id) {
    $user = User::findOne(Yii::$app->user->id);
}
?>


    <div class="productSingle">
        <div class="container">

            <?php if (count($breads) > 1) { ?>
                <div class="productSingleBreadcumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <?php for($i = 0; $i < count($breads); $i++) { ?>
                                <li class="breadcrumb-item">
                                    <?php if ($i + 1 != count($breads)) { ?>
                                        <a href="<?=(isset($breads[$i]->url))? Url::to(['category/index', 'id' => $breads[$i]->url]): $breads[$i]['url']?>">
                                            <?php } ?>
                                            <?=(isset($breads[$i]->translate))? $breads[$i]->translate->name: $breads[$i]['name']?>
                                            <?php if ($i + 1 != count($breads)) { ?>
                                        </a>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ol>
                    </nav>
                    
                </div>
            <?php } ?>
            <div class="productSingleMain">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="productSingleMainGallery">
                            <div class="sswiper-container gallery-top">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide" style="background-image:url(<?= $product->mainImage->image ?>)"></div>
                                    <?php if (count($product->otherImages) > 1) { ?>
                                        <?php for ($i = 0; $i < count($product->otherImages); $i++) { ?>
                                            <div class="swiper-slide" style="background-image:url(<?= $product->otherImages[$i]->image ?>)"></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="swiper-container gallery-thumbs">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide" style="background-image:url(<?= $product->mainImage->image ?>)"></div>
                                    <?php if (count($product->otherImages) > 1) { ?>
                                        <?php for ($i = 0; $i < count($product->otherImages); $i++) { ?>
                                            <div class="swiper-slide" style="background-image:url(<?= $product->otherImages[$i]->image ?>)"></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="productSingleMainContent">
                            <div class="productSingleMainContentTop">
                                <div class="productSingleMainContentTopPrice">
                                    <h4><?=Yii::t('frontend', 'Price')?> <strong>
                                            <?php if ($product->price_type == 0) {
                                                $wholesales = json_decode($product->wholesale);
                                                ?>
                                                <?php if ($product->price == 0) { ?>
                                                    <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                                                <?php } else { ?>
                                                    <?= $product->showPrice ?>
                                                <?php } ?>
                                            <?php } elseif ($product->price_type == 1) {
                                                $wholesales = json_decode($product->wholesale);
                                                ?>
                                                <?php if (!empty($wholesales)) { ?>
                                                    <?php foreach ($wholesales as $ws_count => $sum) {

                                                        $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                                        $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                                        ?>
                                                        <?= $ws_price ?> <span><?= Yii::t('frontend', $currency) ?></span>
                                                        <?php break; } ?>
                                                <?php } else { ?>
                                                    <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                                                <?php } ?>
                                            <?php } elseif ($product->price_type == 2) {
                                                $wholesales = json_decode($product->wholesale);
                                                ?>
                                                <?php if ($product->price == 0) { ?>
                                                    <?php if (!empty($wholesales)) { ?>
                                                        <?php foreach ($wholesales as $ws_count => $sum) {
                                                            $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                                            $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                                            ?>
                                                            <?= $ws_price ?> <span><?= Yii::t('frontend', $currency) ?></span>
                                                            <?php break; } ?>
                                                    <?php } else { ?>
                                                        <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                                                    <?php } ?>

                                                <?php } else { ?>
                                                    <?= $product->showPrice ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if (!empty($wholesales)) { ?>
                                                <br>
                                                <a href="#" class="show_wholesale_link" data-toggle="modal" data-target="#sholesaleModal"><?=Yii::t('frontend', 'Wholesales')?></a>

                                                <div class="modal fade" id="sholesaleModal" tabindex="-1" role="dialog" aria-labelledby="sholesaleModal">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel"><?=Yii::t('frontend', 'Wholesales')?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <?php foreach ($wholesales as $ws_count => $sum) {
                                                                            $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                                                            $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <?=Yii::t('frontend', 'From')?> <?= $ws_count ?> <?=Yii::t('frontend', 'unit')?>
                                                                                </td>
                                                                                <td>
                                                                                    <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </strong></h4>
                                </div>
                                <div class="productSingleMainContentTopIcon">
                                    <img src="/gta/images/bottom.svg" alt="">
                                </div>
                            </div>
                            <?php if($product->shop_id) { ?>
                            <a href="<?= Url::to(['shop/index', 'id' => $product->shop->url, 'cat' => $product->category->url]) ?>" data-pjax="0" style="color: inherit">
                                <div class="productSingleMainContentAuthor">
                                    <div class="productSingleMainContentAuthorImage">
                                        <img src="<?=$product->shop->logo?>" style="width: 74px;height: 74px;border-radius: 50%; border: 1px solid #1A1919">
                                    </div>
                                    <div class="productSingleMainContentAuthorText">
                                        <h5><?=$product->shop->name?></h5>
                                        <h6><?=Yii::t('frontend', 'Verified dealer')?></h6>
                                        <div class="productSingleMainContentAuthorTextAddress">
                                            <?php if($product->shop->info->address != '') { ?>
                                            <img src="/gta/images/address.svg" alt="">
                                                <?=$product->shop->info->address?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="productSingleMainContentAuthorFavorite">
                                        <?php if (in_array($product->id, $product_ids)) { ?>
                                            <button class="button-list__item add_fav" style="background: #d91b30;" data-prod-id="<?=$product->id?>" data-action="remove">
                                                <i class="fa fa-star-o font-star " style="color: #fff"></i>
                                            </button>
                                        <?php } else { ?>
                                            <button class="button-list__item add_fav" data-prod-id="<?=$product->id?>" data-action="add">
                                                <i class="fa fa-star-o font-star " ></i>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                            <?php } ?>

                            <div class="productSingleMainContentOptions">

                                <div class="row">
<?php if (!empty($product->activeOptions)) {
    $loop = 1;
    $temp = '';
    ?>
    <?php foreach ($product->activeOptions as $option) {
        if ($option->option->group->main == 0) continue;
        if($option->option->group->translate->name == $temp) {
            $temp_product .= ', '.$option->option->translate->name;
        } else { $temp_product = $option->option->translate->name; ?>
            <div class="col-6">
                <div class="productSingleMainContentOptsionsItem">
                    <div class="productSingleMainContentOptsionsItemText">
                <h6><?=$option->option->group->translate->name ?></h6><h5>
        <?php } ?>

            <?=($option->option->group->translate->name == $temp ? ', '.$option->option->translate->name : $option->option->translate->name)?>

        <?php if($option->option->group->translate->name == $temp) {
            continue;
        } else { $temp_product = $option->option->translate->name; ?>
            </h5>
            </div>
            </div>
            </div>
        <?php } ?>

        <?php $loop++; $temp = $option->option->group->translate->name;} ?>
<?php } ?>
                                    <?php if($product->km > 0) { ?>
                                        <div class="col-6">
                                            <div class="productSingleMainContentOptsionsItem">
                                                <div class="productSingleMainContentOptsionsItemText">
                                                    <h6><?=Yii::t('frontend','mileage')?></h6>
                                                    <h5><?=(number_format($product->km, 0, '', ' ').' '.Yii::t('frontend','km'))?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                            <div class="productSingleMainContentContacts">
                                <?php $im_type = $product->shop_id? 'shop': 'user'; ?>
                                <?php $im_id = $product->shop_id? $product->shop_id: $product->user_id; ?>
                                <?php $im = base64_encode($im_type.':'.$im_id); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?=Url::to(['user/messages', 'im' => $im])?>" class="productSingleMainContentContactsMessage">
                                            <img src="/gta/images/message.svg" alt="">
                                            <?=Yii::t('frontend', 'Write message')?>
                                        </a>
                                    </div>
<?php if (($product->shop_id && $product->shop->info->phone != '') || ($product->user_id && $product->user->phone != '')) { ?>
                                    <div class="col-md-6">
                                        <a data-prod="<?=$product->id?>" class="productSingleMainContentContactsPhone show_phone text-white" style="cursor: pointer" >
                                            <img src="/gta/images/phone.svg" alt="">
                                            <?=Yii::t('frontend', 'Show number')?>
                                        </a>
                                    </div>
<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="productSingleAdd">
                <div class="row">
                    <div class="col-md-9">
                        <nav>
                            <div class="nav nav-tabs productSingleTabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><?=Yii::t('frontend', 'Seller description')?></a>
<?php if($category_spec == 1) { ?>
                                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?=Yii::t('frontend', 'Delivery')?></a>
                                <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false"><?=Yii::t('frontend', 'Warranty')?></a>

<?php } else { ?>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?=Yii::t('frontend', 'Product Options')?></a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false"><?=Yii::t('frontend', 'Lineup Info')?></a>
<?php } ?>
                            </div>
                        </nav>
                        <div class="tab-content productSingleTabContent" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <?=($product->translate->description != '')? nl2br($product->translate->description): '<div class="text-center">'.Yii::t('frontend', 'No info').'</div>' ?>
                                <?php
                                $custom_options = [];
                                if(isset($product->custom_options) && $product->custom_options !== '') {
                                    $custom_options = json_decode($product->custom_options, true);
                                }
                                ?>
                                <?php if (is_array($custom_options) && !empty($custom_options)) { ?>
                                    <div class="characteristics">
                                        <ul class="characteristics__list" id="char_list_equipment">
                                            <?php foreach ($custom_options as $group_id => $value) {
//                                if($value == '') continue;
                                                ?>
                                                <li class="characteristics__item">
                                                    <?php
                                                    $group = \common\models\OptionGroup::find()->where(['id' => $group_id])->one();
                                                    if($group) {
                                                        echo $group->translate->name;
                                                    }
                                                    ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <ul class="characteristics__list characteristics__list--bold" id="char_list_equipment">
                                            <?php foreach ($custom_options as $group_id => $value) {
//                                if($value == '') continue;
                                                ?>
                                                <li class="characteristics__item">
                                                    <?=($value != '')? $value: Yii::t('frontend', 'Other')?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
<?php if($category_spec == 1) { ?>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <?=($product->translate->warranty != '')? nl2br($product->translate->warranty): '<div class="text-center">'.Yii::t('frontend', 'No info').'</div>' ?>
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        <?=($product->translate->delivery != '')? nl2br($product->translate->delivery): '<div class="text-center">'.Yii::t('frontend', 'No info').'</div>' ?>
    </div>
<?php } else { ?>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

        <?php if (!empty($product->activeOptions)) { ?>
            <div class="characteristics">
                <ul class="characteristics__list" id="char_list_equipment">
                    <?php
                    $temp = '';
                    foreach ($product->activeOptions as $option) {
                        if($option->option->group->translate->name == $temp) {
                            continue;
                        }
                        ?>
                        <li class="characteristics__item">
                            <?=$option->option->group->translate->name ?>
                        </li>
                        <?php $temp = $option->option->group->translate->name;} ?>
                </ul>
                <ul class="characteristics__list characteristics__list--bold" id="char_list_equipment_equals">
                    <li class="characteristics__item" style="margin: 0">
                        <?php
                        $temp = '';
                        foreach ($product->activeOptions as $option) {
                        if($option->option->group->translate->name == $temp) {
                            ?>
                            , <?= $option->option->translate->name ?>
                            <?php
                        } else { ?>
                    </li>
                    <li class="characteristics__item">
                        <?= $option->option->translate->name ?>
                        <?php } ?>
                        <?php $temp = $option->option->group->translate->name;} ?>
                    </li>
                </ul>
            </div>
        <?php } else { ?>
            <div class="text-center"><?=Yii::t('frontend', 'No info')?></div>
        <?php } ?>
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        <?php if ($product->lineup) { ?>
            <div>
                <?=(($product->lineup->translate->description) != '')? nl2br($product->lineup->translate->description):''?>
            </div>
            <?php if (!empty($product->lineup->activeOptions)) { ?>
                <div class="characteristics">
                    <ul class="characteristics__list" id="char_list_reference">
                        <?php
                        $temp = '';
                        foreach ($product->lineup->activeOptions as $option) {
                            if($option->option->group->translate->name == $temp) {
                                continue;
                            }
                            ?>
                            <li class="characteristics__item">
                                <?=$option->option->group->translate->name ?>
                            </li>
                            <?php $temp = $option->option->group->translate->name;} ?>
                    </ul>
                    <ul class="characteristics__list characteristics__list--bold" id="char_list_reference_equals">
                        <li class="characteristics__item">
                            <?php
                            $temp = '';
                            foreach ($product->lineup->activeOptions as $option) {
                            if($option->option->group->translate->name == $temp) {
                                ?>
                                , <?= $option->option->translate->name ?>
                                <?php
                            } else { ?>
                        </li>
                        <li class="characteristics__item">
                            <?= $option->option->translate->name ?>
                            <?php } ?>
                            <?php $temp = $option->option->group->translate->name;} ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="text-center"><?=Yii::t('frontend', 'No info')?></div>
        <?php } ?>
    </div>
<?php } ?>
                        </div>
                    </div>
                    <div class="col-md-3">
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
        </div>
    </div>
<div class="container">
    <?=WProduct::widget(['id' => $product->id])?>
    <?=WProduct::widget(['id' => $product->id, 'cat_id' => 4])?>
</div>
<?php

$this->registerJs(
        "
            $(document).on('ready pjax:success pjax:error', function() {
                $('a[data-toggle=\"tab\"]').on('shown.bs.tab', function (e) {
                  e.target // newly activated tab
                  e.relatedTarget // previous active tab
                  if($(e.target).attr('href') == '#equipment') {
                      $('#char_list_equipment > li').each(function(i, v) {
                        if($(this).height() < $($('#char_list_equipment_equals > li')[i + 1]).height()) {
                            $(this).height($($('#char_list_equipment_equals > li')[i + 1]).height());
                        } else {
                            $($('#char_list_equipment_equals > li')[i + 1]).height($(this).height());
                        }
                      });
                  }
                  
                  if($(e.target).attr('href') == '#reference') {
                      $('#char_list_reference > li').each(function(i, v) {
                        if($(this).height() < $($('#char_list_reference_equals > li')[i + 1]).height()) {
                            $(this).height($($('#char_list_reference_equals > li')[i + 1]).height());
                        } else {
                            $($('#char_list_reference_equals > li')[i + 1]).height($(this).height());
                        }
                      });
                  }
                })
                
                  $('#char_list_main > li').each(function(i, v) {
                    if($(this).height() < $($('#char_list_main_equals > li')[i]).height()) {
                        $(this).height($($('#char_list_main_equals > li')[i]).height());
                    } else {
                        $($('#char_list_main_equals > li')[i]).height($(this).height());
                    }
                  });
                
            });
        "
);
