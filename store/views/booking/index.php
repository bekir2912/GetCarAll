<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;




/* @var $this yii\web\View */
/* @var $searchModel app\models\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Бронирования');


//$cats = \common\models\Booking::find()->select('pickup_id ')->where(['shop_id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0])->orderBy('pickup_id')->groupBy('pickup_id')->all();
//$cat_filter = [];
//if(!empty($cats)) {
//    foreach ($cats as $cat) {
//        $row_cat = [];
//        $row_cat[$cat->category->id] = $cat->category->translate->name;
//        $parent = $cat->category->parent;
//        while ($parent) {
//            $temp = $row_cat;
//            if(isset($row_cat)) unset($row_cat);
//            $row_cat[$parent->translate->name] = $temp;
//            $parent = $parent->parent;
//        }
//        $cat_filter = ArrayHelper::merge($cat_filter, $row_cat);
//    }
//}





$statuses = \common\models\Booking::find()->select('status')->orderBy('status')->groupBy('status')->all();

$brand_filter = [];
if(!empty($brands)) {
    foreach ($brands as $brand) {
        $brand_filter[$brand->brand->id] = $brand->brand->name;
    }
}

//$cats = \common\models\Booking::find()->select('product_id')->where(['shop_id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0])->orderBy('category_id')->groupBy('category_id')->all();
//$cat_filter = [];
//if(!empty($cats)) {
//    foreach ($cats as $cat) {
//        $row_cat = [];
//        $row_cat[$cat->category->id] = $cat->category->translate->name;
//        $parent = $cat->category->parent;
//        while ($parent) {
//            $temp = $row_cat;
//            if(isset($row_cat)) unset($row_cat);
//            $row_cat[$parent->translate->name] = $temp;
//            $parent = $parent->parent;
//        }
//        $cat_filter = ArrayHelper::merge($cat_filter, $row_cat);
//    }
//}


?>


<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="product-index">

                    <div class="page-header">
                        <div>
                            <?= Html::encode($this->title) ?>
                            <small class="pull-right">
                                <?= Html::a('Добавить ' . FA::i('plus'), ['create'], ['class' => 'btn btn-success']) ?>
                            </small>
                            <small class="pull-right">
                                <?= Html::a('Сброс фильтров', ['booking/index'],['class' => 'btn btn-primary'], ['data-confirm' => 'Сбросить все фильтры']); ?>

                            </small>
                        </div>
                        <div>

                            <div class="row">
                                <div class="col-md-12">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation"><a href="#ru" aria-controls="ru" role="tab" data-toggle="tab">Таблица</a></li>
                                        <li role="presentation"  class="active"><a href="#uz" aria-controls="uz" role="tab" data-toggle="tab">Календарь</a></li>
                                    </ul>

                                </div>
                            </div>

                        </div>
                    </div>
                    <p></p>


                    <div class="tab-content">


                        <!--                Таблица        -->

                        <div role="tabpanel" class="tab-pane" id="ru">
                            <p></p>
                            <div class="row">
                                <div class="col-sm-12">

                                    <?php Pjax::begin(); ?>
                                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                                    <?= GridView::widget([
                                        'dataProvider' => $dataProvider,
                                        'filterModel' => $searchModel,
                                        'columns' => [
                                            ['class' => 'yii\grid\SerialColumn'],

                                            [
                                                'attribute' => 'id',
                                                'options' => ['style' => 'width: 65px; max-width: 65px;'],
                                                'contentOptions' => ['style' => 'width: 65px; max-width: 65px;'],
                                            ],
//                                        pickup_shop_id

                                            [
                                                'attribute' => 'pickup_shop_id',
                                                'value' => function($data) {
                                                    return $data->pickupLocation->address;
                                                },
                                                'options' => ['width' => '200px'],
                                                'filter' => Html::activeDropDownList(
                                                    $searchModel,
                                                    'pickup_shop_id',
                                                    ArrayHelper::map(\common\models\Location::find()->where(['category'=> 2])->all(), 'id', 'address'),
                                                    ['class' => 'form-control', 'prompt'=>'']

                                                ),
                                            ],

                                            [
                                                'attribute' => 'return_shop_id',
                                                'value' => function($data) {
                                                    return $data->returnLocation->address;
                                                },
                                                'options' => ['width' => '200px'],
                                                'filter' => Html::activeDropDownList(
                                                    $searchModel,
                                                    'return_shop_id',
                                                    ArrayHelper::map(\common\models\Location::find()->where(['category'=> 1])->all(), 'id', 'address'),
                                                    ['class' => 'form-control', 'prompt'=>'']
                                                ),
                                            ],
//                                            'return_shop_id',
                                            [
                                                'attribute' => 'product_id',
                                                'value' => function($data) {
                                                    return $data->product->lineup->translate->name;
                                                },
                                                'options' => ['width' => '200px'],
                                                'filter' => $status_filter,
                                            ],
//                                            'product_id',
                                            'start_date',
                                            'end_date',
//                                            'pickup_location',
//                                            'return_location',


                                            [
                                                'attribute' => 'status',
                                                'value' => function ($data) {
                                                    return \common\models\BookingStatus::findOne(['id'=>$data->status])->name;
                                                },
                                                'filter' => Html::activeDropDownList(
                                                      $searchModel,
                                                      'status',
                                                      ArrayHelper::map(\common\models\BookingStatus::find()->all(), 'id', 'name'),
                                                    ['class' => 'form-control', 'prompt'=>'']
                                                 ),

                                            ],

                                            [
                                                'class' => 'yii\grid\ActionColumn',
                                                'template' => '{delete} {update}',
                                                'buttons' => [
                                                    'update' => function ($url, $data)  {
                                                        return Html::a(
                                                            FA::i('arrow-right')->size('lg'),
                                                            $url, ['class' => 'text-secondary']);
                                                    },
                                                    'delete' => function ($url, $data)  {
                                                        return Html::a(
                                                            FA::i('trash')->size('lg'),
                                                            $url, ['class' => 'text-danger',
                                                            'title'=>"Удалить", 'aria-label'=>"Удалить", 'data-pjax'=>"0", 'data-confirm'=>"Вы уверены, что хотите удалить этот элемент?", 'data-method'=>"post"
                                                        ]);
                                                    },
                                                ],
                                                'options' => ['width' => '80px'],
                                            ],
                                        ],
                                    ]); ?>

                                    <?php Pjax::end(); ?>


                                </div>
                            </div>
                        </div>


                        <!--                Календарик        -->
                        <div role="tabpanel" class="tab-pane active" id="uz">
                            <iframe src="http://booking/" style=' overflow: scroll; width:100%; height:100vh; overflow: scroll;' title="Iframe Example"></iframe>

                            <!--                            <div class="row">-->
<!--                                <div class="col-sm-12">-->
<!--                                    <div id="scheduler_here" class="dhx_cal_container"-->
<!--                                         style='width:100%; height:100vh;'>-->
<!--                                        <div class="dhx_cal_navline" style="width: 100%;">-->
<!---->
<!--                                            <div style="font-size:16px;padding:4px 20px; width: 100%">-->
<!--                                                Показать категории:-->
<!--                                                <select id="room_filter" onchange='updateSections(this.value)'></select>-->
<!--                                            </div>-->
<!--                                            <div class="dhx_cal_prev_button" >&nbsp;</div>-->
<!--                                            <div class="dhx_cal_next_button" >&nbsp;</div>-->
<!--                                            <div class="dhx_cal_today_button"></div>-->
<!--                                            <div class="dhx_cal_date" style="width: 100%"></div>-->
<!--                                        </div>-->
<!--                                        <div class="dhx_cal_header">-->
<!--                                        </div>-->
<!--                                        <div class="dhx_cal_data">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!---->
<!--                                </div>-->
<!--                            </div>-->
                        </div>


                    </div>


                </div>

            </div>

        </div>

    </div>
</div>

<style>
    iframe {
        border-width: 0px;
        border-style: inset;
        border-color: initial;
        border-image: initial;
    }

</style>


