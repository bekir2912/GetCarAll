<?php
/**
 * Created by ru.lexcorp.
 * User: lexcorp
 * Date: 11.10.2017
 * Time: 4:28
 */

namespace frontend\controllers;

use common\models\Radar;
use Yii;

class MapController extends \yii\web\Controller
{

    public function actionRadar()
    {
        $city = false;
        $city_id = Yii::$app->request->get('city_id') != ''? Yii::$app->request->get('city_id'): Yii::$app->session->get('city_id');
        if($city_id == 'all') {
            Yii::$app->session->set('city_id', false);
            $city_id = false;
        } elseif ($city_id) {
            $city = \common\models\City::find()->where(['status' => 1, 'id' => $city_id])->orderBy('`order` ASC')->one();
            if ($city) {
                Yii::$app->session->set('city_id', $city->id);
            } else {
                Yii::$app->session->set('city_id', false);
            }
        }

        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', 'radars');
        $radars = Radar::find();

        if($city_id) {
            $radars->where(['city_id' => $city_id]);
        }

        return $this->render('index', [
            'radars' => $radars->all(),
            'city' => $city,
        ]);
    }
}