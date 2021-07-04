<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
            'shop_id',
            'category_id',
            'brand_id',
            'sale_id',
            'price',
            'warranty',
            'url:url',
            'view',
            'interesting',
            'interesting_order',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
