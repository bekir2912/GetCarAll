<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 12.10.2017
 * Time: 16:31
 */

namespace frontend\controllers;


use common\models\Chat;
use common\models\FbToken;
use common\models\Order;
use common\models\OrderProduct;
use common\models\Shop;
use common\models\User;
use common\models\UserAddress;
use frontend\models\AddressForm;
use frontend\models\ProfileForm;
use frontend\models\ReviewForm;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class UserController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'purchase-delete' => ['post'],
//                ],
//            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->session->set('root_category', 'cabinet/index');
        Yii::$app->session->set('page', 'cabinet');
        $model = new ProfileForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->updateProfile()) {
                if (Yii::$app->getUser()->login($user)) {

                    Yii::$app->session->setFlash('success', FA::i('check').' '.Yii::t('frontend', 'Updated'));
                    return $this->redirect(['user/index']);
                }
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionMessages()
    {
        Yii::$app->session->set('root_category', 'cabinet/messages');
        Yii::$app->session->set('page', 'cabinet');
        $id = !empty(Yii::$app->request->get('im')) ? Yii::$app->request->get('im') : false;
        $messages = [];
        $active = [];
        $chats = Chat::findBySql("
            SELECT *
                FROM (
                    SELECT * FROM `chat`
                    ORDER BY `created_at` DESC
                ) AS sub
                WHERE `user_id` = '".Yii::$app->user->id."'
                GROUP BY `shop_id`, `type`
        ")->all();

        $users_chats = Chat::findBySql("
            SELECT *
                FROM (
                    SELECT * FROM `chat`
                    ORDER BY `created_at` DESC
                ) AS sub
                WHERE `shop_id` = '".Yii::$app->user->id."' AND `type` = 'user'
                GROUP BY `user_id`, `type`
        ")->all();

//        $chats = Chat::find()->where(['user_id' => Yii::$app->user->id])
//            ->groupBy('shop_id, type')->orderBy('`created_at` DESC')->all();
//        $users_chats = Chat::find()->where(['shop_id' => Yii::$app->user->id, 'type' => 'user'])
//            ->groupBy('user_id, type')->orderBy('`created_at` DESC')->all();
        foreach ($users_chats as $k => $chat) {
            $temp_chat =  Chat::find()->where(['user_id' => Yii::$app->user->id, 'type' => 'user', 'shop_id' => $chat->user_id])->one();
            if($temp_chat) {
                unset($users_chats[$k]);
                continue;
            }
            $temp_user_id = $chat->shop_id;
            $chat->shop_id = $chat->user_id;
            $chat->user_id = $temp_user_id;
        }
        if($users_chats) {
            $chats = array_merge($chats, array_values($users_chats));
        }
        if ($id) {
            $id = base64_decode($id);
            $id = explode(':', $id);
            if (!isset($id[0]) && !isset($id[1])) {
                $this->redirect(['user/messages']);
            }
            if($id[0] == 'shop') {
                if (empty(Shop::findOne(['id' => $id[1], 'status' => 1]))) return $this->redirect(['user/messages']);
                $messages = Chat::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => $id[0]])
                    ->orderBy('`created_at`')->all();
                $active = Chat::findOne(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => $id[0]]);
                Chat::updateAll(['is_read' => 1], ['user_id' => Yii::$app->user->id, 'direction' => 2, 'is_read' => 0, 'shop_id' => $id[1], 'type' => $id[0]]);
            } elseif ($id[0] == 'user') {
                if (empty(User::findOne(['id' => $id[1], 'status' => 10]))) return $this->redirect(['user/messages']);
                if ($id[1] == Yii::$app->getUser()->identity->id) return $this->redirect(['user/messages']);

                $messages = Chat::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => 'user'])
                    ->orWhere(['user_id' => $id[1], 'shop_id' => Yii::$app->user->id, 'type' => 'user'])
                    ->orderBy('`created_at`')->all();
                $active = Chat::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => 'user'])
                    ->orWhere(['user_id' => $id[1], 'shop_id' => Yii::$app->user->id, 'type' => 'user'])->one();
                Chat::updateAll(['is_read' => 1], ['user_id' => $id[1], 'shop_id' => Yii::$app->user->id, 'is_read' => 0, 'type' => 'user']);
            }

            if (empty($active)) {
                    $active = new Chat();
                    $active->user_id = Yii::$app->user->id;
                    $active->shop_id = $id[1];
                    $active->type = $id[0];
                    $active->direction = 1;
                    $active->is_read = 0;
                    $chats = ArrayHelper::merge($chats, [$active]);
            }
        }

        if (!empty($chats)) {
            usort($chats, function ($a, $b) {
                if ($a->created_at == $b->created_at) {
                    return 0;
                }
                return ($a->created_at < $b->created_at) ? 1 : -1;
            });
        }

        return $this->render('messages', [
            'messages' => $messages,
            'chats' => $chats,
            'active' => $active,
        ]);
    }

    public function actionSendMessage()
    {
        Yii::$app->session->set('root_category', 'cabinet/messages');
        Yii::$app->session->set('page', 'cabinet');
        $id = !empty(Yii::$app->request->post('chat_id')) ? Yii::$app->request->post('chat_id') : false;

        $message = (!empty(Yii::$app->request->post('message')))? Yii::$app->request->post('message'): false;
        if (!$id) return $this->redirect(['user/messages']);
        $id_code = $id;
        $id = base64_decode($id);
        $id = explode(':', $id);
        if (!isset($id[0]) && !isset($id[1])) {
            $this->redirect(['user/messages']);
        }
        if (!$message) return $this->redirect(['user/messages']);

        if($id[0] == 'shop') {
            if (empty(Shop::findOne(['id' => $id[1], 'status' => 1]))) return $this->redirect(['user/messages']);
        } elseif ($id[0] == 'user') {
            if (empty(User::findOne(['id' => $id[1], 'status' => 10]))) return $this->redirect(['user/messages']);
            if ($id[1] == Yii::$app->getUser()->identity->id) return $this->redirect(['user/messages']);
        }

        $chat = new Chat();
        $chat->user_id = Yii::$app->user->id;
        $chat->shop_id = $id[1];
        $chat->type = $id[0];
        $chat->message = $message? $message: "";
        $chat->hasFile = 0;
        $chat->fileType = null;
        $chat->mime = null;
        $chat->geo = null;
        $chat->direction = 1;
        $chat->is_read = 0;
        $chat->save();

        if($id[0] == 'user') {
            $fbTokens = FbToken::find()->where(['user_id' => $id[1] ])->asArray()->all();

            if($fbTokens) {
                $fbTokens = ArrayHelper::map($fbTokens, 'id', 'token');

                $this->pushNotification('Новое сообщение', 'Кликните что бы прочитать', $fbTokens, 'message', base64_encode('user:'.Yii::$app->user->id));
            }
        }

        return $this->redirect(['user/messages', 'im' => $id_code]);
    }

