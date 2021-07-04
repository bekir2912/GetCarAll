    <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

    $this->registerJsFile('/inputmask/jquery.inputmask.bundle.min.js', ['depends' => 'yii\web\JqueryAsset']);
    $this->registerJsFile('/inputmask/inputmask/bindings/inputmask.binding.min.js', ['depends' => 'yii\web\JqueryAsset']);


$this->registerJs('
    $(\'.phone\').inputmask({mask: "+\\\\9\\\\9\\\\8 (99) 999-99-99"});
');

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;

$this->title = Yii::t('frontend', 'Sign In');
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header text-center"><?= Html::encode($this->title) ?></div>
        </div>
        <div class="col-md-6 offset-sm-3">
            <div class="news_body">
                <?= Alert::widget() ?>
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'phone form-control', 'placeholder' => Yii::t('frontend', 'Phone placeholder')]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="pull-left">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <div class="pull-right">
                    <?= Html::a(Yii::t('frontend', 'Rest Password'), ['site/request-password-reset'], ['style' => 'margin-top: 10px;display: inline-block;']) ?>
                </div>

                <div class="clearfix"></div>

                <div class="form-group text-center">
                    <?= Html::submitButton(Yii::t('frontend', 'Sign In'), ['class' => 'btn btn-login', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
                <div class="separator"></div>
                <div class="text-center">
                    <?= Html::a(Yii::t('frontend', 'Sign Up'), ['site/signup']) ?>
                </div>
            </div>
        </div>
    </div>
