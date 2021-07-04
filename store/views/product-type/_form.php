<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductType */
/* @var $form yii\widgets\ActiveForm */



$max_id = \common\models\ProductType::find()->max('id');




?>

<div class="product-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id',['template' => '{input}'])->hiddenInput(['value'=> $max_id+1]) ?>

    <div div class="row">
        <div class="col-sm-6 col-md-6">
            <?= $form->field($model, 'name')->textarea(['rows' => 1]) ?>
        </div>

        <div class="col-sm-6 col-md-6">
            <?= $form->field($model, 'status')->dropDownList(["1" => "Активен", "0" => "Не активен"]) ?>
        </div>
    </div>




<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
