<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Тип авто');
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
                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            'name:ntext',
                            [
                                'attribute' => 'status',
                                'format'=> 'html',
                                'filter' => array("0" => "Не активный", "1" => "Активный"),
                                'value' => function($data) {
                                    if($data->status == '0') {
                                        return '<span class="text-danger"><i class="fa fa-info"></i> Не активный</span>';
                                    }
                                    if($data->status == '1') {
                                        return '<span class="text-success"><i class="fa fa-check"></i> Активный</span>';
                                    }
                                },
                            ],
                            ['attribute' => 'created_at', 'format' => ['date', 'php:d-m-Y H:i:s']],
                            ['attribute' => 'updated_at', 'format' => ['date', 'php:d-m-Y H:i:s']],


                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>

            </div>

        </div>

    </div>
</div>