//    public function actionAddresses()
//    {
//        Yii::$app->session->set('active_category', '');
//        $model = new AddressForm();
//        if ($model->load(Yii::$app->request->post())) {
//            if ($address = $model->saveAddress()) {
//                return $this->redirect(['user/addresses']);
//            }
//        }
//        $addresses = UserAddress::find()->where(['user_id' => Yii::$app->user->id])->all();
//
//        return $this->render('addresses', [
//            'model' => $model,
//            'addresses' => $addresses,
//        ]);
//    }
//
//    public function actionPurchases()
//    {
//        Yii::$app->session->set('active_category', '');
//        $statuses = [
//            ['Check availability', 'info'],
//            ['Order canceled', 'danger'],
//            ['Waiting for payment', 'info'],
//            ['You can pickup goods', 'success'],
//        ];
//        $query = Order::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC');
//        $countQuery = clone $query;
//        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
//        $orders = $query->offset($pages->offset)
//            ->limit($pages->limit)
//            ->all();
//        return $this->render('purchases', [
//            'orders' => $orders,
//            'pages' => $pages,
//            'statuses' => $statuses
//        ]);
//    }
//
//    public function actionReviews()
//    {
//        Yii::$app->session->set('active_category', '');
//        $orders = Order::find()->where(['user_id' => Yii::$app->user->id])->andWhere('`comment_rate` > 0')->orderBy('`created_at` DESC')->all();
//        $products = [];
//        $temp_products = [];
//        $user_oreders = Order::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC')->all();
//        for ($i = 0; $i < count($user_oreders); $i++) {
//            $temp_products[] = OrderProduct::find()->where(['order_id' => $user_oreders[$i]->id])->andWhere('`comment_rate` > 0')->orderBy('`created_at` DESC')->all();
//        }
//        if (!empty($temp_products)) {
//            for ($i = 0; $i < count($temp_products); $i++) {
//
//                $products = ArrayHelper::merge($products, $temp_products[$i]);
//            }
//        }
//
//        return $this->render('reviews', [
//            'orders' => $orders,
//            'products' => $products,
//        ]);
//    }
//
//    public function actionReview()
//    {
//        Yii::$app->session->set('active_category', '');
//
//        $model = new ReviewForm();
//        if ($model->load(Yii::$app->request->post())) {
//            if ($model->updateReview()) {
//                return $this->redirect(['user/reviews']);
//            }
//        }
//        $order = [];
//        $product = [];
//        if (empty($type = Yii::$app->request->get('type')) || empty($id = Yii::$app->request->get('id'))) return $this->redirect(['user/reviews']);
//        if ($type == 'shop') {
//            if (empty($order = Order::findOne(['id' => $id, 'user_id' => Yii::$app->user->id, 'status' => 1]))) return $this->redirect(['user/reviews']);
//            if ($order->comment_status == 1) return $this->redirect(['user/reviews']);
//        } elseif ($type == 'product') {
//            if (empty($product = OrderProduct::findOne(['id' => $id]))) return $this->redirect(['user/reviews']);
//            if ($product->comment_status == 1) return $this->redirect(['user/reviews']);
//            if ($product->order->status == 0) return $this->redirect(['user/reviews']);
//            if ($product->order->user_id != Yii::$app->user->id) return $this->redirect(['user/reviews']);
//        } else {
//            return $this->redirect(['user/reviews']);
//        }
//        $model->type = $type;
//        $model->id = $id;
//        $model->review = !empty($order) ? $order->comment : $product->comment;
//        $model->rate = !empty($order) ? $order->comment_rate : $product->comment_rate;
//
//
//        return $this->render('review', [
//            'model' => $model,
//            'order' => $order,
//            'product' => $product,
//        ]);
//    }
//
//    public function actionPurchase($id)
//    {
//        Yii::$app->session->set('active_category', '');
//        $order = Order::findOne(['user_id' => Yii::$app->user->id, 'id' => $id]);
//        if (empty($order)) return $this->redirect(['site/error']);
//        $statuses = [
//            ['Check availability', 'info'],
//            ['Order canceled', 'danger'],
//            ['Waiting for payment', 'info'],
//            ['You can pickup goods', 'success'],
//        ];
//        return $this->render('purchase', [
//            'order' => $order,
//            'statuses' => $statuses
//        ]);
//    }
//
//    public function actionPurchaseDelete()
//    {
//        if ($id = Yii::$app->request->post('id')) {
//            if ($order = Order::findOne(['user_id' => Yii::$app->user->id, 'id' => $id]))
//                if ($order->getCanCancel()) {
//                    $order->status = '-1';
//                    $order->save();
//                }
//            return $this->redirect(['user/purchases']);
//        }
//        return $this->goBack();
//    }
//
//    public function actionRemoveAdd()
//    {
//        if ($id = Yii::$app->request->post('id')) {
//            if ($add = UserAddress::findOne(['user_id' => Yii::$app->user->id, 'id' => $id]))
//                if ($add->delete()) return json_encode(['error' => false]);
//        }
//        return json_encode(['error' => true]);
//    }

    protected function pushNotification($title, $msg, $tokens = array(), $type, $news_id = null) {
        $note = Yii::$app->fcm->createNotification($title, $msg);

        $chunk = array_chunk($tokens, 1000);

        foreach ($chunk as $v) {
            $message = Yii::$app->fcm->createMessage($v);

            $message->setNotification($note)->setData(['message'=>$msg, 'title'=>$title, 'type'=>$type, 'im'=>$news_id, 'date'=>time()]);

            Yii::$app->fcm->send($message);
        }

        return true;
    }
}