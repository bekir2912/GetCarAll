<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 24.09.2017
 * Time: 3:35
 */
use yii\helpers\Url;
use yii\web\View;

$checked_filters = !empty(Yii::$app->request->get('filters')) ? Yii::$app->request->get('filters') : array();
$checked_shops = !empty(Yii::$app->request->get('shops')) ? Yii::$app->request->get('shops') : array();
$checked_brands = !empty(Yii::$app->request->get('brands')) ? Yii::$app->request->get('brands') : array();
$checked_lineups = !empty(Yii::$app->request->get('lineups')) ? Yii::$app->request->get('lineups') : array();
$all_params_opened = !empty(Yii::$app->request->get('all-params')) ? Yii::$app->request->get('all-params') : false;
$km_start = !empty(Yii::$app->request->get('kmstart')) ? Yii::$app->request->get('kmstart') : '';
$km_end = !empty(Yii::$app->request->get('kmend')) ? Yii::$app->request->get('kmend') : '';

$cities = \common\models\City::find()->where(['status' => 1])->orderBy('`order` ASC')->all();

$selected_city = (Yii::$app->request->get('city_id') > 0)? Yii::$app->request->get('city_id'): 0;

$city_id = Yii::$app->session->get('city_id');

if ($city_id) {
    $city = \common\models\City::find()->where(['status' => 1, 'id' => $city_id])->orderBy('`order` ASC')->one();
}
?>
<?php if (!empty($brands)
    || !empty($options)
    || $def_kmStart != 0
    || $def_kmEnd != 0
    || $def_pStart != 0 || $def_pEnd != 0) { ?>
    <div class="filterTop ">
        <form class="filter-form " action="<?= Url::to(['category/index', 'id' => $category->url]) ?>" id="filter_form" data-pjax="true">
            <input type="hidden" name="s" value="<?=Yii::$app->request->get('s')?>">
            <input type="hidden" name="sd" value="<?=Yii::$app->request->get('sd')?>">
        <div class="filterTopSelects">
            <div class="form-group filterTopSelectsBox">
                    <select class="form-control filter_button" name="city_id">
                        <!--                                filter__select -->
                        <option value="" disabled selected><?=($city)? $city->translate->name: Yii::t('frontend', 'All cities')?></option>
                        <option value="all" ><?=Yii::t('frontend', 'All cities');?></option>
                        <?php foreach ($cities as $city) { ?>
                            <li><a href="<?=Url::current(['city_id' => $city->id])?>"><?=$city->translate->name?></a></li>
                            <option value="<?= $city->id ?>"><?=$city->translate->name?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php if (!empty($options)) {
                $show_all_params = false;
                $chevron = 'down';
                ?>
                <?php foreach ($options['group'] as $group_id => $group) { ?>
                    <?php if ($group->category_id != $category->id) {
                        continue;
                    } ?>
                    <?php if (empty($options['values'][$group_id])) {
                        continue;
                    } if (count($options['values'][$group_id]) <= 1) {
                        continue;
                    }
                    $main_class = 'opened';
                    if($group->main == 0) {
                        $main_class = 'not-main closed';
                        if ($all_params_opened == 'true') {
                            $chevron = 'up';
                            $main_class = 'not-main opened';
                        }
                        $show_all_params = true;
                        continue;
                    }
                    ?>
                    <?php if ($group->type == 1) { ?>
                        <div class="form-group filterTopSelectsBox">
                            <div>
                                <strong><?= $group->translate->name ?></strong>
                            </div>
                            <?php foreach ($options['values'][$group_id] as $value) { ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="filter_button" name="filters[]" value="<?= $group_id.'_'.$value->id ?>" <?= (in_array($group_id.'_'.$value->id, $checked_filters) ? 'checked' : '') ?>> <?=$value->translate->name?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="form-group filterTopSelectsBox">
                            <select class="form-control filter_button" name="filters[]">
<!--                                filter__select -->
                                <option value="" disabled selected><?= $group->translate->name ?></option>
                                <option value="" ></option>
                                <?php foreach ($options['values'][$group_id] as $value) { ?>
                                    <option value="<?= $group_id.'_'.$value->id ?>"
                                        <?= (in_array($group_id.'_'.$value->id, $checked_filters) ? 'selected' : '') ?>
                                    ><?=$value->translate->name?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                <?php } ?>
                <!-- Modal-->
                <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel"><?=Yii::t('frontend', 'All Params')?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body filterbody">
                                <?php if($def_kmStart != 0 || $def_kmEnd != 0) { ?>
                                    <div class="row">
                                    <div class="col">
                                        <div class="form-group filterTopSelectsBox">
                                            <input value="<?=$km_start?>" class="filter_button form-control" type="number"  name="kmstart" placeholder="<?=Yii::t('frontend', 'mileage')?>, <?=mb_strtolower(Yii::t('frontend', 'From'))?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group filterTopSelectsBox">
                                            <input value="<?=$km_end?>" class="filter_button form-control" type="number"  name="kmend" placeholder="<?=Yii::t('frontend', 'mileage')?>, <?=mb_strtolower(Yii::t('frontend', 'To'))?>">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group filterTopSelectsBox">
                                            <div class="" style="padding: 5px">
                                                <label style="font-size: 14px;text-transform: unset;font-weight: normal;margin-bottom: 0;">
                                                    <input type="checkbox" name="photo" value="1" class=" filter_button" <?=($photo == 1)? 'checked': '' ?>> <?=Yii::t('frontend', 'With photo')?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                <?php } ?>

                                <?php if($def_pStart != 0 || $def_pEnd != 0) { ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="filter-price">
                                                <p><strong><?=Yii::t('frontend', 'Price')?></strong></p>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-12" style="padding: 0 45px;">
                                                            <input id="price_range" class="irs-hidden-input" type="hidden" tabindex="-1" name="price_range" readonly="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                                <div class="row">
                                <?php foreach ($options['group'] as $group_id => $group) { ?>
                                    <?php if ($group->category_id != $category->id) {
                                        continue;
                                    } ?>
                                    <?php if (empty($options['values'][$group_id])) {
                                        continue;
                                    } if (count($options['values'][$group_id]) <= 1) {
                                        continue;
                                    }
                                    $main_class = 'opened';
                                    if($group->main == 1) {
                                        continue;
                                    }
                                    ?>
                                    <?php if ($group->type == 1) { ?>
                                        <div class="col-12">
                                            <div class="form-group filterTopSelectsBox">
                                                <div>
                                                    <strong><?= $group->translate->name ?></strong>
                                                </div>
                                                <?php foreach ($options['values'][$group_id] as $value) { ?>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="" name="filters[]" value="<?= $group_id.'_'.$value->id ?>" <?= (in_array($group_id.'_'.$value->id, $checked_filters) ? 'checked' : '') ?>> <?=$value->translate->name?>
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-6">
                                            <div class="form-group filterTopSelectsBox">
                                                <select class="form-control " name="filters[]">
                                                    <!--                                filter__select -->
                                                    <option value="" disabled selected><?= $group->translate->name ?></option>
                                                    <option value="" ></option>
                                                    <?php foreach ($options['values'][$group_id] as $value) { ?>
                                                        <option value="<?= $group_id.'_'.$value->id ?>"
                                                            <?= (in_array($group_id.'_'.$value->id, $checked_filters) ? 'selected' : '') ?>
                                                        ><?=$value->translate->name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="submitFilter" class="btn btn-danger"><?=Yii::t('frontend', 'Show')?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="">

                <a class="filter-reset mx-3" href="<?= Url::to(['category/index', 'id' => $category->url, 's' => Yii::$app->request->get('s'), 'sd' => Yii::$app->request->get('sd')]) ?>">
                    <?=Yii::t('frontend', 'Clear')?> <i class="fa fa-close"></i>
                </a>
                <a type="button"  data-toggle="modal" data-target="#filterModal"><?=Yii::t('frontend', 'All Params')?></a>
            </div>
        </div>

    </form>
    </div>
<?php } ?>
<?php $this->registerJs('
    $(function () {
        $(\'[data-toggle="tooltip"]\').tooltip();
        
    $(\'.filter_button, #city_id\').on(\'change\', function (e) {
        $(\'#filterModal\').modal(\'toggle\');
        $(\'body\').removeClass(\'modal-open\');
        $(\'.modal-backdrop\').remove();
        $(\'#filter_form\').submit();
    });
    $(\'#submitFilter\').on(\'click\', function (e) {
        $(\'#filterModal\').modal(\'toggle\');
        $(\'body\').removeClass(\'modal-open\');
        $(\'.modal-backdrop\').remove();
    });
    
    $(\'#options-btn\').on(\'click\', function(){
        if($(\'#options-hidden\').hasClass(\'hidden-xs\')){
            $(\'#options-hidden\').show();
            $(\'#options-hidden\').removeClass(\'hidden-xs\');
        } else {
            $(\'#options-hidden\').hide();
            $(\'#options-hidden\').addClass(\'hidden-xs\');
        }
    });
    
    $(\'#brands_lineups_btn\').on(\'click\', function(){
        $(this).parent().hide();
        $(\'.brands_lineups_item\').show();
        return false;
    });
    
    $(\'#filter-all\').on(\'click\', function (e) {
        var chevron = \'fa fa-chevron-down\';
        $(\'.not-main\').each(function() {
            if($(this).hasClass(\'closed\')){
                chevron = \'fa fa-chevron-up\';
                $(this).addClass(\'opened\');
                $(this).removeClass(\'closed\');
                $(\'#all-params\').val(\'true\');
            } else {
                $(this).addClass(\'closed\');
                $(this).removeClass(\'opened\');
                $(\'#all-params\').val(\'false\');
            }
        });
        $(\'.all-params-icon i\').removeAttr(\'class\');
        $(\'.all-params-icon i\').addClass(\'fa\');
        $(\'.all-params-icon i\').addClass(chevron);
    });
    
    $(".filter__button").click(function() {
    $([document.documentElement, document.body]).animate({
        scrollTop: $("#search-result").offset().top
    }, 500);
});

    })
'); ?>

<?php if($def_pStart != 0 || $def_pEnd != 0) { ?>
    <?php $this->registerCss('
        .closed {
            display: none;
        }
        .filter-all {
            color: #d91b30;
            margin-right: 10px;
        }
    ');?>
    <?php $this->registerCssFile('/js/ion.rangeSlider-2.2.0/css/ion.rangeSlider.css');?>
    <?php $this->registerCssFile('/js/ion.rangeSlider-2.2.0/css/ion.rangeSlider.skinFlat.css');?>
    <?php $this->registerJsFile('/js/ion.rangeSlider-2.2.0/js/ion-rangeSlider/ion.rangeSlider.min.js', ['depends' => ['yii\web\YiiAsset']]);?>
    <?php $this->registerJs('
        $(function () {
            $("#price_range").ionRangeSlider({
                type: "double",
                grid: true,
                step: 1000,
                min: '.$def_pStart.',
                max: '.$def_pEnd.',
                from: '.(Yii::$app->request->get('price_range')? $pStart: $def_pStart).',
                to: '.(Yii::$app->request->get('price_range')? $pEnd: $def_pEnd).',
                onFinish: function (data) {
                    $(\'#filterModal\').modal(\'toggle\');
                    $(\'body\').removeClass(\'modal-open\');
                    $(\'.modal-backdrop\').remove();
                    $(\'#filter_form\').submit();
                }
    //            ,prefix: "$"
            });
        })
    '); ?>
<?php } ?>