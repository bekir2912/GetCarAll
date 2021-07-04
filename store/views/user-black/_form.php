<?php

use common\models\Shop;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserBlack */
/* @var $form yii\widgets\ActiveForm */




$selected_shop = Shop::findOne(['id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0]);

$all_black = \common\models\User::find()->andWhere(['status' => '10'])->andWhere(['shop_id' => "$selected_shop->id"])->orderBy('`id` DESC')->all();
$users = [];
if (!empty($all_black)) {
    foreach ($all_black as $prod) {
        $users[$prod->id] = $prod->name;
    }
}

?>

<div class="user-black-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shop_id')->hiddenInput(['value'=>  Yii::$app->session->get('shop_id'), 'deleted_at' => 0])->label(false) ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'user_id')->dropDownList($users,['prompt'=>'- Выберите пользовтеля для добавления в черный список-']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
