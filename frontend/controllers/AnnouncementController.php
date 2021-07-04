<?php

namespace frontend\controllers;

use common\components\Helper;
use common\components\SimpleImage;
use common\models\Brand;
use common\models\Category;
use common\models\Lineup;
use common\models\ProductImages;
use common\models\ProductOption;
use common\models\ProductPerformance;
use common\models\ProductPerformanceTranslation;
use common\models\ProductTranslation;
use common\models\Shop;
use common\models\User;
use rmrevin\yii\fontawesome\FA;
use Yii;
use common\models\Product;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * AnnouncementController implements the CRUD actions for Product model.
 */
class AnnouncementController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'purchase-delete' => ['post'],
//                ],
//            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */


    protected function getIds($category)
    {
        $ids = ArrayHelper::map(ArrayHelper::toArray($category->activeCategories), 'id', 'user_id');
        return array_keys($ids);
    }

    public function actionIndex()
    {
        Yii::$app->session->set('root_category', 'cabinet/announce');
        Yii::$app->session->set('page', 'cabinet');

        $category = !empty(Yii::$app->request->get('cat_id')) ? Yii::$app->request->get('cat_id') : false;
        if (empty($category = Category::findOne(['id' => $category, 'status' => 1, 'parent_id' => null]))) $category = false;

        $query = Product::find()->where(['user_id' => Yii::$app->getUser()->identity->id])->orderBy('`created_at` DESC');

        if ($category->on_main == 1) return $this->redirect(['site/error']);
        if ($category) {
            $temp_category = $category;
            $ids = [$category->id];

            if ($temp_category->activeCategories) {
                $ids = array_merge($ids, $this->getIds($temp_category));
                foreach ($temp_category->activeCategories as $activeCategory) {
                    $ids = array_merge($ids, $this->getIds($activeCategory));
                }
            }
            $query->andWhere(['in', 'category_id', $ids]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $announces = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'announces' => $announces,
            'pagination' => $pagination,
            'filter_cat' => $category,
        ]);
    }
