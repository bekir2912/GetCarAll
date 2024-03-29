<?php
/* @var $this yii\web\View */


use common\models\Category;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

$currency = Yii::$app->session->get('currency', 'uzs');

$this->title = $category->translate->name;
$breads = [];

$temp_parent = $category;
while($temp_parent){
    $breads[] = $temp_parent;
    if(!$temp_parent->parent) break;
    $temp_parent = $temp_parent->parent;
}
$breads = array_reverse($breads);


$this->registerMetaTag([
    'name' => 'description',
    'content' => Html::encode(strip_tags($category->translate->description)),
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Html::encode(strip_tags($category->translate->description)),
]);

$sort = [
    'price' => [
        'label' => 'price',
        'get' => ['s' => 'p', 'sd' => (Yii::$app->request->get('d') == 'a' ? 'd' : 'a')],
    ],
    'view' => [
        'label' => 'view',
        'get' => ['s' => 'v', 'sd' => (Yii::$app->request->get('d') == 'a' ? 'd' : 'a')],
    ],
];

if (Yii::$app->request->get('s') == 'p' ||
    Yii::$app->request->get('s') == 'v' ||
    Yii::$app->request->get('s') == 'd'
) {
    if (Yii::$app->request->get('sd') == 'a') {
        $fa_sort_icon[Yii::$app->request->get('s')] = FA::i('sort-amount-asc');
    } elseif (Yii::$app->request->get('sd') == 'd') {
        $fa_sort_icon[Yii::$app->request->get('s')] = FA::i('sort-amount-desc');
    } else $fa_sort_icon[Yii::$app->request->get('s')] = FA::i('sort-amount-desc');

} else $fa_sort_icon['d'] = FA::i('sort-amount-desc');

if(Yii::$app->user->id){
    $userFav = \common\models\UserFavorite::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC')->all();
    $product_ids = array();
    if(!empty($userFav)) {
        for ($i = 0; $i < count($userFav); $i++) {
            $product_ids[] = $userFav[$i]->product_id;
        }
    }
}
else {
    $product_ids = !empty(Yii::$app->session->get('product_ids')) ? Yii::$app->session->get('product_ids') : array();
}

