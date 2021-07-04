<?php

namespace frontend\controllers;

use common\models\Brand;
use common\models\Category;
use common\models\Lineup;
use common\models\OptionGroup;
use common\models\OptionValue;
use common\models\Order;
use common\models\Product;
use common\models\Shop;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

class ServiceController extends Controller
{
    public function actionIndex($id, $cat)
    {
        $this->layout = 'service';
        $category = Category::findOne(['status' => 1, 'url' => $cat]);
        if (empty($category)) return $this->redirect(['site/error']);
        if ($category->on_main == 0) return $this->redirect(['site/error']);

        $shop = Shop::findOne(['status' => 1, 'url' => $id]);
        if (empty($shop)) return $this->redirect(['site/error']);

        $unset = false;
        $temp_parent = $category;
        while($temp_parent){
            if (!$temp_parent->status) {
                $unset = true;
                break;
            }
            if(empty($temp_parent->parent)) break;
            $temp_parent = $temp_parent->parent;
        }

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

        $brands_passed = Yii::$app->request->get('brands', []);
        if(is_array($brands_passed)) {
            $brands_passed = array_filter($brands_passed);
        }

        $lineups_passed = Yii::$app->request->get('lineups', []);
        if(is_array($lineups_passed)) {
            $lineups_passed = array_filter($lineups_passed);
        }

        Yii::$app->session->set('root_category', $temp_parent->id);
        Yii::$app->session->set('page', 'service');
        if($unset) return $this->redirect(['site/error']);

        $category_views = (!empty(Yii::$app->session->get('category_views')))? Yii::$app->session->get('category_views'): [];
        if(!in_array($temp_parent->id, $category_views)) {
            $category_views[] = $temp_parent->id;
            Yii::$app->session->set('category_views', $category_views);
            $temp_parent->view++;
            $temp_parent->save();
        }

        $cat_ids = $this->get_ids($category) . $category->id;
        $products = false;
        if (!empty($cat_ids)) {
            $sort['s'] = '`view`';
            $sort['sd'] = 'desc';
            if (Yii::$app->request->get('s') == 'p') $sort['s'] = '`price`';
            if (Yii::$app->request->get('s') == 'd') $sort['s'] = '`created_at`';
            if (Yii::$app->request->get('sd') == 'a') $sort['sd'] = 'asc';
            $products = Product::find()->where(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->orderBy($sort['s'] . " " . $sort['sd'])->all();
        }
        $shops = [];
        $shops_products = [];
        $brands = [];
        $lineups = [];
        $brands_count = [];
        $lineups_count = [];
        $options = [];
        $filtered_products = $products;

        if(Yii::$app->session->get('currency', 'uzs') == 'usd') {
            $def_pStart = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price_usd') ?
                Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price_usd') : 0;
            $def_pEnd = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price_usd') ?
                Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price_usd') : 0;

        } else {
            $def_pStart = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price') ?
                Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price') : 0;
            $def_pEnd = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price') ?
                Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price') : 0;
        }
        $def_kmStart = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('km') ?
            Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('km') : 0;
        $def_kmEnd = Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('km') ?
            Product::find()->andWhere(['status' => 1, 'shop_id' => $shop->id])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('km') : 0;

        $price_range = Yii::$app->request->get('price_range') != '' ? Yii::$app->request->get('price_range') : '';
        $price_range = explode(';', $price_range);
        $pStart = isset($price_range[0])? $price_range[0]: $def_pStart;
        $pEnd = isset($price_range[1])? $price_range[1]: $def_pEnd;

        $kmStart = Yii::$app->request->get('kmstart') != '' ? Yii::$app->request->get('kmstart') : $def_kmStart;
        $kmEnd = Yii::$app->request->get('kmend') != '' ? Yii::$app->request->get('kmend') : $def_kmEnd;


        $filters = array();
        if (!empty(Yii::$app->request->get('filters'))) {
            for ($i = 0; $i < count(Yii::$app->request->get('filters')); $i++) {
                $temp_filter = explode('_', Yii::$app->request->get('filters')[$i]);
                if (count($temp_filter) != 2) continue;
                $filters[$temp_filter[0]][] = $temp_filter[1];
            }
        }
        $delete = array();
        if (!empty($products)) {
            for ($i = 0; $i < count($products); $i++) {
                if(Yii::$app->session->get('currency', 'uzs') == 'usd') {
                    $products[$i]->price = $products[$i]->price_usd;
                    $products[$i]->wholesale = $products[$i]->wholesale_usd;
                }
                if ($products[$i]->shop_id && $products[$i]->shop->status != 1) {
                    if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                    continue;
                }
                if ($products[$i]->user_id && $products[$i]->user->status != 10) {
                    if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                    continue;
                }
                $opt_ids = array();
                if (!empty($filters)) $delete = array();
                for ($k = 0; $k < count($products[$i]->activeOptions); $k++) {
                    $opt = OptionValue::findOne(['id' => $products[$i]->activeOptions[$k]->option_id, 'status' => 1]);
                    $opt_ids[] = $opt->id;
                    if (empty($options['group'][$opt->group_id])) $options['group'][$opt->group_id] = OptionGroup::findOne(['id' => $opt->group_id, 'status' => 1]);
                    if (empty($options['values'][$opt->group_id][$opt->id])) $options['values'][$opt->group_id][$opt->id] = $opt;
                }
                if ($products[$i]->brand_id) {
                    if (empty($brands[$products[$i]->brand_id])) {
                        $brands[$products[$i]->brand_id] = Brand::findOne(['id' => $products[$i]->brand_id, 'status' => 1]);
                    }
                    if(isset($brands_count[$products[$i]->brand_id])) {
                        $brands_count[$products[$i]->brand_id] += 1;
                    } else {
                        $brands_count[$products[$i]->brand_id] = 1;
                    }
                }
                if ($products[$i]->lineup_id) {

                    if (!empty($brands_passed)) {
                        if (in_array($products[$i]->brand_id, $brands_passed)) {
                            if (empty($lineups[$products[$i]->lineup_id])) {
                                $lineups[$products[$i]->lineup_id] = Lineup::findOne(['id' => $products[$i]->lineup_id, 'status' => 1]);
                            }
                            if(isset($lineups_count[$products[$i]->lineup_id])) {
                                $lineups_count[$products[$i]->lineup_id] += 1;
                            } else {
                                $lineups_count[$products[$i]->lineup_id] = 1;
                            }
                        }
                    } else {
                        if (empty($lineups[$products[$i]->lineup_id])) {
                            $lineups[$products[$i]->lineup_id] = Lineup::findOne(['id' => $products[$i]->lineup_id, 'status' => 1]);
                        }
                        if(isset($lineups_count[$products[$i]->lineup_id])) {
                            $lineups_count[$products[$i]->lineup_id] += 1;
                        } else {
                            $lineups_count[$products[$i]->lineup_id] = 1;
                        }
                    }
                }
                if (!empty($filters)) {
                    foreach ($filters as $key => $v) {
                        if (!empty($opt_ids)) {
                            for ($of = 0; $of < count($opt_ids); $of++) {
                                if (in_array($opt_ids[$of], $v)) {
                                    $delete[$key] = 'yes';
                                    break;
                                } else $delete[$key] = 'no';
                            }
                        } else $delete[$key] = 'no';
                    }
                }
                if (!empty($filters)) {
                    if (in_array('no', $delete)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }
                if (!empty($brands_passed)) {
                    if (!in_array($products[$i]->brand_id, $brands_passed)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }
                if (!empty($lineups_passed)) {
                    if (!in_array($products[$i]->lineup_id, $lineups_passed)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }

                if(($products[$i]->sale->value > 0)) {
                    if($products[$i]->sale->type == 0){
                        $prod_price = $products[$i]->price - ($products[$i]->price * $products[$i]->sale->value);
                    }
                    else {
                        $prod_price = $products[$i]->price - $products[$i]->sale->value;
                    }
                }
                else {
                    $prod_price = $products[$i]->price;
                }

                if($prod_price < $def_pStart) $def_pStart = $prod_price;
                $pStart = Yii::$app->request->get('price_range') != '' ? $pStart : $def_pStart;
                if (!(($pStart <= $prod_price) && ($prod_price <= $pEnd))) if (isset($filtered_products[$i])) unset($filtered_products[$i]);

                $kmStart = Yii::$app->request->get('kmstart') != '' ? $kmStart : $def_kmStart;
                if($products[$i]->km < $def_kmStart) $def_kmStart = $products[$i]->km;
                if (!(($kmStart <= $products[$i]->km) && ($products[$i]->km <= $kmEnd))) if (isset($filtered_products[$i])) unset($filtered_products[$i]);


                if($city_id && $products[$i]->shop_id) {
                    if(!in_array($city_id, (json_decode($products[$i]->shop->cities))? json_decode($products[$i]->shop->cities): [])) {
                        if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                        continue;
                    }
                } else if($city_id && $products[$i]->user_id) {
                    if($products[$i]->user->city_id != $city_id) {
                        if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                        continue;
                    }
                }
            }
        }


        $shop_views = (!empty(Yii::$app->session->get('shop_views')))? Yii::$app->session->get('shop_views'): [];
        if(!in_array($shop->id, $shop_views)) {
            $shop_views[] = $shop->id;
            Yii::$app->session->set('shop_views', $shop_views);
            $shop->view++;
            $shop->save();
        }

        foreach ($filtered_products as $product) {
            if($product->shop_id) {
                if (empty($shops[$product->shop_id])) {
                    $shops[$product->shop_id] = Shop::findOne(['id' => $product->shop_id, 'status' => 1]);
                }
                if(isset($shops_products[$product->shop_id])) {
                    $shops_products[$product->shop_id] += 1;
                } else {
                    $shops_products[$product->shop_id] = 1;
                }
            }
        }

        return $this->render('index', ['category' => $category,
            'products' => array_values($filtered_products),
            'shops_products' => $shops_products,
            'options' => $options,
            'shops' => $shops,
            'shop' => $shop,
            'brands' => $brands,
            'lineups' => $lineups,
            'brands_count' => $brands_count,
            'lineups_count' => $lineups_count,
            'pStart' => $pStart,
            'def_pStart' => $def_pStart,
            'def_pEnd' => $def_pEnd,
            'pEnd' => $pEnd,
            'kmStart' => $kmStart,
            'def_kmStart' => $def_kmStart,
            'def_kmEnd' => $def_kmEnd,
            'kmEnd' => $kmEnd,
            'city' => $city,]);
    }

    public function actionList($id)
    {
        $this->layout = 'service';
        $category = Category::findOne(['status' => 1, 'url' => $id]);
        if (empty($category)) return $this->redirect(['site/error']);
        if ($category->on_main == 0) return $this->redirect(['site/error']);

        $unset = false;
        $temp_parent = $category;
        while($temp_parent){
            if (!$temp_parent->status) {
                $unset = true;
                break;
            }
            if(empty($temp_parent->parent)) break;
            $temp_parent = $temp_parent->parent;
        }

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

        $brands_passed = Yii::$app->request->get('brands', []);
        if(is_array($brands_passed)) {
            $brands_passed = array_filter($brands_passed);
        }

        $lineups_passed = Yii::$app->request->get('lineups', []);
        if(is_array($lineups_passed)) {
            $lineups_passed = array_filter($lineups_passed);
        }

        Yii::$app->session->set('root_category', $temp_parent->id);
        Yii::$app->session->set('page', 'services');
        if($unset) return $this->redirect(['site/error']);

        $category_views = (!empty(Yii::$app->session->get('category_views')))? Yii::$app->session->get('category_views'): [];
        if(!in_array($temp_parent->id, $category_views)) {
            $category_views[] = $temp_parent->id;
            Yii::$app->session->set('category_views', $category_views);
            $temp_parent->view++;
            $temp_parent->save();
        }

        $cat_ids = $this->get_ids($category) . $category->id;
        $products = false;
        if (!empty($cat_ids)) {
            $sort['s'] = '`view`';
            $sort['sd'] = 'desc';
            if (Yii::$app->request->get('s') == 'p') $sort['s'] = '`price`';
            if (Yii::$app->request->get('s') == 'd') $sort['s'] = '`created_at`';
            if (Yii::$app->request->get('sd') == 'a') $sort['sd'] = 'asc';
            $products = Product::find()->where(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->orderBy($sort['s'] . " " . $sort['sd'])->all();
        }
        $shops = [];
        $shops_products = [];
        $brands = [];
        $lineups = [];
        $brands_count = [];
        $lineups_count = [];
        $options = [];
        $filtered_products = $products;

        if(Yii::$app->session->get('currency', 'uzs') == 'usd') {
            $def_pStart = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price_usd') ?
                Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price_usd') : 0;
            $def_pEnd = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price_usd') ?
                Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price_usd') : 0;

        } else {
            $def_pStart = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price') ?
                Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('price') : 0;
            $def_pEnd = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price') ?
                Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('price') : 0;
        }
        $def_kmStart = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('km') ?
            Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->min('km') : 0;
        $def_kmEnd = Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('km') ?
            Product::find()->andWhere(['status' => 1])->andWhere(['or', '`category_id` IN (' . $cat_ids . ')', '`add_cats` LIKE "%,' . $category->id . ',%"'])->max('km') : 0;

        $price_range = Yii::$app->request->get('price_range') != '' ? Yii::$app->request->get('price_range') : '';
        $price_range = explode(';', $price_range);
        $pStart = isset($price_range[0])? $price_range[0]: $def_pStart;
        $pEnd = isset($price_range[1])? $price_range[1]: $def_pEnd;

        $kmStart = Yii::$app->request->get('kmstart') != '' ? Yii::$app->request->get('kmstart') : $def_kmStart;
        $kmEnd = Yii::$app->request->get('kmend') != '' ? Yii::$app->request->get('kmend') : $def_kmEnd;


        $filters = array();
        if (!empty(Yii::$app->request->get('filters'))) {
            for ($i = 0; $i < count(Yii::$app->request->get('filters')); $i++) {
                $temp_filter = explode('_', Yii::$app->request->get('filters')[$i]);
                if (count($temp_filter) != 2) continue;
                $filters[$temp_filter[0]][] = $temp_filter[1];
            }
        }
        $delete = array();
        if (!empty($products)) {
            for ($i = 0; $i < count($products); $i++) {
                if(Yii::$app->session->get('currency', 'uzs') == 'usd') {
                    $products[$i]->price = $products[$i]->price_usd;
                    $products[$i]->wholesale = $products[$i]->wholesale_usd;
                }
                if ($products[$i]->shop_id && $products[$i]->shop->status != 1) {
                    if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                    continue;
                }
                if ($products[$i]->user_id && $products[$i]->user->status != 10) {
                    if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                    continue;
                }
                $opt_ids = array();
                if (!empty($filters)) $delete = array();
                for ($k = 0; $k < count($products[$i]->activeOptions); $k++) {
                    $opt = OptionValue::findOne(['id' => $products[$i]->activeOptions[$k]->option_id, 'status' => 1]);
                    $opt_ids[] = $opt->id;
                    if (empty($options['group'][$opt->group_id])) $options['group'][$opt->group_id] = OptionGroup::findOne(['id' => $opt->group_id, 'status' => 1]);
                    if (empty($options['values'][$opt->group_id][$opt->id])) $options['values'][$opt->group_id][$opt->id] = $opt;
                }
                if ($products[$i]->brand_id) {
                    if (empty($brands[$products[$i]->brand_id])) {
                        $brands[$products[$i]->brand_id] = Brand::findOne(['id' => $products[$i]->brand_id, 'status' => 1]);
                    }
                    if(isset($brands_count[$products[$i]->brand_id])) {
                        $brands_count[$products[$i]->brand_id] += 1;
                    } else {
                        $brands_count[$products[$i]->brand_id] = 1;
                    }
                }
                if ($products[$i]->lineup_id) {

                    if (!empty($brands_passed)) {
                        if (in_array($products[$i]->brand_id, $brands_passed)) {
                            if (empty($lineups[$products[$i]->lineup_id])) {
                                $lineups[$products[$i]->lineup_id] = Lineup::findOne(['id' => $products[$i]->lineup_id, 'status' => 1]);
                            }
                            if(isset($lineups_count[$products[$i]->lineup_id])) {
                                $lineups_count[$products[$i]->lineup_id] += 1;
                            } else {
                                $lineups_count[$products[$i]->lineup_id] = 1;
                            }
                        }
                    } else {
                        if (empty($lineups[$products[$i]->lineup_id])) {
                            $lineups[$products[$i]->lineup_id] = Lineup::findOne(['id' => $products[$i]->lineup_id, 'status' => 1]);
                        }
                        if(isset($lineups_count[$products[$i]->lineup_id])) {
                            $lineups_count[$products[$i]->lineup_id] += 1;
                        } else {
                            $lineups_count[$products[$i]->lineup_id] = 1;
                        }
                    }
                }
                if (!empty($filters)) {
                    foreach ($filters as $key => $v) {
                        if (!empty($opt_ids)) {
                            for ($of = 0; $of < count($opt_ids); $of++) {
                                if (in_array($opt_ids[$of], $v)) {
                                    $delete[$key] = 'yes';
                                    break;
                                } else $delete[$key] = 'no';
                            }
                        } else $delete[$key] = 'no';
                    }
                }
                if (!empty($filters)) {
                    if (in_array('no', $delete)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }
                if (!empty($brands_passed)) {
                    if (!in_array($products[$i]->brand_id, $brands_passed)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }
                if (!empty($lineups_passed)) {
                    if (!in_array($products[$i]->lineup_id, $lineups_passed)) if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                }

                if(($products[$i]->sale->value > 0)) {
                    if($products[$i]->sale->type == 0){
                        $prod_price = $products[$i]->price - ($products[$i]->price * $products[$i]->sale->value);
                    }
                    else {
                        $prod_price = $products[$i]->price - $products[$i]->sale->value;
                    }
                }
                else {
                    $prod_price = $products[$i]->price;
                }

                if($prod_price < $def_pStart) $def_pStart = $prod_price;
                $pStart = Yii::$app->request->get('price_range') != '' ? $pStart : $def_pStart;
                if (!(($pStart <= $prod_price) && ($prod_price <= $pEnd))) if (isset($filtered_products[$i])) unset($filtered_products[$i]);

                $kmStart = Yii::$app->request->get('kmstart') != '' ? $kmStart : $def_kmStart;
                if($products[$i]->km < $def_kmStart) $def_kmStart = $products[$i]->km;
                if (!(($kmStart <= $products[$i]->km) && ($products[$i]->km <= $kmEnd))) if (isset($filtered_products[$i])) unset($filtered_products[$i]);


                if($city_id && $products[$i]->shop_id) {
                    if(!in_array($city_id, (json_decode($products[$i]->shop->cities))? json_decode($products[$i]->shop->cities): [])) {
                        if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                        continue;
                    }
                } else if($city_id && $products[$i]->user_id) {
                    if($products[$i]->user->city_id != $city_id) {
                        if (isset($filtered_products[$i])) unset($filtered_products[$i]);
                        continue;
                    }
                }
            }
        }

        foreach ($filtered_products as $product) {
            if($product->shop_id) {
                if (empty($shops[$product->shop_id]))
                {
                    $shops[$product->shop_id] = Shop::findOne(['id' => $product->shop_id, 'status' => 1]);
                }

                if(isset($shops_products[$product->shop_id])) {
                    $shops_products[$product->shop_id] += 1;
                } else {
                    $shops_products[$product->shop_id] = 1;
                }
            }
        }


        $page_count = ceil(count(array_values($shops)) / Yii::$app->params['pageSize']);
        if (Yii::$app->request->get('page') > $page_count) {
            return $this->redirect(['site/error']);
        }
        $page_offset = !empty(Yii::$app->request->get('page')) ? ((Yii::$app->request->get('page') - 1) * Yii::$app->params['pageSize']) : 0;
        if ($page_offset < 0) return $this->redirect(['site/error']);
//        $filtered_shops = array_slice(array_values($shops), $page_offset, Yii::$app->params['pageSize']);
        $filtered_shops = $shops;

        $pagination = new Pagination(['totalCount' => count(array_values($shops)), 'pageSize' => Yii::$app->params['pageSize']]);
        Yii::$app->session->set('found_variants', $pagination->totalCount);

        return $this->render('list', ['category' => $category,
            'filtered_shops' => array_values($filtered_shops),
            'shops_products' => $shops_products,
            'options' => $options,
            'shops' => $shops,
            'pagination' => $pagination,
            'brands' => $brands,
            'lineups' => $lineups,
            'brands_count' => $brands_count,
            'lineups_count' => $lineups_count,
            'pStart' => $pStart,
            'def_pStart' => $def_pStart,
            'def_pEnd' => $def_pEnd,
            'pEnd' => $pEnd,
            'kmStart' => $kmStart,
            'def_kmStart' => $def_kmStart,
            'def_kmEnd' => $def_kmEnd,
            'kmEnd' => $kmEnd,
            'city' => $city,]);
    }

    protected function get_ids($category)
    {
        $sub = false;
        for ($s = 0; $s < count($category->activeCategories); $s++) {
            if ($category->activeCategories[$s]) {
                $sub .= $category->activeCategories[$s]->id . ",";
            }
            $sub .= $this->get_ids($category->activeCategories[$s]);
        }
        return $sub;
    }
}
