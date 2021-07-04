<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->address;
\yii\web\YiiAsset::register($this);
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="shop-create">

                    <div class="page-header"><?= Html::encode($this->title) ?></div>
                    <p></p>

    <p>
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
//            'shop_id',
            'address',
            'discription:ntext',
            [
                'attribute' => 'category',
                'value' => function ($model){
                    return $model->status==2?'Место вывоза авто':'Место получения авто';
                },
            ],
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
