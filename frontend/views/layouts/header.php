<?php
/**
 * Created by PhpStorm.
 * User: lexcorp
 * Date: 01.04.2018
 * Time: 17:45
 */

use common\models\Category;
use yii\helpers\Url;

use frontend\widgets\WCategory;

$this->registerCss("
    .cities_dropdown>li>a {
        padding: 9px 15px;
    }
");

$currency = Yii::$app->session->get('currency', 'uzs');
if(Yii::$app->request->get('currency', '') == 'uzs' || Yii::$app->request->get('currency', '') == 'usd') {
    $currency = Yii::$app->request->get('currency', Yii::$app->session->get('currency', 'uzs'));
    Yii::$app->session->set('currency', $currency);
    Yii::$app->response->redirect(Url::current(['currency' => '']));
}

$root_cat = Yii::$app->session->get('root_category');
$root_category = Category::find()->where(['id' => $root_cat, 'status' => 1, 'parent_id' => null])->orderBy('order')->one();
$page = Yii::$app->session->get('page');
$city_id = Yii::$app->session->get('city_id');

if ($city_id) {
    $city = \common\models\City::find()->where(['status' => 1, 'id' => $city_id])->orderBy('`order` ASC')->one();
}
$cities = \common\models\City::find()->where(['status' => 1])->orderBy('`order` ASC')->all();
?>

<nav class="navbar navbar-expand-lg header">
    <div class="container position-relative">
        <a class="navbar-brand" href="<?=Url::to(['/'])?>">
            <img src="/gta/images/logo.png" alt="<?= Yii::$app->params['appName'] ?>">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <img src="/gta/images/open-menu.png" alt="">
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <?= WCategory::widget(['key' => 'menu']); ?>
        </div>
    </div>
</nav>