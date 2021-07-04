<?php

use common\models\Shop;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\widgets\ActiveForm */

$selected_shop = Shop::findOne(['id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0]);

?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'shop_id')->textInput() ?>

    <div class="row">
        <div class="col-6">

            <?= $form->field($model, 'address')->textInput(['maxlength' => true,  'placeholder' => html::encode($model->getAttributeLabel('Введите адрес')),]) ?>
            <?= $form->field($model, 'category')->dropDownList([1 => 'Место вывоза авто', 2 => 'Место возврата авто авто']) ?>
            <?= $form->field($model, 'status')->dropDownList(['1' => 'Активен', '2' => 'Заблокирован']) ?>
            <?= $form->field($model, 'shop_id')->hiddenInput(['maxlength' => true,'shop_id' => "$selected_shop->id",'value' => "$selected_shop->id"])->label(false) ?>
        </div>

        <div class="col-6">
            <?= $form->field($model, 'discription')->textarea(['rows' => 8]) ?>

        </div>
    </div>




<!--    --><?//= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
