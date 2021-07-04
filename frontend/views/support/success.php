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

            <p class="text-center">
                <strong>
                    Спасибо за опращение, мы свяжемся с вами в ближайщее время.
                </strong>
            </p>
        </div>
    </div>
</div>
