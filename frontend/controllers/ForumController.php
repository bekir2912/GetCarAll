<?php

namespace frontend\controllers;

use common\models\Answer;
use common\models\Question;
use frontend\models\AnswerForm;
use frontend\models\QuestionForm;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;

class ForumController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add-question', 'delete-theme', 'delete-answer'],
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
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', 'forum-list');
        $q = Yii::$app->request->get('q', '');

        $query = Question::find()->orderBy('`id` DESC');
        if ($q) {
            $query->where(['like', 'question' , $q]);
        }
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $questions = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        Yii::$app->session->set('found_variants', $pages->totalCount);
        return $this->render('index', [
            'questions' => $questions,
            'pages' => $pages,
            'q' => $q,
            ]);
    }

    public function actionShow($id = null)
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', 'forum-item');

        $theme = Question::findOne($id);
        if (!$theme) return $this->redirect(['forum/index']);
        Yii::$app->session->set('found_variants', $theme->question);

        $model = new AnswerForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->addAnswer($id);
            return $this->refresh();
        }

        $query = Answer::find()->where(['question_id' => $theme->id])->orderBy('`id` ASC');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $answers = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();


        if ((!Yii::$app->user->isGuest) && ($theme->user_id == Yii::$app->user->identity->id)) {
            Answer::updateAll(['is_read' => 1], ['question_id' => $theme->id]);
        }

        return $this->render('show', [
            'model' => $model,
            'question' => $theme,
            'answers' => $answers,
            'pages' => $pages,
            ]);
    }

    public function actionAddQuestion()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', 'add-question');
        $model = new QuestionForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($question = $model->addQuestion()) {
                return $this->redirect(['show', 'id' => $question->id]);
            }
        }
        return $this->render('add-question', [
            'model' => $model,
        ]);
    }

    public function actionDeleteTheme()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);

        if (!empty($question_id = Yii::$app->request->post('theme_id'))) {
            $item = Question::findOne(['id' => $question_id, 'user_id' => Yii::$app->user->id]);
            if (empty($item)) return json_encode(['error' => 'empty-item']);
            $item->delete();
            return json_encode(['error' => false]);
        }
        return json_encode(['error' => true]);
    }

    public function actionDeleteAnswer()
    {
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);

        if (!empty($id = Yii::$app->request->post('id'))) {
            $item = Answer::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
            if (empty($item)) return json_encode(['error' => 'empty-item']);
            $item->delete();
            return json_encode(['error' => false]);
        }
        return json_encode(['error' => true]);
    }
}
