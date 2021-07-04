<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Черный список пользовтелей');
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

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            [
                                'attribute' => 'user_id',
                                'value' => function($data) {
                                    return $data->user->name;
                                },
                            ],
                            'name',
                            'description:ntext',
                            ['attribute' => 'created_at', 'format' => ['date', 'php:d-m-Y H:i:s']],
                            ['attribute' => 'updated_at', 'format' => ['date', 'php:d-m-Y H:i:s']],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update}{delete}{view}',
                                'buttons' => [
                                    'update' => function ($url, $data)  {
                                        return Html::a(
                                            FA::i('cog')->size(FA::SIZE_LARGE),
                                            $url, ['class' => 'text-primary']);
                                    },
                                    'view' => function ($url, $data)  {
                                        return Html::a(
                                            FA::i('eye')->size(FA::SIZE_LARGE),
                                            $url, ['class' => 'text-secondary']);
                                    },
                                    'delete' => function ($url, $data)  {
                                        return Html::a(
                                            FA::i('trash')->size(FA::SIZE_LARGE),
                                            $url, ['class' => 'text-danger',
                                            'title'=>"Удалить", 'aria-label'=>"Удалить", 'data-pjax'=>"0", 'data-confirm'=>"Вы уверены, что хотите удалить этот элемент?", 'data-method'=>"post"
                                        ]);
                                    },
                                ]
                            ],
                        ],
                    ]); ?>

                    <?php Pjax::end(); ?>
                </div>

            </div>

        </div>

    </div>
</div>