//
//    /**
//     * Displays a single Product model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->session->set('root_category', 'cabinet/announce');
        Yii::$app->session->set('page', 'cabinet');
        $category = !empty(Yii::$app->request->get('category')) ? Yii::$app->request->get('category') : false;

        if (empty($category = Category::findOne(['id' => $category, 'status' => 1]))) $category = false;
        if (!empty($category)) if (!empty($category->activeCategories)) $category = false;
        $unset = false;
        $temp_parent = $category;
        while ($temp_parent) {
            if (!$temp_parent->status) {
                $unset = true;
                break;
            }
            if (empty($temp_parent->parent)) break;
            $temp_parent = $temp_parent->parent;
        }

        if ($unset) $category = false;

        if ($category) {
            if ($category->on_main == 1) return $this->redirect(['site/error']);
            $all_brands = Brand::find()->where(['status' => 1, 'category_id' => $category->id])->all();
            $brand = false;
            if (!empty($all_brands)) {
                $brand = !empty(Yii::$app->request->get('brand')) ? Yii::$app->request->get('brand') : false;

                if (empty($brand = Brand::findOne(['id' => $brand, 'status' => 1]))) $brand = false;
            }
            $lineup = false;
            if ($brand) {
                $all_lineups = Lineup::find()->where(['status' => 1, 'brand_id' => $brand->id])->all();
                if (!empty($all_lineups)) {
                    $lineup = !empty(Yii::$app->request->get('lineup')) ? Yii::$app->request->get('lineup') : false;

                    if (empty($lineup = Lineup::findOne(['id' => $lineup, 'status' => 1]))) $lineup = false;
                }
            }
        }

        $model = new Product();
        $info = new ProductTranslation(['scenario' => 'create']);
        $info_uz = new ProductTranslation();
        $info_en = new ProductTranslation();
        $info_oz = new ProductTranslation();
        if($category->id == 1 && (isset($brand) && $brand) && (isset($lineup) && $lineup)) {
            $info->name = "-=GTA=-";
        }



        if ($model->load(Yii::$app->request->post())) {

            $model->price = isset(Yii::$app->request->post('Product')['price'])? Yii::$app->request->post('Product')['price']: 0;
            $model->km = isset(Yii::$app->request->post('Product')['km'])? Yii::$app->request->post('Product')['km']: 0;


            $model->price = str_replace(' ', '', $model->price);
            $model->km = str_replace(' ', '', $model->km);
            $model->km = $model->km < 0? 0: $model->km;

            if($model->validate()) {
                $model->shop_id = null;
                $model->user_id = Yii::$app->getUser()->identity->id;
                $model->sale_id = ($model->sale_id > 0) ? $model->sale_id : null;
                $model->articul = uniqid();
                if ($model->status == 2) {
                    $model->in_order = 1;
                    $model->status = 1;
                } else {
                    $model->in_order = 0;
                }
                if (!empty($def_price = Yii::$app->request->post('def_price')) && $def_price == 1) {
                    $model->wholesale = json_encode([]);
                    $model->price = "0";
                    $model->price_usd = 0;
                    $model->wholesale_usd = 0;
                } else {
                    if ($model->price_type == 0) {
                        $model->wholesale = json_encode([]);
                        $model->wholesale_usd = json_encode([]);

                        if($model->currency == 'uzs') {
                            $model->price_usd = round($model->price / Yii::$app->params['currency']);
                        } else if($model->currency == 'usd'){
                            $model->price_usd = $model->price;
                            $model->price = round($model->price * Yii::$app->params['currency']);
                        }
                    }
                    if ($model->price_type == 1) {
                        $model->price = "0";
                        $model->price_usd = 0;
                        $wholesale = $model->wholesale;
                        $wholesales = [];
                        $wholesales_usd = [];
                        if (!empty($wholesale)) {
                            foreach ($wholesale['price'] as $it => $item) {
                                if ($item !== '' && $wholesale['count'][$it] !== '') {
                                    if($model->currency == 'uzs') {
                                        $wholesales[$wholesale['count'][$it]] = $item;
                                        $wholesales_usd[$wholesale['count'][$it]] = round($item / Yii::$app->params['currency']);
                                    } else if($model->currency == 'usd'){
                                        $wholesales_usd[$wholesale['count'][$it]] = $item;
                                        $wholesales[$wholesale['count'][$it]] = round($item * Yii::$app->params['currency']);
                                    }
                                }
                            }
                        }
                        ksort($wholesales);
                        ksort($wholesales_usd);
                        $model->wholesale = json_encode($wholesales);
                        $model->wholesale_usd = json_encode($wholesales_usd);
                    }
                    if ($model->price_type == 2) {

                        if($model->currency == 'uzs') {
                            $model->price_usd = round($model->price / Yii::$app->params['currency']);
                        } else if($model->currency == 'usd'){
                            $model->price_usd = $model->price;
                            $model->price = round($model->price * Yii::$app->params['currency']);
                        }

                        $wholesale = $model->wholesale;
                        $wholesales = [];
                        $wholesales_usd = [];
                        if (!empty($wholesale)) {
                            foreach ($wholesale['price'] as $it => $item) {
                                if ($item !== '' && $wholesale['count'][$it] !== '') {
                                    if($model->currency == 'uzs') {
                                        $wholesales[$wholesale['count'][$it]] = $item;
                                        $wholesales_usd[$wholesale['count'][$it]] = round($item / Yii::$app->params['currency']);
                                    } else if($model->currency == 'usd'){
                                        $wholesales_usd[$wholesale['count'][$it]] = $item;
                                        $wholesales[$wholesale['count'][$it]] = round($item * Yii::$app->params['currency']);
                                    }
                                }
                            }
                        }
                        ksort($wholesales);
                        ksort($wholesales_usd);
                        $model->wholesale = json_encode($wholesales);
                        $model->wholesale_usd = json_encode($wholesales_usd);
                    }
                }
                $model->wholesale_unit_id = 0;
                $model->url = '_' . uniqid();

                $cats = !empty(Yii::$app->request->post('add_cats')) ? Yii::$app->request->post('add_cats') : [];
                $model->add_cats = ',' . implode(',', array_values(array_unique($cats))) . ',';
                $model->add_cats_titles = json_encode(Yii::$app->request->post('add_cats_titles'));


                $model->price = (string) $model->price;
                if ($model->save()) {
                    $info->product_id = $model->id;

                    if($category->id == 1 && (isset($brand) && $brand) && (isset($lineup) && $lineup)) {
                        $info->name = $lineup->translate->name;
                    } else {
                        $info->name = (Yii::$app->request->post('ProductTranslation')['name']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['name']['ru']: $category->translate->name;
                    }

                    $info->description = (Yii::$app->request->post('ProductTranslation')['description']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['description']['ru']: '';
                    $info->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['ru']: '';
                    $info->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['ru']: '';
                    $info->local = 'ru-RU';
                    $info->save();

                    $info_uz->product_id = $model->id;
                    $info_uz->name = (Yii::$app->request->post('ProductTranslation')['name']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['name']['uz']: $info->name;
                    $info_uz->description = (Yii::$app->request->post('ProductTranslation')['description']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['description']['uz']: $info->description;
                    $info_uz->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['uz']: $info->warranty;
                    $info_uz->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['uz']: $info->delivery;
                    $info_uz->local = 'uz-UZ';
                    $info_uz->save();


                    $info_en->product_id = $model->id;
                    $info_en->name = (Yii::$app->request->post('ProductTranslation')['name']['en'] != '')? Yii::$app->request->post('ProductTranslation')['name']['en']: $info->name;
                    $info_en->description = (Yii::$app->request->post('ProductTranslation')['description']['en'] != '')? Yii::$app->request->post('ProductTranslation')['description']['en']: $info->description;
                    $info_en->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['en'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['en']: $info->warranty;
                    $info_en->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['en'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['en']: $info->delivery;
                    $info_en->local = 'en-EN';
                    $info_en->save();

                    $info_oz->product_id = $model->id;
                    $info_oz->name = (Yii::$app->request->post('ProductTranslation')['name']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['name']['oz']: $info->name;
                    $info_oz->description = (Yii::$app->request->post('ProductTranslation')['description']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['description']['oz']: $info->description;
                    $info_oz->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['oz']: $info->warranty;
                    $info_oz->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['oz']: $info->delivery;
                    $info_oz->local = 'oz-OZ';
                    $info_oz->save();

                    $model->url = Helper::toLatin($info->name) . '_' . $model->id;

                    $model->price = (string) $model->price;
                    $model->save();

                    $image = UploadedFile::getInstanceByName('mainImage');
//                if(!$image) {
//                    Yii::$app->session->setFlash('error', FA::i('warning').' Фото обязательно');
//                    return $this->goBack();
//                }
                    $dir = (__DIR__) . '/../../uploads/products/';
                    if ($image) {
                        $image_model = new ProductImages();
                        $image_model->product_id = $model->id;
                        $image_model->main = 1;
                        $path = $image->baseName . '.' . $image->extension;
                        if ($image->saveAs($dir . $path)) {
                            $resizer = new SimpleImage();
                            $resizer->load($dir . $path);
                            $resizer->resize(Yii::$app->params['imageSizes']['products']['image'][0], Yii::$app->params['imageSizes']['products']['image'][1]);
                            $image_name = uniqid() . '.' . $image->extension;
                            $resizer->save($dir . $image_name);
                            $image_model->image = '/uploads/products/' . $image_name;
                            if (is_file($dir . $path)) if (file_exists($dir . $path)) unlink($dir . $path);

                            $image_model->save();
                        } else {
                            Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                            return $this->goBack();
                        }
                    } else {
                        $image_model = new ProductImages();
                        $image_model->product_id = $model->id;
                        $image_model->main = 1;
                        $image_model->image = '/uploads/site/default_product.png';
                        $image_model->save();
                    }

                    $images = UploadedFile::getInstancesByName('images');
                    if (!empty($images)) {
                        foreach ($images as $image) {
                            $image_model = new ProductImages();
                            $image_model->product_id = $model->id;
                            $image_model->main = 0;
                            $path = $image->baseName . '.' . $image->extension;
                            if ($image->saveAs($dir . $path)) {
                                $resizer = new SimpleImage();
                                $resizer->load($dir . $path);
                                $resizer->resize(Yii::$app->params['imageSizes']['products']['image'][0], Yii::$app->params['imageSizes']['products']['image'][1]);
                                $image_name = uniqid() . '.' . $image->extension;
                                $resizer->save($dir . $image_name);
                                $image_model->image = '/uploads/products/' . $image_name;
                                if (is_file($dir . $path)) if (file_exists($dir . $path)) unlink($dir . $path);

                                $image_model->save();
                            } else {
                                Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                                return $this->goBack();
                            }
                        }

                    }
                    $model_custom_options = [];
                    $custom_options = Yii::$app->request->post('custom_options', []);
                    if (!empty($options = Yii::$app->request->post('options'))) {
                        foreach ($options as $group_id => $option) {
                            if (!isset($option['id'])) continue;
                            if ($option['id'] == -1) {
                                $model_custom_options[$group_id] = isset($custom_options[$group_id])? $custom_options[$group_id]: '';
                                continue;
                            }
                            $opt = new ProductOption();
                            $opt->product_id = $model->id;
                            $opt->option_id = $option['id'];
                            $opt->price = 0;
                            $opt->save();
                        }
                    }
                    $model->custom_options = json_encode($model_custom_options);

                    $model->price = (string) $model->price;
                    $model->save();

                    if (!empty($performances = Yii::$app->request->post('Preformance'))) {
                        foreach ($performances['ru']['name'] as $ind => $name) {
                            if (trim($name) == '' && trim($performances['ru']['desc'][$ind]) == '') continue;
                            $perf = new ProductPerformance();
                            $perf->product_id = $model->id;
                            $perf->save();
                            $perf_ru = new ProductPerformanceTranslation();
                            $perf_uz = new ProductPerformanceTranslation();
                            $perf_en = new ProductPerformanceTranslation();
                            $perf_ru->product_performance_id = $perf->id;
                            $perf_ru->name = $name;
                            $perf_ru->description = $performances['ru']['desc'][$ind];
                            $perf_ru->local = 'ru-RU';
                            $perf_ru->save();
                            $perf_uz->product_performance_id = $perf->id;
                            $perf_uz->name = ($performances['uz']['name'][$ind]) ? $performances['uz']['name'][$ind] : $name;
                            $perf_uz->description = ($performances['uz']['desc'][$ind]) ? $performances['uz']['desc'][$ind] : $performances['ru']['desc'][$ind];
                            $perf_uz->local = 'uz-UZ';
                            $perf_uz->save();
                            $perf_en->product_performance_id = $perf->id;
                            $perf_en->name = ($performances['en']['name'][$ind]) ? $performances['en']['name'][$ind] : $name;
                            $perf_en->description = ($performances['en']['desc'][$ind]) ? $performances['en']['desc'][$ind] : $performances['ru']['desc'][$ind];
                            $perf_en->local = 'en-EN';
                            $perf_en->save();
                        }
                    }

                    Yii::$app->session->setFlash('success', FA::i('check').' '.Yii::t('frontend', 'Saved'));
                    return $this->redirect(['update', 'id' => $model->id, 'category' => $model->category_id, 'brand' => $model->brand_id, 'lineup' => $model->lineup_id]);
                } else {
                    Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                    return $this->goBack();
                }
            }

        } else {
            return $this->render('create', [
                'model' => $model,
                'category' => $category,
                'brand' => $brand,
                'lineup' => $lineup,
                'info' => $info,
                'info_uz' => $info_uz,
                'info_oz' => $info_oz,
                'info_en' => $info_en,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Yii::$app->session->set('root_category', 'cabinet/announce');
        Yii::$app->session->set('page', 'cabinet');
        $model = $this->findModel($id);
        if ($model->in_order == 1 && $model->status != -1) {
            $model->status = 2;
        }
        $old_cat = $model->category_id;
        $unset_opt = false;
        $status = $model->status;
        $category = !empty(Yii::$app->request->get('category')) ? Yii::$app->request->get('category') : false;

        if (empty($category = Category::findOne(['id' => $category]))) $category = false;
        if (!empty($category)) if (!empty($category->activeCategories)) $category = false;

        if ($category) {
            if ($category->on_main == 1) return $this->redirect(['site/error']);
            $all_brands = Brand::find()->where(['status' => 1, 'category_id' => $category->id])->all();
            $brand = false;
            if (!empty($all_brands)) {
                $brand = !empty(Yii::$app->request->get('brand')) ? Yii::$app->request->get('brand') : false;

                if (empty($brand = Brand::findOne(['id' => $brand, 'status' => 1]))) $brand = false;
            }
            $lineup = false;
            if ($brand) {
                $all_lineups = Lineup::find()->where(['status' => 1, 'brand_id' => $brand->id])->all();
                if (!empty($all_lineups)) {
                    $lineup = !empty(Yii::$app->request->get('lineup')) ? Yii::$app->request->get('lineup') : false;

                    if (empty($lineup = Lineup::findOne(['id' => $lineup, 'status' => 1]))) $lineup = false;
                }
            }
        }

        $info = ProductTranslation::findOne((['product_id' => $model->id, 'local' => 'ru-RU']));
        $info->scenario = 'create';

        if($category->id == 1 && (isset($brand) && $brand) && (isset($lineup) && $lineup)) {
            $info->name = "-=GTA=-";
        }
        $info_uz = ProductTranslation::findOne((['product_id' => $model->id, 'local' => 'uz-UZ']));
        if(!$info_uz) $info_uz = new ProductTranslation();
        $info_en = ProductTranslation::findOne((['product_id' => $model->id, 'local' => 'en-EN']));
        if(!$info_en) $info_en = new ProductTranslation();
        $info_oz = ProductTranslation::findOne((['product_id' => $model->id, 'local' => 'oz-OZ']));
        if(!$info_oz) $info_oz = new ProductTranslation();




        if ($model->load(Yii::$app->request->post())) {

            $model->price = isset(Yii::$app->request->post('Product')['price'])? Yii::$app->request->post('Product')['price']: $model->price;
            $model->km = isset(Yii::$app->request->post('Product')['km'])? Yii::$app->request->post('Product')['km']: $model->km;

            $model->price = str_replace(' ', '', $model->price);
            $model->km = str_replace(' ', '', $model->km);
            $model->km = $model->km < 0? 0: $model->km;
            if ($model->validate()) {
                if (Yii::$app->request->post('Product')['category_id'] != $old_cat) $unset_opt = true;
                $model->shop_id = null;


                $model->sale_id = ($model->sale_id > 0) ? $model->sale_id : null;
                if ($model->status == 2) {
                    $model->in_order = 1;
                    $model->status = 1;
                } else {
                    $model->in_order = 0;
                }
                if (!empty($def_price = Yii::$app->request->post('def_price')) && $def_price == 1) {
                    $model->wholesale = json_encode([]);
                    $model->price = "0";
                    $model->price_usd = 0;
                    $model->wholesale_usd = 0;
                } else {
                    if ($model->price_type == 0) {
                        $model->wholesale = json_encode([]);
                        $model->wholesale_usd = json_encode([]);

                        if($model->currency == 'uzs') {
                            $model->price_usd = round($model->price / Yii::$app->params['currency']);
                        } else if($model->currency == 'usd'){
                            $model->price_usd = $model->price;
                            $model->price = round($model->price * Yii::$app->params['currency']);
                        }
                    }
                    if ($model->price_type == 1) {
                        $model->price = "0";
                        $model->price_usd = 0;
                        $wholesale = $model->wholesale;
                        $wholesales = [];
                        $wholesales_usd = [];
                        if (!empty($wholesale)) {
                            foreach ($wholesale['price'] as $it => $item) {
                                if ($item !== '' && $wholesale['count'][$it] !== '') {
                                    if($model->currency == 'uzs') {
                                        $wholesales[$wholesale['count'][$it]] = $item;
                                        $wholesales_usd[$wholesale['count'][$it]] = round($item / Yii::$app->params['currency']);
                                    } else if($model->currency == 'usd'){
                                        $wholesales_usd[$wholesale['count'][$it]] = $item;
                                        $wholesales[$wholesale['count'][$it]] = round($item * Yii::$app->params['currency']);
                                    }
                                }
                            }
                        }
                        ksort($wholesales);
                        ksort($wholesales_usd);
                        $model->wholesale = json_encode($wholesales);
                        $model->wholesale_usd = json_encode($wholesales_usd);
                    }
                    if ($model->price_type == 2) {

                        if($model->currency == 'uzs') {
                            $model->price_usd = round($model->price / Yii::$app->params['currency']);
                        } else if($model->currency == 'usd'){
                            $model->price_usd = $model->price;
                            $model->price = round($model->price * Yii::$app->params['currency']);
                        }

                        $wholesale = $model->wholesale;
                        $wholesales = [];
                        $wholesales_usd = [];
                        if (!empty($wholesale)) {
                            foreach ($wholesale['price'] as $it => $item) {
                                if ($item !== '' && $wholesale['count'][$it] !== '') {
                                    if($model->currency == 'uzs') {
                                        $wholesales[$wholesale['count'][$it]] = $item;
                                        $wholesales_usd[$wholesale['count'][$it]] = round($item / Yii::$app->params['currency']);
                                    } else if($model->currency == 'usd'){
                                        $wholesales_usd[$wholesale['count'][$it]] = $item;
                                        $wholesales[$wholesale['count'][$it]] = round($item * Yii::$app->params['currency']);
                                    }
                                }
                            }
                        }
                        ksort($wholesales);
                        ksort($wholesales_usd);
                        $model->wholesale = json_encode($wholesales);
                        $model->wholesale_usd = json_encode($wholesales_usd);
                    }
                }
                $model->wholesale_unit_id = 0;

                $model->is_moderated = 0;
                $cats = !empty(Yii::$app->request->post('add_cats')) ? Yii::$app->request->post('add_cats') : [];
                $model->add_cats = ',' . implode(',', array_values(array_unique($cats))) . ',';
                $model->add_cats_titles = json_encode(Yii::$app->request->post('add_cats_titles'));

                if ($status == -1) $model->status = -1;

                $model->price = (string) $model->price;
                if ($model->save()) {
                    $info->product_id = $model->id;

                    if($category->id == 1 && (isset($brand) && $brand) && (isset($lineup) && $lineup)) {
                        $info->name = $lineup->translate->name;
                    } else {
                        $info->name = (Yii::$app->request->post('ProductTranslation')['name']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['name']['ru']: $category->translate->name;
                    }

                    $info->description = (Yii::$app->request->post('ProductTranslation')['description']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['description']['ru']: '';
                    $info->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['ru']: '';
                    $info->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['ru'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['ru']: '';
                    $info->local = 'ru-RU';
                    $info->save();

                    $info_uz->product_id = $model->id;
                    $info_uz->name = (Yii::$app->request->post('ProductTranslation')['name']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['name']['uz']: $info->name;
                    $info_uz->description = (Yii::$app->request->post('ProductTranslation')['description']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['description']['uz']: $info->description;
                    $info_uz->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['uz']: $info->warranty;
                    $info_uz->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['uz'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['uz']: $info->delivery;
                    $info_uz->local = 'uz-UZ';
                    $info_uz->save();


                    $info_en->product_id = $model->id;
                    $info_en->name = (Yii::$app->request->post('ProductTranslation')['name']['en'] != '')? Yii::$app->request->post('ProductTranslation')['name']['en']: $info->name;
                    $info_en->description = (Yii::$app->request->post('ProductTranslation')['description']['en'] != '')? Yii::$app->request->post('ProductTranslation')['description']['en']: $info->description;
                    $info_en->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['en'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['en']: $info->warranty;
                    $info_en->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['en'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['en']: $info->delivery;
                    $info_en->local = 'en-EN';
                    $info_en->save();


                    $info_oz->product_id = $model->id;
                    $info_oz->name = (Yii::$app->request->post('ProductTranslation')['name']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['name']['oz']: $info->name;
                    $info_oz->description = (Yii::$app->request->post('ProductTranslation')['description']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['description']['oz']: $info->description;
                    $info_oz->warranty = (Yii::$app->request->post('ProductTranslation')['warranty']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['warranty']['oz']: $info->warranty;
                    $info_oz->delivery = (Yii::$app->request->post('ProductTranslation')['delivery']['oz'] != '')? Yii::$app->request->post('ProductTranslation')['delivery']['oz']: $info->delivery;
                    $info_oz->local = 'oz-OZ';
                    $info_oz->save();

                    $model->url = Helper::toLatin($info->name) . '_' . $model->id;

                    $model->price = (string) $model->price;
                    $model->save();

                    $image = UploadedFile::getInstanceByName('mainImage');
                    $dir = (__DIR__) . '/../../uploads/products/';
                    if ($image) {
                        $old_image = ProductImages::findOne(['main' => 1, 'product_id' => $model->id]);
                        if (!empty($old_image)) $old_image->delete();
                        $image_model = new ProductImages();
                        $image_model->product_id = $model->id;
                        $image_model->main = 1;
                        $path = $image->baseName . '.' . $image->extension;
                        if ($image->saveAs($dir . $path)) {
                            $resizer = new SimpleImage();
                            $resizer->load($dir . $path);
                            $resizer->resize(Yii::$app->params['imageSizes']['products']['image'][0], Yii::$app->params['imageSizes']['products']['image'][1]);
                            $image_name = uniqid() . '.' . $image->extension;
                            $resizer->save($dir . $image_name);
                            $image_model->image = '/uploads/products/' . $image_name;
                            if (is_file($dir . $path)) if (file_exists($dir . $path)) unlink($dir . $path);

                            $image_model->save();
                        } else {
                            Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                            return $this->goBack();
                        }
                    }

                    $images = UploadedFile::getInstancesByName('images');
                    if (!empty($images)) {
                        foreach ($images as $image) {
                            $image_model = new ProductImages();
                            $image_model->product_id = $model->id;
                            $image_model->main = 0;
                            $path = $image->baseName . '.' . $image->extension;
                            if ($image->saveAs($dir . $path)) {
                                $resizer = new SimpleImage();
                                $resizer->load($dir . $path);
                                $resizer->resize(Yii::$app->params['imageSizes']['products']['image'][0], Yii::$app->params['imageSizes']['products']['image'][1]);
                                $image_name = uniqid() . '.' . $image->extension;
                                $resizer->save($dir . $image_name);
                                $image_model->image = '/uploads/products/' . $image_name;
                                if (is_file($dir . $path)) if (file_exists($dir . $path)) unlink($dir . $path);

                                $image_model->save();
                            } else {
                                Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                                return $this->goBack();
                            }
                        }

                    }
                    if ($unset_opt) {
//                    ProductOption::deleteAll(['product_id' => $model->id]);
                    }

                    ProductOption::deleteAll(['product_id' => $model->id]);


                    $model_custom_options = [];
                    $custom_options = Yii::$app->request->post('custom_options', []);
                    if (!empty($options = Yii::$app->request->post('options'))) {
                        foreach ($options as $group_id => $option) {
                            if (!isset($option['id'])) continue;
                            if ($option['id'] == -1) {
                                $model_custom_options[$group_id] = isset($custom_options[$group_id])? $custom_options[$group_id]: '';
                                continue;
                            }
                            $opt = (!empty(ProductOption::findOne(['product_id' => $model->id, 'option_id' => $option['id']]))) ? ProductOption::findOne(['product_id' => $model->id, 'option_id' => $option['id']]) : new ProductOption();
                            $opt->product_id = $model->id;
                            $opt->option_id = $option['id'];
                            $opt->price = 0;
                            $opt->save();
                        }
                    }
                    $model->custom_options = json_encode($model_custom_options);

                    $model->price = (string) $model->price;
                    $model->save();

                    ProductPerformance::deleteAll(['product_id' => $model->id]);
                    if (!empty($performances = Yii::$app->request->post('Preformance'))) {
                        foreach ($performances['ru']['name'] as $ind => $name) {
                            if (trim($name) == '' && trim($performances['ru']['desc'][$ind]) == '') continue;
                            $perf = new ProductPerformance();
                            $perf->product_id = $model->id;
                            $perf->save();
                            $perf_ru = new ProductPerformanceTranslation();
                            $perf_uz = new ProductPerformanceTranslation();
                            $perf_en = new ProductPerformanceTranslation();
                            $perf_ru->product_performance_id = $perf->id;
                            $perf_ru->name = $name;
                            $perf_ru->description = $performances['ru']['desc'][$ind];
                            $perf_ru->local = 'ru-RU';
                            $perf_ru->save();
                            $perf_uz->product_performance_id = $perf->id;
                            $perf_uz->name = ($performances['uz']['name'][$ind]) ? $performances['uz']['name'][$ind] : $name;
                            $perf_uz->description = ($performances['uz']['desc'][$ind]) ? $performances['uz']['desc'][$ind] : $performances['ru']['desc'][$ind];
                            $perf_uz->local = 'uz-UZ';
                            $perf_uz->save();
                            $perf_en->product_performance_id = $perf->id;
                            $perf_en->name = ($performances['en']['name'][$ind]) ? $performances['en']['name'][$ind] : $name;
                            $perf_en->description = ($performances['en']['desc'][$ind]) ? $performances['en']['desc'][$ind] : $performances['ru']['desc'][$ind];
                            $perf_en->local = 'en-EN';
                            $perf_en->save();
                        }
                    }
                    Yii::$app->session->setFlash('success', FA::i('check').' '.Yii::t('frontend', 'Updated'));
                    return $this->redirect(['update', 'id' => $model->id, 'category' => $model->category_id, 'brand' => $model->brand_id, 'lineup' => $model->lineup_id]);
                } else {
                    Yii::$app->session->setFlash('error', FA::i('warning') . ' Ошибка, попробуйте позже.');
                    return $this->goBack();
                }
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'category' => $category,
                'brand' => $brand,
                'lineup' => $lineup,
                'info' => $info,
                'info_uz' => $info_uz,
                'info_oz' => $info_oz,
                'info_en' => $info_en,
            ]);
        }
    }

    public function actionDeleteImage()
    {
        if (!empty($prod_id = Yii::$app->request->get('product_id')) &&
            !empty($image = Yii::$app->request->get('image_id'))) {
            $item = ProductImages::findOne(['id' => $image, 'product_id' => $prod_id]);
            if (empty($item)) return json_encode(['error' => 'empty-item']);
            if ($item->product->user_id != Yii::$app->getUser()->identity->id) return json_encode(['error' => 'shop-notMatch']);
            if ($item->main == 1) return json_encode(['error' => 'item-isMain']);
            $item->delete();
            return json_encode(['error' => false]);
        }
        return json_encode(['error' => true]);
    }
//
//    /**
//     * Deletes an existing Product model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
    public function actionDelete($id)
    {
        $product = $this->findModel($id);
        $product->delete();
        Yii::$app->session->setFlash('success', FA::i('check').' '.Yii::t('frontend', 'Deleted'));
        return $this->redirect(['index']);
    }


    public function actionSwitch() {
        $model = $this->findModel(Yii::$app->request->post('id'));
        if($model->user_id == Yii::$app->user->identity->id) {
            $model->status = Yii::$app->request->post('status');
            $model->price = (string) $model->price;
            $model->save();
        }
        return json_encode(['error' => false]);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['user_id' => Yii::$app->getUser()->identity->id, 'id' => $id, 'deleted_at' => 0])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionBoost($id) {
        $product = $this->findModel($id);
        $user = User::findOne(Yii::$app->user->id);
        $balance = $user->balance;
        $color_offer_price = Yii::$app->params['boost_price']['colored_offer']['price'];
        $special_offer_price = Yii::$app->params['boost_price']['special_offer']['price'];

        $color_offer = Yii::$app->request->post('colored_offer', 0);
        $special_offer = Yii::$app->request->post('special_offer', 0);
        if($color_offer == 1 && $special_offer == 1) {
            $total_price = $color_offer_price + $special_offer_price;
            if(($total_price) > $balance) {
                return $this->redirect(['balance/fill']);
            }
            if($product->colored_offer > time()) {
                $product->colored_offer = $product->colored_offer + Yii::$app->params['boost_price']['colored_offer']['days'] * 24 * 3600;
            } else {
                $product->colored_offer = time() + Yii::$app->params['boost_price']['colored_offer']['days'] * 24 * 3600;
            }
            if($product->special_offer > time()) {
                $product->special_offer = $product->special_offer + Yii::$app->params['boost_price']['special_offer']['days'] * 24 * 3600;
            } else {
                $product->special_offer = time() + Yii::$app->params['boost_price']['special_offer']['days'] * 24 * 3600;
            }

            $product->price = (string) $product->price;
            $product->save();
            $user->balance = $user->balance - $total_price;
            $user->save();
            return $this->redirect(['update', 'id' => $product->id, 'category' => $product->category_id, 'brand' => $product->brand_id, 'lineup' => $product->lineup_id]);
        } else if($color_offer == 1) {
            $total_price = $color_offer_price;
            if(($total_price) > $balance) {
                return $this->redirect(['balance/fill']);
            }
            if($product->colored_offer > time()) {
                $product->colored_offer = $product->colored_offer + Yii::$app->params['boost_price']['colored_offer']['days'] * 24 * 3600;
            } else {
                $product->colored_offer = time() + Yii::$app->params['boost_price']['colored_offer']['days'] * 24 * 3600;
            }

            $product->price = (string) $product->price;

            $product->save();
            $user->balance = $user->balance - $total_price;
            $user->save();
            return $this->redirect(['update', 'id' => $product->id, 'category' => $product->category_id, 'brand' => $product->brand_id, 'lineup' => $product->lineup_id]);
        } else if($special_offer == 1) {
            $total_price = $special_offer_price;
            if(($total_price) > $balance) {
                return $this->redirect(['balance/fill']);
            }

            if($product->special_offer > time()) {
                $product->special_offer = $product->special_offer + Yii::$app->params['boost_price']['special_offer']['days'] * 24 * 3600;
            } else {
                $product->special_offer = time() + Yii::$app->params['boost_price']['special_offer']['days'] * 24 * 3600;
            }

            $product->price = (string) $product->price;
            $product->save();
            $user->balance = $user->balance - $total_price;
            $user->save();
            return $this->redirect(['update', 'id' => $product->id, 'category' => $product->category_id, 'brand' => $product->brand_id, 'lineup' => $product->lineup_id]);
        }

        return $this->render('boost', [
            'product' => $product,
        ]);
    }
}
