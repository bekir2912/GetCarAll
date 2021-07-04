<?php

use rmrevin\yii\fontawesome\FA;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Пользовтели');
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
                            'shop_id',
                            'username',
                            'name',
                            'auth_key',
                            //'password_hash',
                            //'password_reset_token',
                            //'status',
                            //'city_id',
                            //'birthday',
                            //'avatar',
                            //'balance',
                            //'ucard',
                            //'phone',
                            //'push',
                            //'created_at',
                            //'updated_at',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>

                    <?php Pjax::end(); ?>

                </div>

            </div>

        </div>

    </div>
</div>


