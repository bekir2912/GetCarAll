<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OptionGroup */

$this->title = 'Группа опций: ' . $model->translate->name;
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
                        'info' => $info,
                        'info_uz' => $info_uz,
                        'info_oz' => $info_oz,
                        'info_en' => $info_en,
                        'brands' => $brands,
                    ]) ?>

                </div>
            </div>
        </div>
    </div>
</div>