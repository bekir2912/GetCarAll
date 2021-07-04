<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form about `common\models\Product`.
 */
class ProductSearch extends Product
{
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'shop_id', 'user_id', 'category_id', 'brand_id', 'sale_id', 'price', 'warranty', 'interesting', 'interesting_order', 'status', 'created_at', 'updated_at'], 'integer'],
            [['url', 'articul', 'name', 'is_moderated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Product::find()->leftJoin('product_translations', 'product_translations.product_id=products.id AND product_translations.local = "ru-RU"');
        $query->andWhere(['deleted_at' => 0]);
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
            'products.id' => $this->id,
            'shop_id' => $this->shop_id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'sale_id' => $this->sale_id,
            'articul' => $this->articul,
            'price' => $this->price,
            'warranty' => $this->warranty,
            'view' => $this->view,
            'is_moderated' => $this->is_moderated,
            'interesting' => $this->interesting,
            'interesting_order' => $this->interesting_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if($this->status == 2) {
            $query->andFilterWhere([
                'status' => 1,
                'in_order' => 1,
            ]);
        }
        elseif($this->status == 1) {
            $query->andFilterWhere([
                'status' => 1,
                'in_order' => 0,
            ]);
        }
        else {
            $query->andFilterWhere([
                'status' => $this->status,
            ]);
        }

        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['like', 'product_translations.name', $this->name]);

        return $dataProvider;
    }
}
