<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserBlack */


$this->title = Yii::t('app', 'Добавить пользовтеля в черный список');
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
                <div class="shop-create">


                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>


                </div>
            </div>
        </div>
    </div>
</div>
