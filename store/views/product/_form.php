<?php

use common\models\Brand;
use common\models\Category;
use common\models\Lineup;
use common\models\LineupTranslation;
use common\models\OptionGroup;
use common\models\ProductPerformance;
use common\models\ProductPerformanceTranslation;
use common\models\Sale;
use common\models\Unit;
use kartik\file\FileInput;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\DatePicker;

use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
$model->category_id = $category->id;

$all_cats = Category::find()->where(['parent_id' => null, 'status' => 1])->orderBy('`order`')->limit('2')->all();
$all_brands = Brand::find()->where(['status' => 1, 'category_id' => $category->id])->all();

if (!$model->isNewRecord) {
    $model->price = $model->currency == 'uzs' ? $model->price : $model->price_usd;
    $model->wholesale = $model->currency == 'uzs' ? $model->wholesale : $model->wholesale_usd;
}

$checked_options['id'] = [];
$checked_options['price'] = [];

if ($brand) {
    $model->brand_id = $brand->id;
    $all_lineups = Lineup::find()->where(['status' => 1, 'brand_id' => $brand->id])->all();
}
if ($lineup) {
    $model->lineup_id = $lineup->id;
    if ($model->isNewRecord) {
        $lineup_info = LineupTranslation::findOne((['lineup_id' => $lineup->id, 'local' => 'ru-RU']));
        $lineup_info_uz = LineupTranslation::findOne((['lineup_id' => $lineup->id, 'local' => 'uz-UZ']));
        $lineup_info_en = LineupTranslation::findOne((['lineup_id' => $lineup->id, 'local' => 'en-EN']));
        $lineup_info_oz = LineupTranslation::findOne((['lineup_id' => $lineup->id, 'local' => 'oz-OZ']));

        foreach ($lineup->activeOptions as $lchecked_opt) {
            $checked_options['id'][$lchecked_opt->option_id] = $lchecked_opt->option_id;
            $checked_options['price'][$lchecked_opt->option_id] = $lchecked_opt->price;
        }
    }
}
$all_sales = Sale::find()->where(['shop_id' => Yii::$app->session->get('shop_id'), 'status' => 1])->andWhere('`expire_till` > "' . time() . '"')->all();
$options = OptionGroup::find()->where(['status' => 1, 'category_id' => $category->id, 'type' => 0])->orderBy('order')->all();
$options_multi = OptionGroup::find()->where(['status' => 1, 'category_id' => $category->id, 'type' => 1])->orderBy('order')->all();
$perf_ru = [];
$perf_uz = [];
$perf_en = [];
if (isset($model->id)) {
    $performs = ProductPerformance::find()->where(['product_id' => $model->id])->all();
    if (!empty($performs)) {
        foreach ($performs as $perform) {
            $perf_ru[] = ProductPerformanceTranslation::findOne(['product_performance_id' => $perform->id, 'local' => 'ru-RU']);
            $perf_uz[] = ProductPerformanceTranslation::findOne(['product_performance_id' => $perform->id, 'local' => 'uz-UZ']);
            $perf_en[] = ProductPerformanceTranslation::findOne(['product_performance_id' => $perform->id, 'local' => 'en-EN']);
        }
    }
}

if (!empty($model->activeOptions)) {
    foreach ($model->activeOptions as $checked_opt) {
        $checked_options['id'][$checked_opt->option_id] = $checked_opt->option_id;
        $checked_options['price'][$checked_opt->option_id] = $checked_opt->price;
    }
}
$brands = [];
if (!empty($all_brands)) {
    foreach ($all_brands as $item) {
        $brands[$item->id] = $item->name;
    }
}

function recursiveOptions($all_cats, $index = 0)
{
    $result = '';
    $prefix = '';
    for ($i = 0; $i < $index; $i++) {
        $prefix .= '-';
    }
    foreach ($all_cats as $cats) {
        if (!empty($cats->activeCategories)) {
            $result .= '<optgroup label="' . $prefix . $cats->translate->name . '">';
            $result .= recursiveOptions($cats->activeCategories, $index + 1);
            $result .= '</optgroup>';
        } else {
            $result .= '<option value="' . $cats->id . '">' . $prefix . $cats->translate->name . '</option>';
        }
    }
    return $result;
}

if ($model->isNewRecord) {
    $custom_options = [];
} else {
    $custom_options = ($model->custom_options) ? json_decode($model->custom_options, true) : [];
}

$units = ArrayHelper::merge(['Без ед.измерения'], ArrayHelper::map(Unit::find()->where(['status' => 1])->all(), 'id', 'name'));
$wholesales = is_array($model->wholesale) ? $model->wholesale : json_decode($model->wholesale, true);


