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



    <div class="find">
        <div class="filter">
            <div class="container">
                <div class="filterBrand">
<!--                    <div class="filterBrandBox">-->
<!--                        <span>Выберите</span>-->
<!--                        <h3>Бренд</h3>-->
<!--                    </div>-->
                </div>
                <?php require_once('right.php') ?>

            </div>
            <div class="container">
                <?php if(!empty($brands) && empty(array_filter($checked_brands))) { ?>
                    <div class="d-comp">
                        <div class="filterRadios row">
                            <?php foreach ($brands as $brand_id => $brand) {
                            if(!(isset($brands_count[$brand_id])) || $brands_count[$brand_id] <=0  ) continue; ?>
                                        <a href="<?= Url::current(['brands' => [$brand_id]]) ?>" class="col-md-1">
                                            <div class="filterRadiosBox">
                                                <label for="filter1" class="filterRadiosBoxLabel">
                                                    <div class="filterRadiosBoxLabelLogo">
                                                        <img src="<?=$brand->logo?>" alt="<?= $brand->name ?>" style="width: 69px; height: 69px;border-radius: 100%;">
                                                    </div>
                                                    <div class="filterRadiosBoxText">
                                                        <h4><?= $brand->name ?></h4>
                                                    </div>
                                                </label>
                                            </div>
                                        </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="d-mob">
                        <div class="swiper-container swiper-container2">
                            <div class="swiper-wrapper">
                                <?php foreach ($brands as $brand_id => $brand) {
                                if(!(isset($brands_count[$brand_id])) || $brands_count[$brand_id] <=0  ) continue; ?>
                                            <a href="<?= Url::current(['brands' => [$brand_id]]) ?>" class="swiper-slide">
                                                <div class="filterRadiosBox">
                                                    <label for="filter1" class="filterRadiosBoxLabel">
                                                        <div class="filterRadiosBoxLabelLogo">
                                                            <img src="<?=$brand->logo?>" alt="<?= $brand->name ?>" style="width: 69px; height: 69px;border-radius: 100%;">
                                                        </div>
                                                        <div class="filterRadiosBoxText">
                                                            <h4><?= $brand->name ?></h4>
                                                        </div>
                                                    </label>
                                                </div>
                                            </a>
                                <?php } ?>
                            </div>
                            <div class="swiper-button-next">
                                <img src="/gta/images/next.png">
                            </div>
                            <div class="swiper-button-prev">
                                <img src="/gta/images/return.png">
                            </div>
                        </div>
                    </div>
                <?php } else if(!empty($lineups) && empty(array_filter($checked_lineups))) { ?>

    <div class="filterModels">
    <?php foreach ($lineups as $lineup_id => $lineup) {
        if(!(isset($lineups_count[$lineup_id])) || $lineups_count[$lineup_id] <=0 ) continue; ?>
            <a href="<?=Url::current(['lineups' => [$lineup_id]])?>">
                <div class="filterRadiosBox">
                    <label for="filter1" class="filterRadiosBoxLabel">
                        <div class="filterRadiosBoxText">
                            <h4><?= $lineup->translate->name ?></h4>
                        </div>
                    </label>
                </div>
            </a>
    <?php } ?>
    </div>
<?php } ?>
            </div>
            <div class="container">
                <div class="fintTop">
                    <div class="fintTopInfo">
                        <h3><?=Yii::t('frontend', 'Found variants')?> (<?=$pagination->totalCount?>)</h3>
                    </div>
                    <div class="fintTopFilter">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?= (isset($fa_sort_icon['v']) ? 'checked' : '') ?> type="radio" name="inlineRadioOptions" id="inlineRadio1" value="<?= Url::current(['s' => 'v', 'sd' => (Yii::$app->request->get('s') == '') ? 'a' : ((Yii::$app->request->get('s') == 'v' && Yii::$app->request->get('sd') == 'd') ? 'a' : 'd')]) ?>">
                            <label class="form-check-label" for="inlineRadio1"><?= Yii::t('frontend', 'By popularity') ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?= (isset($fa_sort_icon['p']) ? 'checked' : '') ?> type="radio" name="inlineRadioOptions" id="inlineRadio2" value="<?= Url::current(['s' => 'p', 'sd' => ((Yii::$app->request->get('s') == 'p' && Yii::$app->request->get('sd') == 'a') ? 'd' : 'a')]) ?>">
                            <label class="form-check-label" for="inlineRadio2"><?= Yii::t('frontend', 'By price') ?></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" <?= (isset($fa_sort_icon['d']) ? 'checked' : '') ?> type="radio" name="inlineRadioOptions" id="inlineRadio3" value="<?= Url::current(['s' => 'd', 'sd' => ((Yii::$app->request->get('s') == 'd' && Yii::$app->request->get('sd') == 'd') ? 'a' : 'd')]) ?>">
                            <label class="form-check-label"  for="inlineRadio3"><?= Yii::t('frontend', 'By date') ?></label>
                        </div>
                    </div>
                </div>

<?php if(!empty($special_products)) { ?>
    <?php foreach ($special_products as $product) {?>
                <div class="special <?=($product->colored_offer > time())? 'colored_offer':'' ?>">
                    <div class="specialTop">
                        <div class="specialTopTitle">
                            <span><?=Yii::t('frontend', 'Special offers')?></span>
                        </div>
                        <div class="specialTopFavorite">
                            <?php if (in_array($product->id, $product_ids)) { ?>
                                <button class="btn add_fav" style="background: #d91b30;" data-prod-id="<?=$product->id?>" data-action="remove">
                                    <i class="fa fa-star-o font-star" style="color: #fff"></i>
                                </button>
                            <?php } else { ?>
                                <button class="btn add_fav" style="background: transparent;" data-prod-id="<?=$product->id?>" data-action="add">
                                    <i class="fa fa-star-o font-star" ></i>
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="specialContent">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="specialContentImage">
                                    <img src="<?=$product->mainImage->image?>" alt="<?=$product->translate->name?>" width="100%">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="specialContentText">
                                    <div class="specialContentTextTop">
                                        <div class="specialContentTextTopTitle">
                                            <h2><?=$product->translate->name?> <br />
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
                                            </h2>
                                        </div>
                                        <?php if (!empty($products[$i]->activeOptions)) {
                                            $loop = 1; ?>
                                                <div class="specialContentTextTopItem">
                                                    <?php foreach ($products[$i]->activeOptions as $option) {
                                                        
                                                                if($loop > 4) continue;
                                                    if ($option->option->group->main == 0) continue;
                                                    ?>
                                                        <p><?= $option->option->translate->name ?></p>
                                                    <?php if($loop % 2 == 0) { ?>
                                                </div>
                                                <div class="specialContentTextTopItem">
                                                    <?php } ?>
                                                    <?php $loop++; } ?>
                                                </div>
                                                <?=($products[$i]->km > 0)? number_format($products[$i]->km, 0, '', ' ').' '.Yii::t('frontend','km'):''?>
                                        <?php } ?>
                                    </div>
                                    <div class="specialContentTextDes">
                                        <p>
                                            <?=($product->translate->description != '')? nl2br(mb_strlen($product->translate->description) > 255 ? mb_substr($product->translate->description, 0, 250): $product->translate->description): '<div class="text-center">'.Yii::t('frontend', 'No info').'</div>' ?>
                                        </p>
                                    </div>
                                    <div class="specialContentTextButton">
                                        <a href="<?=Url::to(['product/index', 'id' => $product->url])?>" class="btn1">
                                            <?= Yii::t('frontend', 'Details') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php } ?>
<?php } ?>
                <div class="result">
                    <div class="row">

<?php
$bc = 0;
for ($i = 0; $i < count($products); $i++) { ?>
                        <div class="col-lg-6">
                            <a href="<?=Url::to(['product/index', 'id' => $products[$i]->url])?>" data-pjax="0" class="resultItem <?=($products[$i]->colored_offer > time())? 'colored_offer':'' ?>">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="resultItemImage">
                                            <img src="<?= $products[$i]->mainImage->image ?>" alt="<?= $products[$i]->translate->name ?>" width="100%">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="resultItemText">
                                            <div class="resultItemTextTop">
                                                <span><?php
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
                                                    <?= ($city_name)? $city_name.',': '' ?> <?= date('d.m.Y H:i', $products[$i]->created_at) ?></span>
                                            </div>
                                            <div class="resultItemTextName">
                                                <p><?= $products[$i]->translate->name ?></p>
                                            </div>
                                            <div class="resultItemTextPrice">
                                                <p><?php if ($products[$i]->price_type == 0) {
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
                                                    <?php } ?></p>
                                            </div>
                                            <div class="resultItemTextList">
                                                <?php if (!empty($products[$i]->activeOptions)) {
                                                    $loop = 1; ?>
                                                    <div class="row">
                                                            <?php foreach ($products[$i]->activeOptions as $option) {
                                                                if($loop > 4) continue;
                                                            if ($option->option->group->main == 0) continue;
                                                            ?>
                                                                <div class="col-6"><?= $option->option->translate->name ?></div>
                                                            <?php $loop++; } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="resultItemTextButton">
                                                <button class="btn1"><?= Yii::t('frontend', 'Details') ?></button>
                                            </div>
                                            <div class="resultItemTextFavorite">
                                                <?php if (in_array($products[$i]->id, $product_ids)) { ?>
                                                    <button class="btn add_fav" style="background: #d91b30;" data-prod-id="<?=$products[$i]->id?>" data-action="remove">
                                                        <i class="fa fa-star-o font-star" style="color: #fff"></i>
                                                    </button>
                                                <?php } else { ?>
                                                    <button class="btn add_fav" style="background: transparent;" data-prod-id="<?=$products[$i]->id?>" data-action="add">
                                                        <i class="fa fa-star-o font-star" ></i>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php if($i + 1 % 2 == 0) { ?>
                        </div>
                        <div class="row">
                        <?php } ?>

<?php } ?>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="container">
        <div class="pagination-block">
            <?php echo LinkPager::widget([
                'pagination' => $pagination,
            ]); ?>
        </div>
    </div>

    <?php
        $root = Category::find()->where(['status' => 1, 'parent_id' => null])->orderBy('order')->one();
        if ($root->id == $category->id) {
            echo \frontend\widgets\WNews::widget();
        }
    ?>


<?php Pjax::end()?>