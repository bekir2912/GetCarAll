<?php

namespace frontend\controllers;

use common\models\Product;
use common\models\UserFavorite;
use Yii;
use yii\data\Pagination;

class FavoriteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $category = !empty(Yii::$app->request->get('cat_id')) ? Yii::$app->request->get('cat_id') : false;
        $all_cats = [];
        Yii::$app->session->set('root_category', false);
        Yii::$app->session->set('page', false);
        $products = array();
        $prod_ids = array();

        if (Yii::$app->user->id) {
            $prod_ids = UserFavorite::find()->where(['user_id' => Yii::$app->user->id])->orderBy('`created_at` DESC')->all();
        } elseif (!empty(Yii::$app->session->get('product_ids'))) {
            $ses_prod_ids = array_reverse(Yii::$app->session->get('product_ids'));
            for ($i = 0; $i < count($ses_prod_ids); $i++) {
                $prod_ids[$i] = new \stdClass();
                $prod_ids[$i]->product_id = $ses_prod_ids[$i];
            }
        }
        if (!empty($prod_ids)) {
            for ($i = 0; $i < count($prod_ids); $i++) {
                $temp_prod = Product::findOne(['id' => $prod_ids[$i]->product_id, 'status' => 1]);
                if (Yii::$app->session->get('currency', 'uzs') == 'usd') {
                    $temp_prod->price = $temp_prod->price_usd;
                    $temp_prod->wholesale = $temp_prod->wholesale_usd;
                }
                if (!empty($temp_prod)) {
                    if ($temp_prod->shop_id) {
                        if (!$temp_prod->shop->status) continue;
                    }
                    if ($temp_prod->user_id) {
                        if ($temp_prod->user->status != 10) continue;
                    }
                    if ($temp_prod->category->status == 0) continue;
                    $temp_parent = $temp_prod->category;
                    while ($temp_parent) {
                        if (!$temp_parent->status) {
                            break;
                        }
                        if (empty($temp_parent->parent)) {
                            $all_cats[$temp_parent->id] = $temp_parent;
                            break;
                        }
                        $temp_parent = $temp_parent->parent;
                    }
                    if ($category && $temp_parent->id != $category) {
                        continue;
                    }
                    $products[] = $temp_prod;
                }
            }
        }
        $page_count = ceil(count(array_values($products)) / Yii::$app->params['pageSize']);
        if (Yii::$app->request->get('page') > $page_count) {
            return $this->redirect(['site/error']);
        }
        $page_offset = !empty(Yii::$app->request->get('page')) ? ((Yii::$app->request->get('page') - 1) * Yii::$app->params['pageSize']) : 0;
        if ($page_offset < 0) return $this->redirect(['site/error']);
        $products_slice = array_slice(array_values($products), $page_offset, Yii::$app->params['pageSize']);

        ksort($all_cats);
        return $this->render('index', [
            'products' => array_values($products_slice),
            'filter_cat' => $category,
            'cats' => $all_cats,
            'pagination' => new Pagination(['totalCount' => count(array_values($products)), 'pageSize' => Yii::$app->params['pageSize']]),
        ]);
    }

    public function actionAdd()
    {
        if (!empty(Yii::$app->request->post('id'))) {
            if (Yii::$app->request->post('action') == 'add') {
                if (Yii::$app->user->id) {
                    if (empty(UserFavorite::findOne(['product_id' => Yii::$app->request->post('id'), 'user_id' => Yii::$app->user->id]))) {
                        $userFav = new UserFavorite();
                        $userFav->product_id = Yii::$app->request->post('id');
                        $userFav->user_id = Yii::$app->user->id;
                        $userFav->save();
                        return json_encode(['error' => false]);
                    }
                } else {
                    $prod_ids = Yii::$app->session->get('product_ids', []);

                    if(!in_array(Yii::$app->request->post('id'), $prod_ids)) {
                        $prod_ids[] = Yii::$app->request->post('id');
                        Yii::$app->session->set('product_ids', array_filter(array_unique($prod_ids)));
                    }
                    return json_encode(['error' => false]);
                }
            }
            if (Yii::$app->request->post('action') == 'remove') {
                if (Yii::$app->user->id) {
                    if (!empty(UserFavorite::findOne(['product_id' => Yii::$app->request->post('id'), 'user_id' => Yii::$app->user->id]))) {
                        $userFav = UserFavorite::findOne(['product_id' => Yii::$app->request->post('id'), 'user_id' => Yii::$app->user->id]);
                        $userFav->delete();
                        return json_encode(['error' => false]);
                    }
                } else {
                    $prod_ids = Yii::$app->session->get('product_ids', []);
                    for ($i = 0; $i < count($prod_ids); $i++) {
                        if ($prod_ids[$i] == Yii::$app->request->post('id')) unset($prod_ids[$i]);
                    }
                    if(empty($prod_ids)) {
                        Yii::$app->session->set('product_ids', []);
                    } else {
                        Yii::$app->session->set('product_ids', array_filter(array_unique($prod_ids)));
                    }
                    return json_encode(['error' => false]);
                }
            }
        }
        return json_encode(['error' => true]);
    }
}