$all_statuses = \common\models\ProductStatus::find()->orderBy('`id` ASC')->all();
$stat = [];
if (!empty($all_statuses)) {
    foreach ($all_statuses as $s) {
        $stat[$s->id] = $s->name;
    }
}

$all_type = \common\models\ProductType::find()->orderBy('`id` ASC')->all();
$types = [];
if (!empty($all_type)) {
    foreach ($all_type as $s) {
        $types[$s->id] = $s->name;
    }
}




?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />




<div class="product-form">

    <?php if (!$category) { ?>
        <form action="<?= Url::current() ?>" id="cat-form">
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="category_id">Выберите категорию</label>
                    </div>

                    <?php if (!empty($all_cats)) { ?>
                        <ul class="list-unstyled category-widget-list">
                            <?php foreach ($all_cats as $all_cat) { ?>

                                <li data-id="<?= $all_cat->id ?>"
                                    data-childs="<?= (!empty($all_cat->categories)) ? $all_cat->id : '' ?>"
                                    class="cat-widget-li"><?= $all_cat->translate->name ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                    <input type="text" name="category" id="category_id" value="" style="visibility: hidden;"/>
                    <?php if (!empty($prod_id = Yii::$app->request->get('id'))) { ?>
                        <input type="hidden" name="id" value="<?= $prod_id ?>"/>
                    <?php } ?>
                </div>
            </div>
        </form>
    <?php } else if (!$brand && !empty($all_brands)) { ?>
        <form action="<?= Url::current() ?>" id="brand-form">
            <div class="row">

                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="category_id">Категория</label>
                        <input type="text" readonly="readonly" id="category_id" class="form-control"
                               value="<?= $category->translate->name ?>"/>
                        <a href="<?= Url::current(['category' => '']) ?>"
                           onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                            другую
                            категорию</a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="brand">Выберите марку</label>
                        <select name="brand" id="brand" class="form-control" onchange="$('#brand-form').submit()">
                            <option data-id=""></option>
                            <?php foreach ($all_brands as $item) { ?>
                                <option value="<?= $item->id ?>"><?= $item->name ?></option>
                            <?php } ?>
                        </select>

                    </div>
                    <input type="hidden" name="category" value="<?= $category->id ?>"/>
                    <?php if (!empty($prod_id = Yii::$app->request->get('id'))) { ?>
                        <input type="hidden" name="id" value="<?= $prod_id ?>"/>
                    <?php } ?>
                </div>
            </div>
        </form>
    <?php } else if (!$lineup && !empty($all_lineups)) { ?>
        <form action="<?= Url::current() ?>" id="lineup-form">
            <div class="row">

                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="category_id">Категория</label>
                        <input type="text" readonly="readonly" id="category_id" class="form-control"
                               value="<?= $category->translate->name ?>"/>
                        <a href="<?= Url::current(['category' => '']) ?>"
                           onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                            другую
                            категорию</a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="brand_id">Марка</label>
                        <input type="text" readonly="readonly" id="brand_id" class="form-control"
                               value="<?= $brand->name ?>"/>
                        <a href="<?= Url::current(['brand' => '', 'lineup' => '']) ?>"
                           onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                            другую
                            марку</a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="lineup">Выберите модель</label>
                        <select name="lineup" id="lineup" class="form-control"
                                onchange="$('#lineup-form').submit()">
                            <option data-id=""></option>
                            <?php foreach ($all_lineups as $item) { ?>
                                <option value="<?= $item->id ?>"><?= $item->translate->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <input type="hidden" name="category" value="<?= $category->id ?>"/>
                    <input type="hidden" name="brand" value="<?= $brand->id ?>"/>
                    <?php if (!empty($prod_id = Yii::$app->request->get('id'))) { ?>
                        <input type="hidden" name="id" value="<?= $prod_id ?>"/>
                    <?php } ?>
                </div>
            </div>
        </form>
    <?php } else { ?>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'prod-form']]); ?>
        <div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="form-group">
                    <label for="category_id">Категория</label>
                    <input type="text" readonly="readonly" id="category_id" class="form-control"
                           value="<?= $category->translate->name ?>"/>
                    <a href="<?= Url::current(['category' => '']) ?>"
                       onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                        другую
                        категорию</a>
                    <?= $form->field($model, 'category_id')->hiddenInput()->label(false) ?>
                </div>
            </div>
            <?php if ($brand) { ?>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="brand_id">Марка</label>
                        <input type="text" readonly="readonly" id="brand_id" class="form-control"
                               value="<?= $brand->name ?>"/>
                        <a href="<?= Url::current(['brand' => '', 'lineup' => '']) ?>"
                           onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                            другую
                            марку</a>
                        <?= $form->field($model, 'brand_id')->hiddenInput()->label(false) ?>
                    </div>
                </div>
            <?php } ?>





            <?php if ($lineup) { ?>
                <div class="col-sm-6 col-md-3">
                    <div class="form-group">
                        <label for="lineup_id">Модель</label>
                        <input type="text" readonly="readonly" id="lineup_id" class="form-control"
                               value="<?= $lineup->translate->name ?>"/>
                        <a href="<?= Url::current(['lineup' => '']) ?>"
                           onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать
                            другую
                            модель</a>
                        <?php
                        $str = $lineup->translate->name;
                        $rest = substr($str, 0, 8);
                        ?>
                        <?= $form->field($model, 'label',['template' => '{input}'])->hiddenInput(['value'=> $rest ]) ?>
                        <?=$rest?>
                        <?= $form->field($model, 'lineup_id')->hiddenInput()->label(false) ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($category->on_main == 0 && $category->spec == 0) { ?>
                <div class="col-sm-6 col-md-3">
                    <?= $form->field($model, 'type')->dropDownList($types) ?>
                </div>
            <?php } ?>
            <div class="col-sm-6 col-md-3" style="display: none;">
                <?php if ($model->status == -1) { ?>
                    <div class="form-group field-product-status has-error">
                        <label class="control-label"
                               for="product-status"><?= Yii::t('frontend', 'Status') ?></label>
                        <select id="product-status" class="form-control" style="border" name="status"
                                disabled="disabled">
                            <option value="0"><?= Yii::t('frontend', 'Blocked') ?></option>
                        </select>
                        <div class="help-block"></div>
                    </div>

                    <?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
                <?php } else { ?>
                    <?php if ($model->status == 0) { ?>
                        <div class="form-group has-error">
                    <?php } ?>
                    <?= $form->field($model, 'status')->hiddenInput(['value' => 1])->label(false) ?>
                    <?php if ($model->status == 0) { ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="separator"></div>



        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-4">
                        <h3>Авто инфо</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'vin')->textInput(['placeholder' => Yii::t('frontend', '#'), 'data-a-sign' => "", 'data-a-sep' => " "]) ?>
                    </div>

                    <div class="col-sm-4">
                        <?= $form->field($model, 'auto_number')->textInput(['placeholder' => Yii::t('frontend', '10AA777B'), 'data-a-sign' => "", 'data-a-sep' => " "]) ?>
                    </div>

                    <div class="col-sm-4">
                        <?= $form->field($model, 'status')->dropDownList($stat) ?>

                    </div>

                </div>


                <div class="row">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'auto_number')->hiddenInput()->label(false); ?>
                    </div>
                </div>

                <div class="col-sm-4 col-md-4">
                    <?= $form->field($model, 'date_off')->hiddenInput()->label(false); ?>
                </div>

            </div>

        </div>




        <div class="row">
            <div class="col-md-8">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#ru" aria-controls="ru" role="tab"
                                                              data-toggle="tab"><?= Yii::t('frontend', 'ru') ?></a>
                    </li>
                    <li role="presentation"><a href="#uz" aria-controls="uz" role="tab"
                                               data-toggle="tab"><?= Yii::t('frontend', 'uz') ?></a>
                    </li>
                    <li role="presentation"><a href="#oz" aria-controls="oz" role="tab"
                                               data-toggle="tab"><?= Yii::t('frontend', 'oz') ?></a>
                    </li>
                    <li role="presentation"><a href="#en" aria-controls="en" role="tab"
                                               data-toggle="tab"><?= Yii::t('frontend', 'en') ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="ru">
                        <p></p>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($category->id != 1) { ?>
                                    <?= $form->field($info, 'name[ru]')->textInput(['value' => $info->name, 'placeholder' => Yii::t('frontend', 'Announce title placeholder')]) ?>
                                <?php } ?>
                                <?= $form->field($info, 'description[ru]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info->description, 'placeholder' => Yii::t('frontend', 'Announce description placeholder')]) ?>
                                <?php if ($category->spec == 1) { ?>
                                    <?= $form->field($info, 'warranty[ru]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info->warranty]); ?>
                                    <?= $form->field($info, 'delivery[ru]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info->delivery]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="uz">
                        <p></p>
                        <div class="row">
                            <div class="col-md-12">

                                <?php if ($category->id != 1) { ?>
                                    <?= $form->field($info_uz, 'name[uz]')->textInput(['value' => $info_uz->name, 'placeholder' => Yii::t('frontend', 'Announce title placeholder')]) ?>
                                <?php } ?>
                                <?= $form->field($info_uz, 'description[uz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_uz->description, 'placeholder' => Yii::t('frontend', 'Announce description placeholder')]) ?>
                                <?php if ($category->spec == 1) { ?>
                                    <?= $form->field($info_uz, 'warranty[uz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_uz->warranty]); ?>
                                    <?= $form->field($info_uz, 'delivery[uz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_uz->delivery]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="oz">
                        <p></p>
                        <div class="row">
                            <div class="col-md-12">

                                <?php if ($category->id != 1) { ?>
                                    <?= $form->field($info_oz, 'name[oz]')->textInput(['value' => $info_oz->name, 'placeholder' => Yii::t('frontend', 'Announce title placeholder')]) ?>
                                <?php } ?>
                                <?= $form->field($info_oz, 'description[oz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_oz->description, 'placeholder' => Yii::t('frontend', 'Announce description placeholder')]) ?>
                                <?php if ($category->spec == 1) { ?>
                                    <?= $form->field($info_oz, 'warranty[oz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_oz->warranty]); ?>
                                    <?= $form->field($info_oz, 'delivery[oz]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_oz->delivery]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="en">
                        <p></p>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($category->id != 1) { ?>
                                    <?= $form->field($info_en, 'name[en]')->textInput(['value' => $info_en->name, 'placeholder' => Yii::t('frontend', 'Announce title placeholder')]) ?>
                                <?php } ?>
                                <?= $form->field($info_en, 'description[en]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_en->description, 'placeholder' => Yii::t('frontend', 'Announce description placeholder')]) ?>

                                <?php if ($category->spec == 1) { ?>
                                    <?= $form->field($info_en, 'warranty[en]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_en->warranty]); ?>
                                    <?= $form->field($info_en, 'delivery[en]')->textarea(['style' => 'resize: none;', 'rows' => 6, 'value' => $info_en->delivery]); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <div class="row" id="price_block">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="row">
                            <?php if ($model->isNewRecord) {
                                $price_options = ['value' => 0, 'type' => 'number', 'min' => 0, 'step' => 'any'];
                                $checked_def_price = '';
                            } else {
                                if ($model->price == 0 && empty($wholesales)) {
                                    $price_options = ['type' => 'number', 'min' => 0, 'step' => 'any'];
                                    $checked_def_price = 'checked="checked"';
                                } else {
                                    $price_options = ['type' => 'number', 'min' => 0, 'step' => 'any'];
                                    $checked_def_price = '';
                                }
                            } ?>
                            <div class="col-sm-6 col-md-4">
                                <?= $form->field($model, 'price')->textInput(['data-a-sign' => "", 'data-a-sep' => " ", 'value' => $model->price ? $model->price : 0]) ?>
                            </div>

                            <div class="col-sm-6 col-md-4"><?= $form->field($model, 'currency')->dropDownList(['uzs' => Yii::t('frontend', 'uzs'), 'usd' => Yii::t('frontend', 'usd')]); ?></div>

                            <div class="col-sm-6 col-md-4" style="margin-top: 28px;">
                                <?= $form->field($model, 'from')->hiddenInput()->label(false) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-sm-6 col-md-4" style="display:none;">
                                <?= $form->field($model, 'price_type')->dropDownList([
                                    Yii::t('frontend', 'Price Type 0'),
                                    Yii::t('frontend', 'Price Type 1'),
                                    Yii::t('frontend', 'Price Type 2'),
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" style="display: none;" id="wholesale_block">
                        <div class="row">
                            <div class="col-md-12" id="wholesale_parent_block">
                                <?php if (!$model->isNewRecord && !empty($wholesales)) { ?>
                                    <?php foreach ($wholesales as $count => $price) { ?>
                                        <div class="row">
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group field-product-price required has-success">
                                                    <label class="control-label"
                                                           for="product-wholesale"><?= Yii::t('frontend', 'Wholesale Price') ?></label>
                                                    <input type="number" min="0" step="any" id="product-wholesale"
                                                           class="form-control" name="Product[wholesale][price][]"
                                                           value="<?= $price ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group field-product-price required has-success">
                                                    <label class="control-label"
                                                           for="product-wholesale_count"><?= Yii::t('frontend', 'Wholesale From') ?></label>
                                                    <input type="number" min="0" id="product-wholesale_count"
                                                           class="form-control" name="Product[wholesale][count][]"
                                                           value="<?= $count ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-md-2">
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <button class="btn btn-danger" id="delete_wholesale"
                                                            onclick="if($(this).parent().parent().parent().parent().find('.row').length > 1){$(this).parent().parent().parent().remove();}return false;"
                                                            style="display: block">
                                                        <i class="fa fa-minus-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row">
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group field-product-price required has-success">
                                                <label class="control-label"
                                                       for="product-wholesale"><?= Yii::t('frontend', 'Wholesale Price') ?></label>
                                                <input type="number" min="0" step="any" id="product-wholesale"
                                                       class="form-control" name="Product[wholesale][price][]">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group field-product-price required has-success">
                                                <label class="control-label"
                                                       for="product-wholesale_count"><?= Yii::t('frontend', 'Wholesale From') ?></label>
                                                <input type="number" min="0" id="product-wholesale_count"
                                                       class="form-control" name="Product[wholesale][count][]">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-md-2">
                                            <div class="form-group">
                                                <label for="">&nbsp;</label>
                                                <button class="btn btn-danger" id="delete_wholesale"
                                                        onclick="if($(this).parent().parent().parent().parent().find('.row').length > 1){$(this).parent().parent().parent().remove();}return false;"
                                                        style="display: block">
                                                    <i class="fa fa-minus-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6 col-md-4"></div>
                                    <div class="col-sm-6 col-md-4">
                                        <button class="btn btn-primary" id="add_wholesale"><i
                                                    class="fa fa-plus-circle"></i> <?= Yii::t('frontend', 'Wholesale Add') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>









        </div>
        <div class="separator"></div>



        <div class="row">

            <div class="col-md-4">
                <div class="row">
                    <div class="col-sm-4">
                        <h3>Условия</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'min_day')->textInput([ 'type' => 'number', 'placeholder' => Yii::t('frontend', '0'), 'data-a-sign' => " ", 'data-a-sep' => " "]) ?>
                        <?= $form->field($model, 'min_age')->textInput([ 'type' => 'number', 'placeholder' => Yii::t('frontend', '0'),  'data-a-sign' => " ", 'data-a-sep' => " "]) ?>
                        <?= $form->field($model, 'min_experience')->textInput([ 'type' => 'number', 'placeholder' => Yii::t('frontend', '0'), 'data-a-sign' => " ", 'data-a-sep' => " "]) ?>
                    </div>

                </div>
            </div>





            <div class="col-md-4">
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Условия аренды</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <?=$form->field($model, 'unlimited_mileage')->checkbox([ 'value' => '1', 'id'=>'check_unlim', 'check ' => false])->label('');?>
                        <?= $form->field($model, 'included_kilometers')->textInput([ 'type' => 'number', 'placeholder' => Yii::t('frontend', '0'), 'class'=>'disable_input  form-control', 'data-a-sign' => " ", 'data-a-sep' => " "]) ?>
                        <?= $form->field($model, 'excess_mileage')->textInput(['placeholder' => Yii::t('frontend', '0'),  'class'=>'disable_input form-control', 'data-a-sign' => "", 'data-a-sep' => " "]) ?>
                        <?= $form->field($model, 'excess_time')->textInput([ 'type' => 'number', 'placeholder' => Yii::t('frontend', '0'),  'data-a-sign' => " ", 'data-a-sep' => " "]) ?>
                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Планы защиты</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <?=$form->field($model, 'is_deposit')->checkbox([ 'value' => '0', 'id'=>'klient_open_4_end', 'uncheck ' => true])->label('');?>
                        <?= $form->field($model, 'deposit')->textInput(['placeholder' => Yii::t('frontend', 'Deposit'), 'id'=>'nightlife_open_7_start', 'data-a-sign' => "", 'data-a-sep' => " "]) ?>
                    </div>

                </div>
            </div>



        </div>
        <div class="separator"></div>

<!--        <div class="row">-->
<!--            <div class="col-sm-12">-->
<!---->
<!--                --><?php
//                echo $form->field($model, 'date_range', [
//                'addon'=>['prepend'=>['content'=>'<i class="fas fa-calendar-alt"></i>']],
//                'options'=>['class'=>'drp-container form-group']
//                ])->widget(DateRangePicker::classname(), [
//                    'useWithAddon'=>true
//                ]);
//
//                ?>
<!---->
<!--                <input type="date" name="datetimes" class="date_renge" />-->
<!--            </div>-->
<!--        </div>-->
                <script>
                    var startDate;
                    var endDate;


                    $(function() {
                        $('input[name="datetimes"]').daterangepicker({
                            timePicker: true,
                            startDate: moment().startOf('hour'),
                            timePickerIncrement: 30,
                            endDate: moment().startOf('hour').add(32, 'hour'),
                            timePicker24Hour: true,
                            showDropdowns: true,
                            locale: {
                                format: 'DD/MM/YYYY H:mm'
                            }
                        },
                        function(start, end) {
                            console.log("Callback has been called!");
                            $('#reportrange span').html(start.format('DD/MM/YYYY H:mm') + ' - ' + end.format('DD/MM/YYYY H:mm'));
                            startDate = start._d;
                            endDate = end._d;
                            console.log(start)
                            console.log(start._d)
                            console.log(start._i)
                        });
                    });

                </script>


        <div class="row">



        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <div class="checkbox" style="margin-top: 0;margin-bottom: 20px;">
                            <label for="def_price">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="separator"></div>
        <div class="row">
            <?php if (!empty($options) || !empty($options_multi)) { ?>
                <?php if (!empty($options)) { ?>
                    <div class=" options_label">
                        <?php $loop = 1;
                        foreach ($options as $group) {
                            if ($group->main == 0) continue;
                            ?>
                            <div class="col-sm-4 ">
                                <div class="form-group">
                                    <label for="control-label"
                                           class="options_<?= $loop ?>"><?= $group->translate->name ?></label>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select name="options[<?= $group->id ?>][id]" id="options_<?= $loop ?>"
                                                    class="form-control dropDownSelect">
                                                <option value=""></option>
                                                <?php foreach ($group->activeOptions as $option) { ?>
                                                    <option value="<?= $option->id ?>"
                                                            <?php if (in_array($option->id, $checked_options['id'])) { ?>selected<?php } ?>><?= $option->translate->name ?></option>
                                                <?php } ?>
                                                <option value="-1" <?= (in_array($group->id, array_keys($custom_options))) ? 'selected' : '' ?>><?= Yii::t('frontend', 'Other') ?></option>
                                            </select>
                                            <input type="text"
                                                   value="<?= (isset($custom_options[$group->id]) ? $custom_options[$group->id] : '') ?>"
                                                   name="custom_options[<?= $group->id ?>]"
                                                   class="form-control option_input_field" <?= (!in_array($group->id, array_keys($custom_options))) ? 'style="display: none"' : '' ?>/>
                                        </div>
                                        <input type="hidden" name="options[][price]" value="0">
                                    </div>
                                </div>
                            </div>
                            <?php $loop++;
                        } ?>
                    </div>


                <?php } ?>

                <?php if (!empty($options_multi)) { ?>
                    <div class=" options_label">

                        <?php $loop = 1;
                        foreach ($options_multi as $group) {
                            if ($group->main == 0) continue;
                            ?>
                            <?php foreach ($group->activeOptions as $option) { ?>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="options[][id]"
                                                   value="<?= $option->id ?> " <?php if (in_array($option->id, $checked_options['id'])) { ?> checked <?php } ?>><?= $option->translate->name ?>
                                        </label>
                                    </div>
                                </div>
                                <input type="hidden" name="options[][price]" value="0">
                            <?php } ?>

                            <?php $loop++;
                        } ?>
                    </div>
                <?php } ?>

            <?php } ?>
            <?php if ($category->on_main == 0 && $category->spec == 0) { ?>
                <div class="col-sm-6 col-md-4">
                    <?= $form->field($model, 'km')->textInput(['placeholder' => Yii::t('frontend', 'Announce km placeholder'), 'data-a-sign' => "", 'data-a-sep' => " ", 'value' => $model->km ? $model->km : 0]) ?>
                </div>
            <?php } ?>
            <?= $form->field($model, 'articul')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'warranty')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'year')->hiddenInput()->label(false); ?>
        </div>
        <div class="separator"></div>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <label class="control-label"><?= Yii::t('frontend', 'Main Image') ?></label>
                <?php $fileinput_options = ['accept' => 'image/*', 'multiple' => false];
                if ($model->mainImage->image != '') {
                    $fileinput_options = ['accept' => 'image/*', 'multiple' => false]
                    ?>
                    <img src="<?= $model->mainImage->image ?>" style="background: #fff;
    border-radius: 5px;
    border: 1px solid #ddd;
    margin-bottom: 3px;
    width: 100%;
    padding: 0 8px;">
                <?php } ?>
                <div class="form-group">
                    <?php
                    echo FileInput::widget([
                        'name' => 'mainImage',
                        'options' => $fileinput_options,
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                            'browseLabel' => Yii::t('frontend', 'Choose Image'),
                        ],
                        'language' => 'ru'
                    ]);
                    ?>
                </div>
                <p style="font-size: 11px;color: #6b6b6b;margin-top: -10px;">
                    <?php if ($category->on_main == 1) { ?>
                        <?= Yii::t('frontend', 'Service image size') ?>
                    <?php } else { ?>
                        <?= Yii::t('frontend', 'Product image size') ?>
                    <?php } ?>
                </p>
            </div>
            <div class="col-sm-6  col-md-8">
                <label class="control-label"><?= Yii::t('frontend', 'Other Images') ?></label>
                <?php if (!empty($model->productImages)) { ?>
                    <div class="row">
                        <?php foreach ($model->productImages as $images) {
                            if ($images->main) continue;
                            ?>
                            <div class="col-sm-6 col-md-3">
                                <img src="<?= $images->image ?>" alt="." class="img-responsive">
                                <a href="#" class="delete_image text-danger"
                                   data-productid="<?= $images->product_id ?>"
                                   data-imageid="<?= $images->id ?>"><?= FA::i('remove') ?> <?= Yii::t('frontend', 'Delete image') ?></a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <?php
                    echo FileInput::widget([
                        'name' => 'images[]',
                        'options' => ['accept' => 'image/*', 'multiple' => true],
                        'pluginOptions' => [
                            'uploadUrl' => '/uploads/',

                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                            'browseLabel' => Yii::t('frontend', 'Choose few images'),
                            'fileActionSettings' => [
                                'showUpload' => false,
                            ]
                        ],
                        'language' => 'ru'
                    ]);
                    ?>
                </div>
                <p style="font-size: 11px;color: #6b6b6b;margin-top: -10px;">
                    <?= Yii::t('frontend', 'Announce photo placeholder') ?>
                </p>
            </div>
        </div>
        <p style="margin-bottom: 15px;">&nbsp;</p>

        <div class="panel-group options_panel_group" id="accordion" role="tablist" aria-multiselectable="true">
            <?php if (!empty($options) || !empty($options_multi)) { ?>

                <div class="alert alert-info" role="alert">
                    <?= Yii::t('frontend', 'Announce options placeholder') ?>
                    <p class="text-center">
                        <i class="fa fa-angle-double-down fa-2x options_double-angele"></i>
                    </p>
                </div>
                <?php if (!empty($options)) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                   href="#collapse_options" aria-expanded="true" aria-controls="collapse_options">
                                        <span class="pull-right"><i class="fa fa-chevron-down"
                                                                    style="font-size: 10px;"></i></span>
                                    <?= Yii::t('frontend', 'Options') ?>
                                    <span class="clearfix"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_options" class="panel-collapse collapse in" role="tabpanel"
                             aria-labelledby="headingTwo">
                            <div class="panel-body">
                                <div class="row options_label">
                                    <?php $loop = 1;
                                    foreach ($options as $group) {
                                        if ($group->main == 1) continue;
                                        ?>
                                        <div class="col-sm-4 ">
                                            <div class="form-group">
                                                <label for="control-label"
                                                       class="options_<?= $loop ?>"><?= $group->translate->name ?></label>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <select name="options[<?= $group->id ?>][id]"
                                                                id="options_<?= $loop ?>"
                                                                class="form-control dropDownSelect">
                                                            <option value=""></option>
                                                            <?php foreach ($group->activeOptions as $option) { ?>
                                                                <option value="<?= $option->id ?>"
                                                                        <?php if (in_array($option->id, $checked_options['id'])) { ?>selected<?php } ?>><?= $option->translate->name ?></option>
                                                            <?php } ?>
                                                            <option value="-1" <?= (in_array($group->id, array_keys($custom_options))) ? 'selected' : '' ?>><?= Yii::t('frontend', 'Other') ?></option>
                                                        </select>
                                                        <input type="text"
                                                               value="<?= (isset($custom_options[$group->id]) ? $custom_options[$group->id] : '') ?>"
                                                               name="custom_options[<?= $group->id ?>]"
                                                               class="form-control option_input_field" <?= (!in_array($group->id, array_keys($custom_options))) ? 'style="display: none"' : '' ?>/>
                                                    </div>
                                                    <input type="hidden" name="options[][price]" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <?php $loop++;
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>


                <?php } ?>

                <?php if (!empty($options_multi)) { ?>

                    <?php $loop = 1;
                    foreach ($options_multi as $group) {

                        if ($group->main == 1) continue;
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse"
                                       data-parent="#accordion" href="#collapse<?= $group->id ?>"
                                       aria-expanded="false" aria-controls="collapse<?= $group->id ?>">
                                            <span class="pull-right"><i class="fa fa-chevron-down"
                                                                        style="font-size: 10px;"></i></span>
                                        <?= $group->translate->name ?>
                                        <span class="clearfix"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?= $group->id ?>" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row">
                                        <?php foreach ($group->activeOptions as $option) { ?>
                                            <div class="col-sm-4">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="options[][id]"
                                                               value="<?= $option->id ?> " <?php if (in_array($option->id, $checked_options['id'])) { ?> checked <?php } ?>><?= $option->translate->name ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="options[][price]" value="0">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $loop++;
                    } ?>
                <?php } ?>

            <?php } ?>
        </div>
        <p></p>
        <div class="separator"></div>
        <div class="form-group">
            <a href="<?= Url::to(['product/index', 'sort' => '-id']) ?>"
               class="btn btn-secondary btn-cancel">Назад</a>
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php } ?>
</div>

<?php $this->registerJs('
    $(document).on(\'ready pjax:success pjax:error\', function() {
    
    if($(\'#product-km\').length) {
        new AutoNumeric(\'#product-km\', AutoNumeric.getPredefinedOptions().integerPos );
    }
    if($(\'#product-price\').length) {
        new AutoNumeric(\'#product-price\', AutoNumeric.getPredefinedOptions().integerPos );
    }
        $(\'.dropDownSelect\').on(\'change\', function() {
            if($(this).val() == -1) {
                $(this).parent().find(\'.option_input_field\').show(function() {
                    $(this).focus();
                });
            } else {
                $(this).parent().find(\'.option_input_field\').hide();
            }
        });
        
        $(\'select.form-control\').select2(
            {
                language: {
                  noResults: function () {
                    return "Ничего не найдено";
                  }
                }
            }
        );
        $(document).on(\'click\', \'.cat-widget-li\', function () {
            id = $(this).data(\'id\');
            has_childs = $(this).data(\'childs\');
        
            li = $(this);
            if(has_childs != \'\') {
                $.ajax({
                    url: "/site/get-cats",
                    data: {\'id\': li.data(\'id\')}, //data: {}
                    type: "get",
                    success: function (t) {
                        $(\'.category-widget-list\').html(t);
                    }
                });
            }
            else {
                $(\'#category_id\').val(id);
                $(\'#cat-form\').submit();
            }
        });
        $(\'#add_cat_btn\').on(\'click\', function() {
            $($(\'#add-cat-temp\').html()).appendTo($(\'#added_cats\'));
            $(this).hide();
            return false;
        });
        $(document).on(\'click\', \'.add_cat_delete\', function () {
            $(this).parent().remove();
            return false;
        });
            var title = [];
        $(document).on(\'click\', \'.add-cat-widget-li\', function () {
            id = $(this).data(\'id\');
            has_childs = $(this).data(\'childs\');
            li = $(this);
            if(has_childs != \'\') {
                $.ajax({
                    url: "/site/get-cats",
                    data: {\'id\': li.data(\'id\'), \'add\': \'add-\'}, //data: {}
                    type: "get",
                    success: function (t) {
                        if(li.text() != \' Назад\') {
                            title.push(li.text() + \' > \');
                        }
                        else {
                            title.splice(title.length - 1, 1);
                        }
                        $(\'#added_cats .add-category-widget-list\').html(t);
                    }
                });
            }
            else {
                title.push(li.text() + \' > \');
                title = title.join(\'\');
                btn = \' <i class="fa fa-remove add_cat_delete btn btn-danger"></i>\';
                input = \' <input type="hidden" name="add_cats[]" value="\' + id + \'"/>\';
                input_title = \' <input type="hidden" name="add_cats_titles[\' + id + \']" value="\' + title.substr(0, title.length - 3) + \'"/>\';
                $(\'#adding_cats\').html($(\'#adding_cats\').html() + \'<p>\' + title.substr(0, title.length - 3) + btn + input + input_title + \'</p>\');
                title = [];
                $(\'#added_cats .add-category-widget-list\').remove();
                $(\'#add_cat_btn\').show();
            }
            return false;
        });
    });
    
    
');
?>
<script type="text/javascript">
    $(function(){
        $('#klient_open_4_end').on('click', function(){
            if($(this).is(':checked')){
                $('#nightlife_open_7_start').attr('disabled', true);
            } else {
                $('#nightlife_open_7_start').attr('disabled', false);
            }
        });


    });

    $(function(){
        $('#check_unlim').on('click', function(){
            if($(this).is(':checked')){
                $('.disable_input').attr('disabled', true);
            } else {
                $('.disable_input').attr('disabled', false);
            }
        });


    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs/dist/tf.min.js"> </script>