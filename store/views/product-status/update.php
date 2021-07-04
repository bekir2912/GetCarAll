<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductStatus */

$this->title = Yii::t('app', 'Изменит статус: {name}', [
    'name' => $model->name,
]);
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="shop-create">

                    <div class="page-header"><?= Html::encode($this->title) ?></div>
                    <p></p>
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>
