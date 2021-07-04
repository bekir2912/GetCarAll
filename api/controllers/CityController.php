<?php

namespace api\controllers;

use api\transformers\CityList;
use common\models\City;
use yii\web\Controller;

class CityController extends Controller
{

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionList()
    {
        $cities = null;

        $cities = City::find()->where(['status' => 1])->orderBy('`order` ASC')->all();
        if ($cities) {
            return $this->asJson(['data' => CityList::transform($cities)]);
        } else {
            return $this->redirect(['site/error', 'message' => 'Not Found', 'code' => 404]);
        }
    }
}
