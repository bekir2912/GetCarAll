<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 12.10.2017
 * Time: 16:31
 */

namespace api\controllers;


use api\transformers\ChatList;
use api\transformers\MessageList;
use common\components\SmsService;
use common\models\Answer;
use common\models\Chat;
use common\models\FbToken;
use common\models\Order;
use common\models\OrderProduct;
use common\models\Question;
use common\models\Shop;
use common\models\ShopReview;
use common\models\User;
use common\models\UserAddress;
use frontend\models\AddressForm;
use api\models\ProfileForm;
use frontend\models\ReviewForm;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UserController extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {


        $smsService = new SmsService();
        return [
            'authenticator' => [
                'class' => HttpBasicAuth::className(),
                'auth' => function ($username, $password) use ($smsService) {
                    if($smsService->isUzPhone($smsService->clearPhone($username))) {
                        $username = $smsService->clearPhone($username);
                    }
                    $user = User::findByUsername($username);
                    if (!$user) return null;
                    $check = $user->validatePassword($password);
                    return $check? $user: null;
                }
            ]
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'purchase-delete' => ['post'],
//                ],
//            ],
        ];
    }

    public function actionNotifications()
    {
        $data = $this->getMessages('chats');
        if (isset($data['message'])) {
            return $this->redirect(['site/error', 'message' => $data['message'], 'code' => $data['code']]);
        }

        $chatlist = ChatList::transform($data['chats']);
        $chats = [];

        foreach ($chatlist as $item) {
            if($item['new_messages'] > 0) {
                $chats[] = $item;
            }
        }

        $forum = null;
        $questions = Question::find()->where(['user_id' => Yii::$app->user->identity->id])->all();

        if($questions) {
            foreach ($questions as $question) {
                $count = Answer::find()->andWhere(['question_id' => $question->id, 'is_read' => 0])->count('id');
                if($count > 0) {
                    $forum[] = [
                        'id'=> $question->id,
                        'theme'=> $question->question,
                        'new_answers'=> $count,
                    ];
                }
            }
        }

        return $this->asJson([
            'data' => [
                'messages' => $chats,
                'forum' => $forum
            ]
        ]);
    }

    public function actionInfo()
    {
        return $this->asJson([
            'id' => Yii::$app->user->identity->id,
            'name' => Yii::$app->user->identity->name,
            'username' => Yii::$app->user->identity->username,
            'avatar' => Yii::$app->user->identity->avatar? Yii::$app->user->identity->avatar: '/uploads/site/default_shop.png',
//            'phone' => Yii::$app->user->identity->phone,
            'push' => Yii::$app->user->identity->push,
            'balance' => Yii::$app->user->identity->balance,
            'birthday' => Yii::$app->user->identity->birthday,
            'city_id' => Yii::$app->user->identity->city_id,
            'ucard' => Yii::$app->user->identity->ucard,
        ]);
    }

    public function actionUpdate()
    {
        $model = new ProfileForm();

        $model->name = Yii::$app->request->post('name', Yii::$app->user->identity->name);
//        $model->phone = Yii::$app->request->post('phone', Yii::$app->user->identity->phone);
        $model->password = Yii::$app->request->post('password');
        $model->passwordconfirm = Yii::$app->request->post('password_confirm');
        $model->city_id = Yii::$app->request->post('city_id', Yii::$app->user->identity->city_id);
        $model->birthday = Yii::$app->request->post('birthday', Yii::$app->user->identity->birthday);
        $model->ucard = Yii::$app->request->post('ucard', Yii::$app->user->identity->ucard);
        $model->push = Yii::$app->request->post('push', 0);
        $model->img = UploadedFile::getInstanceByName('avatar');

        if (!$model->validate()) {
            Yii::$app->getResponse()->setStatusCode(422);
            return $this->asJson($model->errors);
        }

        if ($user = $model->updateProfile()) {
            return $this->asJson([
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar? $user->avatar: '/uploads/site/default_shop.png',
                'phone' => $user->username,
                'push' => $user->push,
                'balance' => $user->balance,
                'birthday' => $user->birthday,
                'city_id' => (int) $user->city_id,
                'ucard' => $user->ucard,
            ]);
        }
    }

    public function actionReview($id) {
        $review = ShopReview::find()->where(['user_id' => Yii::$app->user->identity->id, 'shop_id' => $id])->one();
        return $this->asJson(['data' => $review]);
    }

    public function actionChats() {
        $data = $this->getMessages('chats');
        if (isset($data['message'])) {
            return $this->redirect(['site/error', 'message' => $data['message'], 'code' => $data['code']]);
        }

        return $this->asJson([
            'data' => ChatList::transform($data['chats']),
        ]);
    }

    public function actionMessages() {
        $data = $this->getMessages('messages');
        if (isset($data['message'])) {
            return $this->redirect(['site/error', 'message' => $data['message'], 'code' => $data['code']]);
        }

        return $this->asJson([
            'data' => MessageList::transform($data['chat'], $data['messages']),
        ]);
    }

    public function getMessages($type)
    {
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
        $chats = Chat::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC')->all();

        $users_chats = Chat::findBySql("
            SELECT *
                FROM (
                    SELECT * FROM `chat`
                    ORDER BY `created_at` DESC
                ) AS sub
                WHERE `shop_id` = '".Yii::$app->user->id."' AND `type` = 'user'
                GROUP BY `user_id`, `type`
        ")->all();
        $users_chats = Chat::find()->where(['shop_id' => Yii::$app->user->id, 'type' => 'user'])->orderBy('`created_at` DESC')->all();

        $ordered_chats = [];
        foreach ($chats as $ch) {
            if(isset($ordered_chats[$ch->user_id.'_'.$ch->shop_id.'_'.$ch->type])) {
                if($ordered_chats[$ch->user_id.'_'.$ch->shop_id.'_'.$ch->type]->created_at < $ch->created_at) {
                    $ordered_chats[$ch->user_id.'_'.$ch->shop_id.'_'.$ch->type] = $ch;
                }
            } else {
                $ordered_chats[$ch->user_id.'_'.$ch->shop_id.'_'.$ch->type] = $ch;
            }
        }

//        $chats = Chat::find()->where(['user_id' => Yii::$app->user->id])
//            ->orderBy('`created_at` DESC')->groupBy('shop_id, type')->all();
//        $users_chats = Chat::find()->where(['shop_id' => Yii::$app->user->id, 'type' => 'user'])
//            ->orderBy('`created_at` DESC')->groupBy('user_id, type')->all();
        foreach ($users_chats as $k => $chat) {
//            $temp_chat =  Chat::find()->where(['user_id' => Yii::$app->user->id, 'type' => 'user', 'shop_id' => $chat->user_id])->one();
//            if($temp_chat) {
//                unset($users_chats[$k]);
//                continue;
//            }
            $temp_user_id = $chat->shop_id;
            $chat->shop_id = $chat->user_id;
            $chat->user_id = $temp_user_id;

            if(isset($ordered_chats[$chat->user_id.'_'.$chat->shop_id.'_'.$chat->type])) {
                if($ordered_chats[$chat->user_id.'_'.$chat->shop_id.'_'.$chat->type]->created_at < $chat->created_at) {
                    $ordered_chats[$chat->user_id.'_'.$chat->shop_id.'_'.$chat->type] = $chat;
                }
            } else {
                $ordered_chats[$chat->user_id.'_'.$chat->shop_id.'_'.$chat->type] = $chat;
            }

        }
//        if($users_chats) {
//            $chats = array_merge($chats, array_values($users_chats));
//        }
        if ($type == 'messages' && !$id) {
            return ['message' => 'incorrect id', 'code' => 422];
        }
        if ($id) {
            $id = base64_decode($id);
            $id = explode(':', $id);
            if (!isset($id[0]) && !isset($id[1])) {
                return ['message' => 'incorrect id', 'code' => 422];
            }
            if($id[0] == 'shop') {
                if (empty(Shop::findOne(['id' => $id[1], 'status' => 1]))) return ['message' => 'incorrect id', 'code' => 422];
                $messages = Chat::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => $id[0]])
                    ->orderBy('`created_at`')->all();
                $active = Chat::findOne(['user_id' => Yii::$app->user->id, 'shop_id' => $id[1], 'type' => $id[0]]);
                Chat::updateAll(['is_read' => 1], ['user_id' => Yii::$app->user->id, 'direction' => 2, 'is_read' => 0, 'shop_id' => $id[1], 'type' => $id[0]]);
            } elseif ($id[0] == 'user') {
                if (empty(User::findOne(['id' => $id[1], 'status' => 10]))) return ['message' => 'incorrect id', 'code' => 422];
                if ($id[1] == Yii::$app->getUser()->identity->id) return ['message' => 'incorrect id', 'code' => 422];

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
//                    $chats = ArrayHelper::merge($chats, [$active]);
            }
        }

        if ($type == 'chats') {
            if (!empty($ordered_chats)) {

                usort($ordered_chats, function ($a, $b) {
                    if ($a->created_at == $b->created_at) {
                        return 0;
                    }
                    return ($a->created_at < $b->created_at) ? 1 : -1;
                });
            }
            
            return ['chats' => $ordered_chats];
        }

        if ($type == 'messages') {
            if($active->shop_id == Yii::$app->user->id) {
                $send_id = $active->user_id;
                $active->user_id = $active->shop_id;
                $active->shop_id = $send_id;
            }

            return ['chat' => $active, 'messages' => $messages];
        }
    }

    public function actionSendMessage()
    {
        $id = !empty(Yii::$app->request->post('im')) ? Yii::$app->request->post('im') : false;

        $message = (!empty(Yii::$app->request->post('message')))? Yii::$app->request->post('message'): false;

        $hasFile = Yii::$app->request->post('hasFile', 0);
        $fileType = Yii::$app->request->post('fileType', null);
        $mime = Yii::$app->request->post('mime', null);
        $geo = Yii::$app->request->post('geo', null);





        if (!$id) return $this->redirect(['site/error', 'message' => 'Invalid id', 'code' => 422]);
        $id_code = $id;
        $id = base64_decode($id);
        $id = explode(':', $id);
        if (!isset($id[0]) && !isset($id[1])) {
            return $this->redirect(['site/error', 'message' => 'Invalid id', 'code' => 422]);
        }
        if (!$geo && !$hasFile && !$message) return $this->redirect(['site/error', 'message' => 'Message cannot be blank', 'code' => 422]);

        if($id[0] == 'shop') {
            if (empty(Shop::findOne(['id' => $id[1], 'status' => 1]))) return $this->redirect(['site/error', 'message' => 'Invalid id', 'code' => 422]);
        } elseif ($id[0] == 'user') {
            if (empty(User::findOne(['id' => $id[1], 'status' => 10]))) return $this->redirect(['site/error', 'message' => 'Invalid id', 'code' => 422]);
            if ($id[1] == Yii::$app->getUser()->identity->id) return $this->redirect(['site/error', 'message' => 'Invalid id', 'code' => 422]);
        }

        $chat = new Chat();

        $chat->user_id = Yii::$app->user->id;
        $chat->shop_id = $id[1];
        $chat->type = $id[0];
        $chat->message = $message? $message: "";
        $chat->hasFile = $hasFile;
        $chat->fileType = $fileType;
        $chat->mime = $mime;
        $chat->geo = $geo;
        $chat->direction = 1;
        $chat->is_read = 0;

        $file = UploadedFile::getInstanceByName('file');
        if($file){
            $dir = (__DIR__).'/../../uploads/users/';
            $path = $file->baseName.'.'.$file->extension;
            if($file->saveAs($dir.$path)) {
                $image_name = uniqid().'.'.$file->extension;
                rename($dir.$path, $dir.$image_name);
//                    $resizer->save($dir.$image_name);
                $chat->file = '/uploads/users/'.$image_name;
                if(file_exists($dir.$path)) unlink($dir.$path);
            }
        }

        $chat->save();

        if($id[0] == 'user') {
            $fbTokens = FbToken::find()->where(['user_id' => $id[1] ])->asArray()->all();

            if($fbTokens) {
                $fbTokens = ArrayHelper::map($fbTokens, 'id', 'token');

                $this->pushNotification('Новое сообщение', 'Кликните что бы прочитать', $fbTokens, 'message', base64_encode('user:'.Yii::$app->user->id));
            }
        }

        return $this->asJson([]);
    }


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