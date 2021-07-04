<?php

namespace app\models;
use Yii;
use common\models\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Booking;

/**
 * BookingSearch represents the model behind the search form of `common\models\Booking`.
 */
class BookingSearch extends Booking
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'pickup_shop_id', 'return_shop_id', 'product_id', 'start_date', 'end_date', 'status'], 'integer'],
            [['pickup_location', 'return_location'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Booking::find()->where(['shop_id' => Yii::$app->session->get('shop_id')]);
//        $query->andWhere(['deleted_at' => 0]);
//        $query = Booking::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'pickup_shop_id' => $this->pickup_shop_id,
            'return_shop_id' => $this->return_shop_id,
            'product_id' => $this->product_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'pickup_location', $this->pickup_location])
            ->andFilterWhere(['like', 'return_location', $this->return_location]);

        return $dataProvider;
    }
}
