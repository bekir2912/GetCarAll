<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BookingStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Статус Бронирования');
?>

<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="product-index">

                    <div class="page-header">

                        <?= Html::encode($this->title) ?>

                        <?= Html::a('Добавить ' . FA::i('plus'), ['create'], ['class' => 'btn btn-success']) ?>

                    </div>
                    <p></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


                </div>

            </div>

        </div>

    </div>
</div>



