<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserBlack */

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
                                'confirm' => Yii::t('app', 'Вы уверены что хотите удалить этого пользователя из черного списка?'),
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
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
