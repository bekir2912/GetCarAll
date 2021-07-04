<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BookingStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="booking-status-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
