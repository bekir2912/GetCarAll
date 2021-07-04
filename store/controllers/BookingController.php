<?php

namespace store\controllers;
use common\models\Shop;
use common\models\User;
use Yii;

use common\models\Booking;
use app\models\BookingSearch;
use JSONSchedulerConnector;
use PDO;
use yii\base\BaseObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\lib\dhtmlxScheduler\connector\SchedulerDataItem;
use common\lib\dhtmlxScheduler\connector\SchedulerConnector;
use common\lib\dhtmlxScheduler\connector\SchedulerDataProcessor;
use common\lib\dhtmlxScheduler\connector\JSONSchedulerDataItem;
use common\lib\dhtmlxScheduler\Connector\JSONOptionsConnector;
use common\lib\dhtmlxScheduler\Connector\CommonDataProcessor;
/**
 * BookingController implements the CRUD actions for Booking model.
 */
class BookingController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Booking models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new BookingSearch();
//        $dataProvider = $searchModel->search($this->request->queryParams);
//
//
//
//
//
////        require_once('lib/dhtmlxScheduler/connector/scheduler_connector.php');
//
////        require_once(dirname(__FILE__).'/lib/dhtmlxScheduler/connector/db_pdo.php');
//
//        $dsn =  Yii::$app->getDb()->dsn;
//        $username = Yii::$app->getDb()->username;
//        $password = Yii::$app->getDb()->password;
//
//
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    public function actionIndex()
    {
        if(empty(Shop::findOne(Yii::$app->session->get('shop_id')))) return $this->redirect(['booking/index']);
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Booking model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Booking model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Booking();



//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            $model->file = $model->uploadFiles($model, 'file'); //upload file
//            $model->booking-status = 1;
//            $model->booking_cur_date = date('Y-m-d');
//            $model->save();
//            //print_r($model);
//            return $this->redirect(['view', 'id' => $model->booking_id]);
//        }



        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public  function actionSearch(){

    $model = new Booking();


    if ($this->request->isPost) {
        if ($model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    } else {
        $model->loadDefaultValues();
    }


    $q = trim(\Yii::$app->request->get('q'));
    $phone = preg_replace('![^0-9]+!', '', $q);

    $user = User::find()->where(['like', 'phone', $phone])->one();
//        echo '<pre>' . print_r($user, true) . '</pre>';

    return $this->render('create',  compact('user', 'model'));
}

    /**
     * Updates an existing Booking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Booking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
