<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Пункты ввоза и вывоза авто');
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

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
//            'shop_id',
            'address',
//            'discription:ntext',
            [
                'attribute' => 'category',
                'value' => function ($model){
                    return $model->category==1?'Место вывоза авто':'Место возврата авто';
                },
            ],
//            'category',
            [
                'attribute' => 'status',
                'value' => function ($model){
                    return $model->status==1?'Активный':'Заблокирован';
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

                </div>

            </div>

        </div>

    </div>
</div>



