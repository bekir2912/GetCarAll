<?php

namespace frontend\controllers;

use common\models\News;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class NewsController extends Controller
{
    public function actionList()
    {
        Yii::$app->session->set('root_category', null);
        Yii::$app->session->set('page', 'news-list');
        $query = News::find()->where(['status' => 1])->orderBy('`created_at` DESC');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 12]);
        $news = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('list', [
            'news' => $news,
            'pages' => $pages,
            ]);
    }

    public function actionIndex($id)
    {
        Yii::$app->session->set('root_category', null);
        Yii::$app->session->set('page', 'news');
        $news = News::findOne(['status' => 1, 'url' => $id]);
        if (empty($news)) return $this->redirect(['site/error']);

        return $this->render('index', [
            'news' => $news,
            ]);
    }
}
