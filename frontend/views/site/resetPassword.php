<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('frontend', 'Reset password form');
?>

<div class="white-block">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header text-center"><?= Html::encode($this->title) ?></div>
        </div>
        <div class="col-md-6 offset-sm-3">
            <div class="news_body">
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