$banners = \common\models\Banner::find()->where(['status' => 1, 'type' => 1])->andWhere(['>', 'expires_in', time()])->orderBy('order')->limit(5)->all();
?>
<?php if (count($breads) > 1) { ?>
    <div class="bread-crumbs">
        <ul class="bread-crumbs__list">
            <?php for($i = 0; $i < count($breads); $i++) { ?>
            <li class="bread-crumbs__item">
                <?php if ($i + 1 != count($breads)) { ?>
                <a href="<?=Url::to(['category/index', 'id' => $breads[$i]->url])?>" class="bread-crumbs__link">
                <?php } ?>
                    <?=$breads[$i]->translate->name?>
                <?php if ($i + 1 != count($breads)) { ?>
                </a>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
<?php if ($category->activeCategories) { ?>
    <div class="SubcategoriesList Subcategories">
        <?php for ($s = 0; $s < count($category->activeCategories); $s++) { ?>
            <div class="SubcategoriesList-item">
                <a class="navigation__dropdown-link" href="<?=Url::to(['category/index', 'id' => $category->activeCategories[$s]->url])?>"><?=$category->activeCategories[$s]->translate->name?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<?php Pjax::begin()?>
<?php require_once('right.php') ?>
<?php if(!empty($special_products)) { ?>
    <section class="special-offer">
        <h3 class="middle__heading">
            <i class="flaticon-like-1"></i>
            <?=Yii::t('frontend', 'Special offers')?>
        </h3>
        <div class="row">
        <div class="col-lg-12">
            <ul class="product-table">
            <?php foreach ($special_products as $product) {?>
            <li class="product-table__item <?=($product->colored_offer > time())? 'colored_offer':'' ?>">
                <div class="product-table__block">
                    <a href="<?=Url::to(['product/index', 'id' => $product->url])?>" class="product-table__link"></a>
                    <img src="<?=$product->mainImage->image?>" class="product-table__img">
                    <h3 class="product-table__heading">
                        <?php if($product->brand && $product->brand->logo != '/uploads/site/default_cat.png') { ?>
                            <img src="<?=$product->brand->logo?>" alt="." class="brand_logo_on_product">
                        <?php } ?>
                        <?=$product->translate->name?>
                    </h3>
                    <span class="product-table__info">
                                    <?=($product->km > 0)? number_format($product->km, 0, '', ' ').' '.Yii::t('frontend','km'):''?>
                                </span>
                    <p class="product-table__price">
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
                                    <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
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
                                        <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                        <?php break; } ?>
                                <?php } else { ?>
                                    <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                                <?php } ?>

                            <?php } else { ?>
                                <?= $product->showPrice ?>
                            <?php } ?>
                        <?php } ?>
                    </p>
                </div>
            </li>
            <?php } ?>
            </ul>
        </div>
        </div>
        <div class="text-right">
            <a href="<?=Url::current(['special' => 1])?>" class="special-offer__link">
                <?=Yii::t('frontend', 'All Special offers')?> <i class="flaticon-next"></i>
            </a>
        </div>
    </section>
<?php } ?>
<section class="result-of-search clearfix" id="search-result">
    <h3 class="result-of-search__heading">
        <?=Yii::t('frontend', 'Found variants')?>:
        <span class="result-of-search--bold">
                                <?=$pagination->totalCount?>
                            </span>
    </h3>

    <?php if (!empty($products)) { ?>
    <div class="result-of-search__filter-search" >
        <ul class="filter-search__list">
            <li class="filter-search__item hidden-xs">
                <div class="filter-search__dropdown">
                    <a class="filter-search__link sort-link" href="<?= Url::current(['s' => 'v', 'sd' => (Yii::$app->request->get('s') == '') ? 'a' : ((Yii::$app->request->get('s') == 'v' && Yii::$app->request->get('sd') == 'd') ? 'a' : 'd')]) ?>">
                        <?= Yii::t('frontend', 'By popularity') ?>
                        <?= (isset($fa_sort_icon['v']) ? $fa_sort_icon['v'] : '') ?>
                    </a>
                </div>
            </li>
            <li class="filter-search__item">
                <div class=" filter-search__dropdown">
                    <a  class="filter-search__link sort-link" href="<?= Url::current(['s' => 'p', 'sd' => ((Yii::$app->request->get('s') == 'p' && Yii::$app->request->get('sd') == 'a') ? 'd' : 'a')]) ?>">
                        <span class="hidden-xs">
                            <?= Yii::t('frontend', 'By price') ?>
                        </span>
                        <i class="fa fa-dollar d-md-none d-lg-none d-xl-none" style="color: #d91b30;"></i>
                        <?= (isset($fa_sort_icon['p']) ? $fa_sort_icon['p'] : '') ?>
                    </a>
                </div>
            </li>
            <li class="filter-search__item">
                <div class=" filter-search__dropdown">
                    <a  class="filter-search__link sort-link" href="<?= Url::current(['s' => 'd', 'sd' => ((Yii::$app->request->get('s') == 'd' && Yii::$app->request->get('sd') == 'd') ? 'a' : 'd')]) ?>">
                        <span class="hidden-xs">
                            <?= Yii::t('frontend', 'By date') ?>
                        </span>
                        <i class="fa fa-clock-o d-md-none d-lg-none d-xl-none" style="color: #d91b30;"></i>
                        <?= (isset($fa_sort_icon['d']) ? $fa_sort_icon['d'] : '') ?>
                    </a>
                </div>
            </li>
<!--            <li class="filter-search__item">-->
<!--                <div class="dropdown filter-search__dropdown">-->
<!--                    <a data-toggle="dropdown" class="filter-search__link" href="#">-->
<!--                        Валюта: <span>uzd</span>-->
<!--                        <i class="flaticon-download"></i>-->
<!--                    </a>-->
<!--                    <ul class="filter-search__dropdown-menu dropdown-menu" role="menu" aria-labelledby="dLabel">-->
<!--                        <li>-->
<!--                            uzd-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            uzd-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            uzd-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </li>-->
        </ul>
<!--        <div class="position-result">-->
<!--            <ul class="position-result__list">-->
<!--                <li class="position-result__item active">-->
<!--                    <i class="flaticon-menu"></i>-->
<!--                </li>-->
<!--                <li class="position-result__item">-->
<!--                    <i class="flaticon-lists"></i>-->
<!--                </li>-->
<!--            </ul>-->
<!--        </div>-->
    </div>


        <div class="clearfix"></div>
    <div class="product-list clearfix">
        <?php
        $bc = 0;
        for ($i = 0; $i < count($products); $i++) { ?>
            <div class="product-list__item <?=$products[$i]->colored_offer > time()? 'colored_offer': ''?>">
                <a href="<?=Url::to(['product/index', 'id' => $products[$i]->url])?>" data-pjax="0" class="product-list__link"></a>
                <div class="product-list__heading text-left  d-lg-none d-xl-none" >
                    <?= $products[$i]->translate->name ?>
                </div>
                <p class="product-list__price d-lg-none d-xl-none">
                    <?php if ($products[$i]->price_type == 0) {
                        $wholesales = json_decode($products[$i]->wholesale);
                        ?>
                        <?php if ($products[$i]->price == 0) { ?>
                            <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                        <?php } else { ?>
                            <?= $products[$i]->showPrice ?>
                        <?php } ?>
                    <?php } elseif ($products[$i]->price_type == 1) {
                        $wholesales = json_decode($products[$i]->wholesale);
                        ?>
                        <?php if (!empty($wholesales)) { ?>
                            <?php foreach ($wholesales as $ws_count => $sum) {

                                $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                ?>
                                <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                <?php break; } ?>
                        <?php } else { ?>
                            <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                        <?php } ?>
                    <?php } elseif ($products[$i]->price_type == 2) {
                        $wholesales = json_decode($products[$i]->wholesale);
                        ?>
                        <?php if ($products[$i]->price == 0) { ?>
                            <?php if (!empty($wholesales)) { ?>
                                <?php foreach ($wholesales as $ws_count => $sum) {
                                    $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                    $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                    ?>
                                    <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                    <?php break; } ?>
                            <?php } else { ?>
                                <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                            <?php } ?>

                        <?php } else { ?>
                            <?= $products[$i]->showPrice ?>
                        <?php } ?>
                    <?php } ?>
                </p>
                <img src="<?= $products[$i]->mainImage->image ?>" class="product-list__img">
                <div class="product-list__block">
                    <h3 class="product-list__heading hidden-xs d-md-none" >
                        <?= $products[$i]->translate->name ?>
                    </h3>
                    <p class="product-list__price hidden-xs d-md-none">
                        <?php if ($products[$i]->price_type == 0) {
                            $wholesales = json_decode($products[$i]->wholesale);
                            ?>
                            <?php if ($products[$i]->price == 0) { ?>
                                <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                            <?php } else { ?>
                                <?= $products[$i]->showPrice ?>
                            <?php } ?>
                        <?php } elseif ($products[$i]->price_type == 1) {
                            $wholesales = json_decode($products[$i]->wholesale);
                            ?>
                            <?php if (!empty($wholesales)) { ?>
                                <?php foreach ($wholesales as $ws_count => $sum) {

                                    $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                    $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                    ?>
                                    <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                    <?php break; } ?>
                            <?php } else { ?>
                                <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                            <?php } ?>
                        <?php } elseif ($products[$i]->price_type == 2) {
                            $wholesales = json_decode($products[$i]->wholesale);
                            ?>
                            <?php if ($products[$i]->price == 0) { ?>
                                <?php if (!empty($wholesales)) { ?>
                                    <?php foreach ($wholesales as $ws_count => $sum) {
                                        $ws_price = (preg_match('/\./i', $sum)) ? number_format($sum, Yii::$app->params['price_dec']['decimals'], Yii::$app->params['price_dec']['dec_pointer'], Yii::$app->params['price_dec']['thousands_sep']) : number_format($sum, Yii::$app->params['price']['decimals'], Yii::$app->params['price']['dec_pointer'], Yii::$app->params['price']['thousands_sep']);
                                        $ws_price = preg_replace('/,00$/i', '', $ws_price);
                                        ?>
                                        <?= $ws_price ?> <?= Yii::t('frontend', $currency) ?>
                                        <?php break; } ?>
                                <?php } else { ?>
                                    <?= Yii::t('frontend', 'Specify prices from the seller') ?>
                                <?php } ?>

                            <?php } else { ?>
                                <?= $products[$i]->showPrice ?>
                            <?php } ?>
                        <?php } ?>
                    </p>
                    <?php if (!empty($products[$i]->activeOptions)) {
                        $loop = 1; ?>
                        <div class="product-list__info">
                            <div class="row">
                                <?php foreach ($products[$i]->activeOptions as $option) {
                                    if ($option->option->group->main == 0) continue;
                                    ?>
                                <div class="col-6">
                                    <?= $option->option->translate->name ?>
                                </div>
                                    <?php if($loop % 2 == 0) { ?>
                            </div>
                            <div class="row">
                                    <?php } ?>
                                    <?php $loop++; } ?>
                            </div>
                            <?=($products[$i]->km > 0)? number_format($products[$i]->km, 0, '', ' ').' '.Yii::t('frontend','km'):''?>
                        </div>
                    <?php } ?>
                    <p class="product-list__time">
                        <?php
                        if($products[$i]->user_id) {
                            $city_name = $products[$i]->user->city->translate->name;
                        } else {
                            $cities = json_decode($products[$i]->shop->cities);
                            $city_name = '';
                            if (!empty($cities)) {
                                foreach ($cities as $city) {
                                    $db_city = \common\models\City::findOne(['id' => $city, 'status' => 1]);
                                    if($db_city) {
                                        $city_name .= $db_city->translate->name.', ';
                                    }
                                }
                                $city_name = mb_substr($city_name, 0, -2);
                            }
                        }
                        ?>
                        <?= ($city_name)? $city_name.',': '' ?> <?= date('d.m.Y H:i', $products[$i]->created_at) ?>
                    </p>
                    <div class="product-lis__like-icon">
                        <?php if($products[$i]->brand && $products[$i]->brand->logo != '/uploads/site/default_cat.png') { ?>
                            <img src="<?=$products[$i]->brand->logo?>" alt="." class="brand_logo_on_product">
                        <?php } ?>
                        <?php if (in_array($products[$i]->id, $product_ids)) { ?>
                            <button class="btn add_fav" style="background: #d91b30;" data-prod-id="<?=$products[$i]->id?>" data-action="remove">
                                <i class="flaticon-like " style="color: #fff"></i>
                            </button>
                        <?php } else { ?>
                            <button class="btn add_fav" style="background: transparent;" data-prod-id="<?=$products[$i]->id?>" data-action="add">
                                <i class="flaticon-like " ></i>
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if(($i + 1) % 5 == 0) { ?>
                <div class="mobile_banner__item d-lg-none d-md-none d-xl-none">
                    <?php if ($banners[$bc]->translate->url != '') { ?> <a href="<?=Url::to(['site/away', 'url' => $banners[$bc]->translate->url])?>" target="_blank"> <?php } ?>
                        <img src="<?=$banners[$bc]->translate->image?>" class="mobile_banner__item img-fluid" title="<?=$banners[$bc]->translate->name?>">
                        <?php if ($banners[$bc]->translate->url != '') { ?> </a> <?php } ?>
                </div>
            <?php $bc++;} ?>
        <?php } ?>
    </div>
    <div class="pagination-block">
        <?php echo LinkPager::widget([
            'pagination' => $pagination,
        ]); ?>
    </div>
    <?php } ?>
    <?php
        $root = Category::find()->where(['status' => 1, 'parent_id' => null])->orderBy('order')->one();
        if ($root->id == $category->id) {
            echo \frontend\widgets\WNews::widget();
        }
        ?>

</section>

<?php Pjax::end()?>