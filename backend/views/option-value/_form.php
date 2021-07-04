<?php

use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OptionValue */
/* @var $form yii\widgets\ActiveForm */
$cats = \common\models\OptionGroup::find()->all();
$cat_filter = [];
if(!empty($cats)) {
    foreach ($cats as $cat) {
        $row_cat = [];
        $row_cat[$cat->id] = $cat->translate->name.' ('.$cat->category->translate->name.')';
        $cat_filter = ArrayHelper::merge($cat_filter, $row_cat);
    }
}
?>

<div class="option-value-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
    <?= $form->field($model, 'group_id')->dropDownList($cat_filter) ?>
        </div>
    </div>
    <p></p>
    <div class="separator"></div>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#perf_ru" aria-controls="perf_ru" role="tab"
                                                          data-toggle="tab">Русский</a></li>
                <li role="presentation"><a href="#perf_uz" aria-controls="perf_uz" role="tab" data-toggle="tab">Узбекский</a>
                </li>
                <li role="presentation"><a href="#perf_oz" aria-controls="perf_oz" role="tab" data-toggle="tab">Oz'bekcha</a>
                </li>
                <li role="presentation"><a href="#perf_en" aria-controls="perf_en" role="tab" data-toggle="tab">Английский</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="perf_ru">
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($info, 'name[ru]')->textInput(['value' => $info->name]) ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane " id="perf_uz">
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($info_uz, 'name[uz]')->textInput(['value' => $info_uz->name]) ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane " id="perf_oz">
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($info_oz, 'name[oz]')->textInput(['value' => $info_oz->name]) ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane " id="perf_en">
                    <p></p>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($info_en, 'name[en]')->textInput(['value' => $info_en->name]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <?php $fileinput_options = ['accept' => 'image/*', 'multiple' => false];
            if ($model->image != '') {
                $fileinput_options = ['accept' => 'image/*', 'multiple' => false];
                ?>
            <?php } ?>
        </div>
    </div>
    <div class="separator"></div>
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <?= $form->field($model, 'order')->textInput(['value' => ($model->order != '')? $model->order: 0]) ?>
        </div>
        <div class="col-sm-6 col-md-4">
            <?= $form->field($model, 'status')->dropDownList(['1' => 'Активен', '0' => 'Не активен']) ?>
        </div>
    </div>
    <div class="form-group">
        <a href="<?= Url::to(['option-value/index', 'sort' => '-id']) ?>" class="btn btn-secondary btn-cancel">Назад</a>
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if(!$model->isNewRecord) { ?>
            <a href="<?= Url::to(['option-value/create']) ?>" class="btn btn-success btn-cancel">Добавить еще</a>
        <?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('
    $(document).on(\'ready pjax:success pjax:error\', function() {
        $(\'select.form-control\').select2(
            {
                language: {
                  noResults: function () {
                    return "Ничего не найдено";
                  }
                }
            }
        );
    });
');?>