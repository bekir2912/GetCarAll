<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BodyStyle */

$this->title = Yii::t('app', 'Create Body Style');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Body Styles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="body-style-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
