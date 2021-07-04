<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProductStatus */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="shop-create">

                    <div class="page-header"><?= Html::encode('Статус:      ' .  $this->title) ?></div>
                    <p></p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name:ntext',
        ],
    ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>
