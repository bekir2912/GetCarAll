<?php

namespace store\controllers;

use common\components\SmsService;
use common\models\Seller;
use common\models\User;
use app\models\UserSearch;
use Yii;
use yii\base\BaseObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */

class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
//        $model = User::find()->where(['shop_id' => Yii::$app->session->get('shop_id'), 'status' => 1])->andWhere('`expire_till` > "' . time() . '"')->all();

        $model = new User();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {


                $model = new User(['scenario' => 'create']);

                $smsService = new SmsService();


                $dir = (__DIR__).'/../../uploads/users/';

                $photo = UploadedFile::getInstance($model,'avatar');


                if($photo) {
                    $path = $photo->baseName . '.' . $photo->extension;
                    if ($photo->saveAs($dir . $path)) {
                        $resizer = new SimpleImage();
                        $resizer->load($dir . $path);
                        $resizer->resize(Yii::$app->params['imageSizes']['users']['photo'][0], Yii::$app->params['imageSizes']['users']['photo'][1]);
                        $photo_name = uniqid() . '.' . $photo->extension;
                        $resizer->save($dir . $photo_name);
                        $model->avatar = '/uploads/users/' . $photo_name;
                        if (file_exists($dir . $path)) unlink($dir . $path);
                    }
                }
                else $model->avatar = '/uploads/site/user.png';


                $model->username = $smsService->clearPhone($model->phone);
                if(User::findByUsername($smsService->clearPhone($model->phone))){
                    Yii::$app->session->setFlash("success", "Номер занят");
                    return $this->goBack();
                }
                $model->setPassword($model->password);
                $model->generateAuthKey();
                $model->save();



                Yii::$app->session->setFlash('success', "Клиент {$model->name} добавлен(а)");





                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
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
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
