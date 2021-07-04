<?php

namespace frontend\controllers;

use common\models\Banner;
use common\models\BannerTranslation;
use common\models\Category;
use common\models\Product;
use frontend\components\AuthHandler;
use common\models\Callback;
use common\models\Shop;
use common\models\StaticPage;
use common\models\StaticPageCategory;
use common\models\User;
use frontend\models\SupportForm;
use frontend\widgets\WProduct;
use Yii;
use yii\base\InvalidParamException;
use yii\di\Instance;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SupportController extends Controller
{
    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSend()
    {
        $this->layout = 'empty';
        $model = new SupportForm();

        $user_id = Yii::$app->request->get('user_id');

        $user = null;
        if($user_id) {
            $user = User::find()->where(['id' => $user_id])->one();
        }

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            return $this->redirect(['success']);
        }

        return $this->render('index', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionSuccess()
    {
        $this->layout = 'empty';
        return $this->render('success');
    }
}
