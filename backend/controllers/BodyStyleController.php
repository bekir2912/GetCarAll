<?php

namespace backend\controllers;

use common\models\OptionGroupsTranslation;
use Yii;
use backend\controllers\BehaviorsController;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\models\BodyStyle;
use common\models\BodyTranslations;
use app\models\BodyStyleSearch;
use yii\web\Controller;

/**
 * BodyStyleController implements the CRUD actions for BodyStyle model.
 */
class BodyStyleController extends BehaviorsController
{
    /**
     * @inheritDoc
     */
//    public function behaviors()
//    {
//        return array_merge(
//            parent::behaviors(),
//            [
//                'verbs' => [
//                    'class' => VerbFilter::className(),
//                    'actions' => [
//                        'delete' => ['POST'],
//                    ],
//                ],
//            ]
//        );
//    }

    /**
     * Lists all BodyStyle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BodyStyleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BodyStyle model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new BodyStyle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new BodyStyle();
//
//        if ($this->request->isPost) {
//            if ($model->load($this->request->post()) && $model->save()) {
//                return $this->redirect(['view', 'id' => $model->id]);
//            }
//        } else {
//            $model->loadDefaultValues();
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }
    /**
     * Creates a new OptionGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BodyStyle();
        $info = new BodyTranslations(['scenario' => 'create']);
        $info_uz = new BodyTranslations();
        $info_en = new BodyTranslations();
        $info_oz = new BodyTranslations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $info->body_id = $model->id;
            $info->name = (Yii::$app->request->post('BodyTranslations')['name']['ru'] != '')? Yii::$app->request->post('BodyTranslations')['name']['ru']: '';
            $info->local = 'ru-RU';
            $info->save();

            $info_uz->body_id = $model->id;
            $info_uz->name = (Yii::$app->request->post('BodyTranslations')['name']['uz'] != '')? Yii::$app->request->post('BodyTranslations')['name']['uz']: $info->name;
            $info_uz->local = 'uz-UZ';
            $info_uz->save();

            $info_en->body_id = $model->id;
            $info_en->name = (Yii::$app->request->post('BodyTranslations')['name']['en'] != '')? Yii::$app->request->post('BodyTranslations')['name']['en']: $info->name;
            $info_en->local = 'en-EN';
            $info_en->save();

            $info_oz->body_id = $model->id;
            $info_oz->name = (Yii::$app->request->post('BodyTranslations')['name']['oz'] != '')? Yii::$app->request->post('BodyTranslations')['name']['oz']: $info->name;
            $info_oz->local = 'oz-OZ';
            $info_oz->save();
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'info' => $info,
                'info_uz' => $info_uz,
                'info_oz' => $info_oz,
                'info_en' => $info_en,
            ]);
        }
    }

    /**
     * Updates an existing BodyStyle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }
    /**
     * Updates an existing OptionGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $info = BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'ru-RU']);
        $info->scenario = 'create';
        $info_uz = (!empty(BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'uz-UZ'])))? BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'uz-UZ']): new BodyTranslations();
        $info_en = (!empty(BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'en-EN'])))? BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'en-EN']): new BodyTranslations();
        $info_oz = (!empty(BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'oz-OZ'])))? BodyTranslations::findOne(['body_id' => $model->id, 'local' => 'oz-OZ']): new BodyTranslations();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $copy_id = Yii::$app->request->post('Copy');
            if($copy_id['category_id'] != '0' && $copy_id['category_id'] != $model->category_id) {
                $copy = new BodyStyle($model->getAttributes());
                $copy->id = null;
                $copy->category_id = $copy_id['category_id'];
                $copy->save();
                $translations = !empty($model->bodyTranslations)? $model->bodyTranslations: [];
                if(!empty($translations)) {
                    foreach ($translations as $translation) {
                        $copy_trans = new BodyTranslations($translation->getAttributes());
                        $copy_trans->id = null;
                        $copy_trans->body_id = $copy->id;
                        $copy_trans->save();
                    }
                }
                return $this->redirect(['update', 'id' => $copy->id]);
            }
            $info->body_id = $model->id;
            $info->name = (Yii::$app->request->post('BodyTranslations')['name']['ru'] != '')? Yii::$app->request->post('BodyTranslations')['name']['ru']: '';
            $info->local = 'ru-RU';
            $info->save();

            $info_uz->body_id = $model->id;
            $info_uz->name = (Yii::$app->request->post('OptionGroupsTranslation')['name']['uz'] != '')? Yii::$app->request->post('BodyTranslations')['name']['uz']: $info->name;
            $info_uz->local = 'uz-UZ';
            $info_uz->save();

            $info_en->body_id = $model->id;
            $info_en->name = (Yii::$app->request->post('OptionGroupsTranslation')['name']['en'] != '')? Yii::$app->request->post('BodyTranslations')['name']['en']: $info->name;
            $info_en->local = 'en-EN';
            $info_en->save();

            $info_oz->body_id = $model->id;
            $info_oz->name = (Yii::$app->request->post('OptionGroupsTranslation')['name']['oz'] != '')? Yii::$app->request->post('BodyTranslations')['name']['oz']: $info->name;
            $info_oz->local = 'oz-OZ';
            $info_oz->save();
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'info' => $info,
                'info_uz' => $info_uz,
                'info_oz' => $info_oz,
                'info_en' => $info_en,
            ]);
        }
    }
    /**
     * Deletes an existing BodyStyle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Deletes an existing OptionGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the BodyStyle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BodyStyle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BodyStyle::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
