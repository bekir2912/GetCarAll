<?php

namespace frontend\controllers;

use common\models\OptionGroup;
use common\models\OptionValue;
use common\models\OrderProduct;
use common\models\Product;
use common\models\UserRecent;
use Yii;

class ProductController extends \yii\web\Controller
{

    public function actionIndex($id)
    {
        $this->layout = 'main-fluid';
        $product = Product::findOne(['status' => 1, 'url' => $id]);
        if (empty($product)) return $this->redirect(['site/error']);
        if ($product->shop_id && $product->shop->status != 1) return $this->redirect(['site/error']);
        if ($product->user && $product->user->status != 10) return $this->redirect(['site/error']);
        if ($product->category->on_main == 1) return $this->redirect(['site/error']);

        $unset = false;
        $temp_parent = $product->category;
        while ($temp_parent) {
            if (!$temp_parent->status) {
                $unset = true;
                break;
            }
            if (empty($temp_parent->parent)) {
                break;
            }
            $temp_parent = $temp_parent->parent;
        }

        if ($unset) return $this->redirect(['site/error']);

        Yii::$app->session->set('root_category', $temp_parent->id);
        Yii::$app->session->set('page', 'product');


        $category_views = (!empty(Yii::$app->session->get('category_views')))? Yii::$app->session->get('category_views'): [];
        if(!in_array($temp_parent->id, $category_views)) {
            $category_views[] = $temp_parent->id;
            Yii::$app->session->set('category_views', $category_views);
            $temp_parent->view++;
            $temp_parent->save();
        }

        $options = [];
        for ($k = 0; $k < count($product->activeOptions); $k++) {
            $opt = OptionValue::findOne(['id' => $product->activeOptions[$k]->option_id, 'status' => 1]);
            $opt_ids[] = $opt->id;
            if (empty($options['group'][$opt->group_id])) $options['group'][$opt->group_id] = OptionGroup::findOne(['id' => $opt->group_id, 'status' => 1]);
            if (empty($options['values'][$opt->group_id][$opt->id])) $options['values'][$opt->group_id][$opt->id] = $opt;
            if (empty($options['prices'][$opt->group_id][$opt->id])) $options['prices'][$opt->group_id][$opt->id] = $product->activeOptions[$k]->price;
        }
        if(Yii::$app->user->id) {
            if(empty(UserRecent::findOne(['user_id' => Yii::$app->user->id, 'product_id' => $product->id]))) {
                $product->view++;
                if ($product->shop_id) {
                    $product->shop->view_prods++;
                    $product->shop->save();
                }
            }
            UserRecent::addProduct($product->id);
        }
        else {
            $prod_ids = (!empty(Yii::$app->session->get('product_fav_ids')))? Yii::$app->session->get('product_fav_ids'): [];
            if(!in_array($product->id, $prod_ids)) {
                $product->view++;
                if ($product->shop_id) {
                    $product->shop->view_prods++;
                    $product->shop->save();
                }
            }
            if(($key = array_search($product->id, $prod_ids)) !== false) {
                unset($prod_ids[$key]);
            }
//            if(count($prod_ids) >= Yii::$app->params['recent_count']) {
//                unset($prod_ids[array_keys($prod_ids)[0]]);
//            }
            $prod_ids[] = $product->id;
            Yii::$app->session['product_fav_ids'] = array_values($prod_ids);
        }
        $product->save();
        if(Yii::$app->session->get('currency', 'uzs') == 'usd') {
            $product->price = $product->price_usd;
            $product->wholesale = $product->wholesale_usd;
        }
        return $this->render('index', [
            'product' => $product,
            'category_spec' => $temp_parent->spec,
            'options' => $options,
            'reviews' => OrderProduct::find()->where(['comment_status' => 1, 'product_id' => $product->id])->all(),
        ]);
    }

}