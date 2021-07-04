<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\Question;
use common\models\Shop;
use common\models\ShopReview;
use frontend\models\AnswerForm;
use frontend\models\QuestionForm;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class ReviewController extends \yii\web\Controller
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
    public function actionAdd()
    {
        $rating = Yii::$app->request->post('rating');
        $shop_id = Yii::$app->request->post('shop_id');
        $comment = Yii::$app->request->post('comment');
        $shop = Shop::find()->where(['id' => $shop_id, 'status' => 1])->one();
        if ($rating != '' && $comment != '' && $shop) {
            $review = ShopReview::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $shop_id])->one();
            if(!$review) {
                $review = new ShopReview();
                $review->user_id = Yii::$app->user->id;
                $review->shop_id = $shop_id;
                $review->rating = $rating;
                $review->comment = $comment;
                $review->status = 1;
                $review->save();
                $shop->rating = round(ShopReview::find()->where(['status' => 1, 'shop_id' => $shop_id])->average("rating"), 1);
                $shop->save();
            }
        }
        return $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer: Yii::$app->homeUrl);
    }
    public function actionUpdate()
    {
        $rating = Yii::$app->request->post('rating');
        $shop_id = Yii::$app->request->post('shop_id');
        $comment = Yii::$app->request->post('comment');
        $shop = Shop::find()->where(['id' => $shop_id, 'status' => 1])->one();
        if ($rating != '' && $comment != '' && $shop) {
            $review = ShopReview::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $shop_id])->one();
            if($review && Yii::$app->user->id == $review->user_id) {
                $review->rating = $rating;
                $review->is_moderated = 0;
                $review->comment = $comment;
                $review->save();
                $shop->rating = round(ShopReview::find()->where(['status' => 1, 'shop_id' => $shop_id])->average("rating"), 1);
                $shop->save();
            }
        }
        return $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer: Yii::$app->homeUrl);
    }
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $shop_id = Yii::$app->request->get('shop_id');
        $shop = Shop::find()->where(['id' => $shop_id, 'status' => 1])->one();
        if ($id != '' && $shop_id != '' && $shop) {
            $review = ShopReview::find()->where(['user_id' => Yii::$app->user->id, 'shop_id' => $shop_id])->one();
            if($review && Yii::$app->user->id == $review->user_id) {
                $review->delete();
                $shop->rating = round(ShopReview::find()->where(['status' => 1, 'shop_id' => $shop_id])->average("rating"), 1);
                $shop->save();
            }
        }
        return $this->redirect(Yii::$app->request->referrer ? Yii::$app->request->referrer: Yii::$app->homeUrl);
    }
}
