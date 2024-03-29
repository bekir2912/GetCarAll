<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = 'Добавить авто';
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="sale-create">
                    <div class="page-header"><?= Html::encode($this->title) ?></div>
                    <p></p>

    <?= $this->render('_form', [
        'model' => $model,
        'category' => $category,
        'brand' => $brand,
        'lineup' => $lineup,
        'info' => $info,
        'info_uz' => $info_uz,
        'info_oz' => $info_oz,
        'info_en' => $info_en,
    ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>
