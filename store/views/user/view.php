<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
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
                                'confirm' => Yii::t('app', 'Вы уверены что хотите удалить этого пользователя?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </p>

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'shop_id',
                                'value' => function($data) {
                                    return $data->shop->name;
                                },
                            ],
                            'username',
                            'name',
//                            'auth_key',
//                            'password_hash',
//                            'password_reset_token',
                            [
                                'attribute' => 'status',
                                'format'=> 'html',
                                'filter' => array("0" => "Не активный", "10" => "Активный"),
                                'value' => function($data) {
                                    if($data->status == '0') {
                                        return '<span class="text-danger"><i class="fa fa-info"></i> Не активный</span>';
                                    }
                                    if($data->status == '10') {
                                        return '<span class="text-success"><i class="fa fa-check"></i> Активный</span>';
                                    }
                                },
                            ],
                            [
                                'attribute' => 'city_id',
                                'value' => function($data) {
                                    return $data->city->translate->name;
                                },
                            ],

                            'birthday',
//                            'avatar',
//                            'balance',
//                            'ucard',
                            'phone',
//                            'push',
                            ['attribute' => 'created_at', 'format' => ['date', 'php:d-m-Y H:i:s']],
                            ['attribute' => 'updated_at', 'format' => ['date', 'php:d-m-Y H:i:s']],
                        ],
                    ]) ?>
                    <div class="container">
                        <div class="row justify-content-md-center">
                            <div class="col-md-auto">
                                <img  src="<?=$model->avatar?>" alt="client_photo" style="max-width: 400px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
