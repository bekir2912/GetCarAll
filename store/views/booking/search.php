<?php

use common\models\Booking;
use common\models\Shop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
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


    <?php if (1) { ?>
        <form action="<?= Url::to(['booking/search']) ?>" id="brand-form" method="get">
            <div class="form-group">
                <label for="category_id">Введите номер пользователя</label>
                <input type="text" name="q" placeholder="номер телефона" id="category_id" class="form-control"/>
                <input type="submit" value="Проверить пользователя" class="btn btn-success" >
                <!--                    <a href="--><?//= Url::current(['category' => '']) ?><!--"-->
                <!--                       onclick="if(!confirm('Ожидается форма для сохранения. Отменить?'))return false;">Выбрать-->
                <!--                        другую-->
                <!--                        категорию</a>-->
            </div>
        </form>
    <?php } ?>


    <?php $form = ActiveForm::begin();  print_r($user) ?>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
