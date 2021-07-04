<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 14.10.2017
 * Time: 2:34
 */

use yii\bootstrap\ActiveForm;

$this->title = Yii::t('frontend', 'Add Forum Theme');
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<?= $form->field($model, 'question')->textarea() ?>
<div class="form-group">
    <input type="file" name="file">
</div>
<p>
    <button class="btn btn-success"><?=Yii::t('frontend', 'Add Forum Theme')?></button>
</p>
<?php ActiveForm::end(); ?>
