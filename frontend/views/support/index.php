<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 14.10.2017
 * Time: 2:34
 */

use yii\bootstrap\ActiveForm;

\frontend\assets\AppAsset::register($this);

$this->title = "Техподдержка";
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">

            <div class="operator text-center">
                <img src="/uploads/site/operator.png" class="operator__icon img-fluid" style="display: inline-block;">
                <h4 class="operator__heading">
                    <?=Yii::t('frontend', 'Support')?>:
                </h4>
                <p class="operator__text">
                    <?=Yii::$app->params['client_support_service']?>
                </p>
                <p class="operator__text">
                    <?=Yii::$app->params['infoEmail']?>
                </p>
            </div>

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'type')->dropDownList([
                'Помощь',
                'Жалоба',
                'Предложение',
            ]) ?>
            <?php if($user) { ?>
                <?= $form->field($model, 'name')->textInput(['readonly' => 'readonly', 'value' => $user->name]) ?>
                <?= $form->field($model, 'contact')->textInput(['readonly' => 'readonly', 'value' => $user->username]) ?>
            <?php } else { ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'contact') ?>
            <?php } ?>
            <?= $form->field($model, 'text')->textarea() ?>
            <p>
                <button class="btn btn-success">Отправить</button>
            </p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
