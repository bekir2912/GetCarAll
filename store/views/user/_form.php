<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\web\UploadedFile;
use kartik\widgets\FileInput;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */

$all_adress_return = \common\models\City::find()->where(['status' => '1'])->orderBy('`id` ASC')->all();
$adres_return = [];
if (!empty($all_adress_return)) {
    foreach ($all_adress_return as $loc) {
        $adres_return[$loc->id] =  $loc->translate->name;
    }
}



?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'shop_id')->hiddenInput(['value' => Yii::$app->session->get('shop_id')])->label(false) ?>

    <div class="row">
        <div class="col-6 md-4">
            <?= $form->field($model, 'phone')->textInput() ?>

            <?php if($model->username){ ?>


            <div class="form-group ">
                <label class="control-label" >Логин</label>
                <p style="padding-left: 10px;" class="text-secondary"><?=$model->username?></p>
            </div>
               <?php } ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'name')->textInput() ?>
            <?= $form->field($model, 'birthday')->widget(DatePicker::className(), [
                'name' => 'birthday',
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'layout' => '{picker}{input}{remove}',
                //'value' => '23-Feb-1982 10:10',
                'options' => ['placeholder' => 'Выберите дату рождения...'],
                'pluginOptions' => [
                    'language' => 'th',
                    'todayHighlight' => true,
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]);
            ?>
            <?= $form->field($model, 'passport_serial')->textInput() ?>

            <!--            --><?//= $form->field($model, 'birthday')->textInput(['maxlength' => true]) ?>
            <!--            --><?//= $form->field($model, 'balance')->textInput() ?>
            <!--            --><?//= $form->field($model, 'ucard')->textInput() ?>



        </div>
        <div class="col-6">
            <div class="row">
                <div class="col-6 md-4">
                    <?= $form->field($model, 'status')->dropDownList(['10' => 'Активен', '0' => 'Заблокирован']) ?>
                </div>
                <div class="col-6 md-4">
                    <?= $form->field($model, 'city_id')->dropDownList($adres_return, ['prompt'=>'- Выберите город -']) ?>
                </div>
            </div>

            <p>
                <strong>Выберите фото клинта</strong>
            </p>
            <?php
            if ($model->avatar != '') {
                ?>
                <img src="<?= $model->avatar ?>" alt="<?= $model->avatar ?>" class="img-responsive">
            <?php } ?>
            <?php echo $form->field($model, 'avatar')->widget(FileInput::classname(), [
                'options' => ['accept' => 'image/*'],
            ])->label(false);?>
        </div>

    </div>


    <div class="row">
        <div class="col-6 md-4">

        </div>
        <div class="col-6 md-4">

        </div>
    </div>





<!--    --><?//= $form->field($model, 'push')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
