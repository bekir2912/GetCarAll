<?php

use common\models\Shop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
//use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;


//use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\checkbox\CheckboxX;
//s
use yii\web\UploadedFile;

// widget
//

/* @var $this yii\web\View */
/* @var $model common\models\Booking */
/* @var $form yii\widgets\ActiveForm */

$selected_shop = Shop::findOne(['id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0]);



// Получение авто компании
$all_products = \common\models\Product::find()->andWhere(['status' => '1'])->andWhere(['shop_id' => "$selected_shop->id"])->orderBy('`id` DESC')->all();
$prods = [];
if (!empty($all_products)) {
    foreach ($all_products as $prod) {
        $prods[$prod->id] = '(' . ($prod->brand->name) . ') ' . $prod->lineup->translate->name;
    }
}



// Получение списка адресов компании
$all_adress_pickup = \common\models\Location::find()->andWhere(['shop_id' => "$selected_shop->id"])->andWhere(['category' => 2])->andWhere(['status' => '1'])->orderBy('`id` DESC')->all();
$all_adress_return = \common\models\Location::find()->andWhere(['shop_id' => "$selected_shop->id"])->andWhere(['category' => 1])->andWhere(['status' => '1'])->orderBy('`id` DESC')->all();

$adres_pickup = [];
if (!empty($all_adress_pickup)) {
    foreach ($all_adress_pickup as $loc) {
        $adres_pickup[$loc->id] =  $loc->address;
    }
}


$adres_return = [];
if (!empty($all_adress_return)) {
    foreach ($all_adress_return as $loc) {
        $adres_return[$loc->id] =  $loc->address;
    }
}

$all_status = \common\models\BookingStatus::find()->orderBy('`id` DESC')->all();
$stat = [];
if (!empty($all_status)) {
    foreach ($all_status as $s) {
        $stat[$s->id] = $s->name;
    }
}




?>

<div class="product-form">


    <?php if(!$user->id){?>
        <div class="row">
            <div class="col-sm-6 col-md-6 ">
                    <form action="<?= Url::to(['booking/search']) ?>" id="brand-form" method="get">
                        <div class="form-group">
                            <label for="category_id">Введите номер пользователя</label>
                            <div class="d-flex p-2 bd-highlight">
                                <input type="text" name="q" placeholder="номер телефона c кодом" id="category_id" class="form-control"/>
                                <input type="submit" value="Проверить пользователя" class="btn btn-success ml-5" >
                            </div>
                        </div>
                    </form>
            </div>
        </div>

    <?php }?>






    <?php if(!empty($user)){?>



    <?php $form = ActiveForm::begin(); ?>


        <?= $form->field($model, 'user_id')->hiddenInput(['value' => $user->id])->label(false) ?>
        <div class="row">

            <div class="col-sm-6 col-md-4">
                <?= $form->field($model, 'return_shop_id')->dropDownList($adres_return, ['prompt'=>'- Выберите место получения авто -']) ?>
            </div>


            <div class="col-sm-6 col-md-4">
                <?= $form->field($model, 'pickup_shop_id')->dropDownList($adres_pickup,['prompt'=>'- Выберите место сдачи авто -']) ?>
            </div>


            <div class="col-sm-6 col-md-4">
                <?= $form->field($model, 'product_id')->dropDownList($prods,['prompt'=>'- Выберите авто для сдачи в аренду -']) ?>
            </div>

        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?=
                    $form->field($model, 'start_date')->widget(DateTimePicker::className(), [
                        'name' => 'start_date',
                        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                        //'type' => DateTimePicker::TYPE_INLINE,
                        'layout' => '{picker}{input}{remove}',
                        //'value' => '23-Feb-1982 10:10',
                        'options' => ['placeholder' => 'Выберите дату получания авто...'],
                        'pluginOptions' => [
                            'language' => 'th',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd hh:ii'
                        ]
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?=
                    $form->field($model, 'end_date')->widget(DateTimePicker::className(), [
                        'name' => 'end_date',
                        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                        //'type' => DateTimePicker::TYPE_INLINE,
                        'layout' => '{picker}{input}{remove}',
                        //'value' => '23-Feb-1982 10:10',
                        'options' => ['placeholder' => 'Выберите дату возрата...'],
                        'pluginOptions' => [
                            'language' => 'th',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'format' => 'yyyy-m-d hh:ii',
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <!--        <div class="col-sm-6 col-md-4">-->
            <!--            --><?//= $form->field($model, 'pickup_location')->textInput(['maxlength' => true]) ?>
            <!--        </div>-->
            <!---->
            <div class="col-sm-8 col-md-8">
                <?= $form->field($model, 'text')->textarea(['rows' => 8, 'placeholder' => "Введите дополнительную информацию о брони"]) ?>
            </div>


            <div class="col-sm-4 col-md-4">
                <?= $form->field($model, 'status')->dropDownList($stat) ?>
                <div style="display: flex; align-items: center">
                    <p><b>Имя: </b></p>
                    <p style="padding:  0 0 0 10px;"><?=$user->name?></p>
                </div>
                <div style="display: flex; align-items: center">
                    <p><b>Паспортные данные: </b></p>
                    <p style="padding:  0 0 0 10px;"><?=$user->passport_serial?></p>
                </div>
                <div style="display: flex; align-items: center">
                    <p><b>Username: </b></p>
                    <p style="padding:  0 0 0 10px;"><?=$user->username ?></p>
                </div>
                <a target="_blank" href="<?= Url::to(["user/view?id=$user->id"]) ?>"
                   onclick="if(!confirm('Хотетите узнать о пользовтели подробно?'))return false;">Подробно...</a>
            </div>

        </div>




        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    <?php } else{ ?>
    <div class="product-form">
        <a target="_blank" href="<?= Url::to(['user/create']) ?>"
           onclick="if(!confirm('Хотетите зарегистрировать  подьзователя?'))return false;">Зарегистрирвоать пользователя?</a>
    </div>
    <?php }  ?>
</div>
