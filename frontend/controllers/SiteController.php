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
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $category = Category::find()->where(['status' => 1, 'parent_id' => null])->orderBy('order')->one();
        return $this->redirect(['category/index', 'id' => $category->url]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['announcement/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['announcement/index']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionMapIcon()
    {
        header( "Content-type: image/png" );
        $new_image = imagecreatetruecolor(17, 17);

        $background = imagecolorallocate($new_image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        $col_transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefill($new_image, 0, 0, $col_transparent);  // set the transparent colour as the background.
        imagecolortransparent ($new_image, $col_transparent); // actually make it transparent
        imagefilledellipse($new_image, 8, 8, 16, 16, $background);
        imagepng( $new_image );
        imagedestroy( $new_image );
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $password = mt_rand(100000, 999999);
            if ($user = $model->signup($password)) {
//                if (Yii::$app->getUser()->login($user)) {
                    $model->sendEmail($password);
                    if($model->isEmail()) {
                        Yii::$app->session->setFlash('success', Yii::t('frontend', 'Password send to your email'));
                    } else if($model->isPhone()){
                        Yii::$app->session->setFlash('success', Yii::t('frontend', 'Password send to your phone'));
                    }
                    return $this->redirect(['site/login']);
//                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {

                if($model->isEmail()) {
                    Yii::$app->session->setFlash('success', Yii::t('frontend', 'Check your email for further instructions.'));
                } else if($model->isPhone()){
                    Yii::$app->session->setFlash('success', Yii::t('frontend', 'Password send to your phone'));
                }

                return $this->redirect(['site/login']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('frontend', 'There is no user with this email address.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('frontend', 'New password saved.'));

            return $this->redirect(['user/index']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionPage($id)
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        $page = StaticPage::findOne(['status' => 1, 'url' => $id]);
        if (empty($page)) return $this->redirect(['site/error']);
        $page_cat = StaticPageCategory::findOne(['id' => $page->category_id]);
        if (empty($page) || $page_cat->status == 0) return $this->redirect(['site/error']);

        return $this->render('page', [
            'page' => $page,
        ]);
    }

//    public function actionCallback()
//    {
//        /** Validate */
//        if (strlen(Yii::$app->request->post('name')) < 2 ||
//            strlen(Yii::$app->request->post('name')) > 100 ||
//            !preg_match("/^\+998(\d{9})$/i", Yii::$app->request->post('phone'))
//        )
//            return json_encode(['error' => true]);
//        //check timeout
//        $cb_check = Yii::$app->session->get('cb_timeout') ? Yii::$app->session->get('cb_timeout') : false;
//        $cb_timeout = [];
//        if ($cb_check) $cb_timeout = Callback::findOne($cb_check);
//        if (!empty($cb_timeout)) {
//            if (($cb_timeout->created_at + 60 * Yii::$app->params['callback_timeout']) > time() && (Yii::$app->session->get('cb_shop') == Yii::$app->request->post('shop_id'))) {
//                return json_encode(['error' => true]);
//            }
//        }
//        /*if (Yii::$app->user->id) {
//            $user = User::findOne(Yii::$app->user->id);
//            $cb = new Callback();
//            $cb->shop_id = Yii::$app->request->post('shop_id');
//            $cb->name = $user->name;
//            $cb->phone = ($user->phone !== '') ? $user->phone : Yii::$app->request->post('phone');
//            $cb->status = 0;
//            if ($cb->save()) {
//                Yii::$app->session->set('cb_shop', $cb->shop_id);
//                Yii::$app->session->set('cb_timeout', $cb->id);
//                return json_encode(['error' => false]);
//            } else return json_encode(['error' => true]);
//        } else*/
//        if (!empty(Yii::$app->request->post('name')) && !empty(Yii::$app->request->post('phone')) && !empty(Yii::$app->request->post('shop_id'))) {
//            $cb = new Callback();
//            $cb->shop_id = Yii::$app->request->post('shop_id');
//            $cb->name = Yii::$app->request->post('name');
//            $cb->phone = Yii::$app->request->post('phone');
//            $cb->status = 0;
//            if ($cb->save()) {
//                Yii::$app->session->set('cb_shop', $cb->shop_id);
//                Yii::$app->session->set('cb_timeout', $cb->id);
//                $temp_shop = Shop::findOne($cb->shop_id);
//                if (!empty($temp_shop)) {
//                    Yii::$app
//                        ->mailer
//                        ->compose(
//                            ['html' => 'callback-html', 'text' => 'callback-text'],
//                            ['type' => 'shop']
//                        )
//                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
//                        ->setTo($temp_shop->info->email)
//                        ->setSubject(Yii::$app->name)
//                        ->send();
//                }
//                return json_encode(['error' => false]);
//            } else return json_encode(['error' => true]);
//        }
//        return json_encode(['error' => true]);
//    }

    public function actionShowNumber()
    {
        $id = !empty(Yii::$app->request->post('id')) ? Yii::$app->request->post('id') : false;
        if (!$id) {
            return Yii::t('frontend', 'N/A');
        }
        $product = Product::findOne(['id' => $id, 'status' => 1]);
        if ($product) {
            if ($product->shop_id) {
                $shop = Shop::findOne(['id' => $product->shop_id, 'status' => 1]);
                if (empty($shop)) {
                    return Yii::t('frontend', 'N/A');
                }

                if ($shop->info->phone != '') {
                    $shop_phones = (!empty(Yii::$app->session->get('shop_phones'))) ? Yii::$app->session->get('shop_phones') : [];
                    if (!in_array($shop->id, $shop_phones)) {
                        $shop_phones[] = $shop->id;
                        Yii::$app->session->set('shop_phones', $shop_phones);
                        $shop->view_phone++;
                        $shop->save();
                    }
                    $product_phones = (!empty(Yii::$app->session->get('prod_phones'))) ? Yii::$app->session->get('prod_phones') : [];
                    if (!in_array($product->id, $product_phones)) {
                        $product_phones[] = $product->id;
                        Yii::$app->session->set('prod_phones', $product_phones);
                        $product->phone_views++;
                        $product->save();
                    }
                    return nl2br($shop->info->phone);
                }
            } elseif ($product->user_id) {
                $user = User::findOne(['id' => $product->user_id, 'status' => 10]);
                if (empty($user)) {
                    return Yii::t('frontend', 'N/A');
                }

                if ($user->phone != '') {
                    $product_phones = (!empty(Yii::$app->session->get('prod_phones'))) ? Yii::$app->session->get('prod_phones') : [];
                    if (!in_array($product->id, $product_phones)) {
                        $product_phones[] = $product->id;
                        Yii::$app->session->set('prod_phones', $product_phones);
                        $product->phone_views++;
                        $product->save();
                    }
                    return nl2br($user->phone);
                }
            }
        }

        return Yii::t('frontend', 'N/A');
    }
    public function actionShowShopNumber()
    {
        $id = !empty(Yii::$app->request->post('shop'))? Yii::$app->request->post('shop'): false;
        if(!$id) {
            return Yii::t('frontend', 'N/A');
        }
        $shop = Shop::findOne($id);
        if(empty($shop)) {
            return Yii::t('frontend', 'N/A');
        }

        if($shop->info->phone != '') {
            $shop_phones = (!empty(Yii::$app->session->get('shop_phones')))? Yii::$app->session->get('shop_phones'): [];
            if(!in_array($shop->id, $shop_phones)) {
                $shop_phones[] = $shop->id;
                Yii::$app->session->set('shop_phones', $shop_phones);
                $shop->view_phone++;
                $shop->save();
            }
            return nl2br($shop->info->phone);
        }

        return Yii::t('frontend', 'N/A');
    }

    public function beforeAction($action)
    {
        if ($action->id == 'auth-back') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionAuthBack()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('selected_cat', false);
        header('Access-Control-Allow-Origin: *');
        header('Content-Security-Policy: default-src https:');
        $username = Yii::$app->request->post('username', '');
        $username = base64_decode($username);
        $username = str_replace('seller2018secret', '', $username);
        if ($user = $this->getUser($username)) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
        }
    }

    protected function getUser($username)
    {
        return User::findByUsername($username);
    }

    public function actionGetCats()
    {
        $this->layout = 'empty';
        $id = !empty(Yii::$app->request->get('id')) ? Yii::$app->request->get('id') : null;
        $add = !empty(Yii::$app->request->get('add')) ? Yii::$app->request->get('add') : '';
        $all_cats = Category::find()->with('categories')->with('parent')->where(['parent_id' => $id, 'status' => 1])->orderBy('`order`')->all();
        return $this->render('cat-widget', [
            'all_cats' => $all_cats,
            'add' => $add
        ]);
    }

    public function actionAway($url)
    {
        $banner = BannerTranslation::find()->where(['url' => $url])->one();
        if($banner) {
            $banner_clicks = Yii::$app->session->get('banner_clicks', []);
            if(!in_array($banner->banner->id, $banner_clicks)) {
                $banner_clicks[] = $banner->banner->id;
                Yii::$app->session->set('banner_clicks', $banner_clicks);
                $banner->banner->clicks = $banner->banner->clicks + 1;
                $banner->banner->save();
            }
            return $this->redirect($url);
        }
        return $this->redirect('error');
    }

//    public function actionNewProducts()
//    {
//        echo WProduct::widget(['key' => 'new']);
//    }
//
//    public function actionHits()
//    {
//        echo WProduct::widget(['key' => 'popular']);
//    }


}
