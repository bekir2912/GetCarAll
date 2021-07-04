<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductType */

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
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>


</div>
</div>
</div>
</div>
</div>
