<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

$this->registerJsFile('/inputmask/jquery.inputmask.bundle.min.js', ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile('/inputmask/inputmask/bindings/inputmask.binding.min.js', ['depends' => 'yii\web\JqueryAsset']);


$this->registerJs('
    $(\'.phone\').inputmask({mask: "+\\\\9\\\\9\\\\8 (\\\\99) 999-99-99"});
');
$this->title = Yii::t('frontend', 'Request password reset');
?>

<div class="white-block">
<div class="row">
    <div class="col-lg-12">
        <div class="page-header text-center"><?= Html::encode($this->title) ?></div>
    </div>
    <div class="col-md-6 offset-sm-3">
        <div class="news_body">
            <?= Alert::widget() ?>
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

        <?= $form->field($model, 'username')->textInput(['class' => 'phone form-control','autofocus' => true, 'placeholder' => Yii::t('frontend', 'Phone placeholder')]) ?>

        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('frontend', 'Reset password'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    </div>
</div>
</div>
