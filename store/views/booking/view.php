<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = $model->id;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Бронирование'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="shop-create">

                    <div class="page-header"><?= Html::encode("Бронь № $this->title") ?></div>
                    <p></p>
        <?= Html::a(Yii::t('app', 'Изменить'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'pickup_shop_id',
                'value' => function($data) {
                    return $data->pickupLocation->address;
                },
            ],
            [
                'attribute' => 'return_shop_id',
                'value' => function($data) {
                    return $data->returnLocation->address;
                },
                'options' => ['width' => '200px'],
                'filter' => $cat_filter,
            ],
            [
                'attribute' => 'product_id',
                'value' => function($data) {
                    return $data->product->translate->name;
                },
            ],
            'start_date',
            'end_date',
//            'pickup_location',
//            'return_location',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    return $model->status==1?'Активный':'Заблокирован';
                },
            ],
        ],
    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
