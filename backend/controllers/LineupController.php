<?php

namespace backend\controllers;

use backend\models\LineupSearch;
use common\models\Category;
use common\models\Lineup;
use common\models\LineupOption;
use common\models\LineupTranslation;
use rmrevin\yii\fontawesome\FA;
use Yii;
use common\models\Product;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class LineupController extends BehaviorsController
{


    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LineupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $category = !empty(Yii::$app->request->get('category'))? Yii::$app->request->get('category'): false;

        if(empty($category = Category::findOne(['id' => $category, 'status' => 1]))) $category = false;
        if(!empty($category)) if(!empty($category->activeCategories))  $category = false;
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

        $model = new Lineup();
        $info = new LineupTranslation(['scenario' => 'create']);
        $info_uz = new LineupTranslation();
        $info_en = new LineupTranslation();
        $info_oz = new LineupTranslation();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->save();
            $dir = (__DIR__).'/../../uploads/brands/';

            $logo = UploadedFile::getInstance($model,'logo');

            if($logo) {
                $path = $logo->baseName . '.' . $logo->extension;
                if ($logo->saveAs($dir . $path)) {
//                    $resizer = new SimpleImage();
//                    $resizer->load($dir . $path);
//                    $resizer->resize(Yii::$app->params['imageSizes']['brands']['logo'][0], Yii::$app->params['imageSizes']['brands']['logo'][1]);
                    $logo_name = uniqid() . '.' . $logo->extension;
//                    $resizer->save($dir . $logo_name);
                    rename($dir.$path, $dir.$logo_name);
                    $model->logo = '/uploads/brands/' . $logo_name;
                    if (file_exists($dir . $path)) unlink($dir . $path);
                }
            }
            else $model->logo = '/uploads/site/default_cat.png';
            if($model->save()) {
                $info->lineup_id = $model->id;
                $info->name = (Yii::$app->request->post('LineupTranslation')['name']['ru'] != '')? Yii::$app->request->post('LineupTranslation')['name']['ru']: '';
                $info->description = (Yii::$app->request->post('LineupTranslation')['description']['ru'] != '')? Yii::$app->request->post('LineupTranslation')['description']['ru']: '';
                $info->local = 'ru-RU';
                $info->save();

                $info_uz->lineup_id = $model->id;
                $info_uz->name = (Yii::$app->request->post('LineupTranslation')['name']['uz'] != '')? Yii::$app->request->post('LineupTranslation')['name']['uz']: $info->name;
                $info_uz->description = (Yii::$app->request->post('LineupTranslation')['description']['uz'] != '')? Yii::$app->request->post('LineupTranslation')['description']['uz']: $info->description;
                $info_uz->local = 'uz-UZ';
                $info_uz->save();


                $info_en->lineup_id = $model->id;
                $info_en->name = (Yii::$app->request->post('LineupTranslation')['name']['en'] != '')? Yii::$app->request->post('LineupTranslation')['name']['en']: $info->name;
                $info_en->description = (Yii::$app->request->post('LineupTranslation')['description']['en'] != '')? Yii::$app->request->post('LineupTranslation')['description']['en']: $info->description;
                $info_en->local = 'en-EN';
                $info_en->save();

                $info_oz->lineup_id = $model->id;
                $info_oz->name = (Yii::$app->request->post('LineupTranslation')['name']['oz'] != '')? Yii::$app->request->post('LineupTranslation')['name']['oz']: $info->name;
                $info_oz->description = (Yii::$app->request->post('LineupTranslation')['description']['oz'] != '')? Yii::$app->request->post('LineupTranslation')['description']['oz']: $info->description;
                $info_oz->local = 'oz-OZ';
                $info_oz->save();

                $model->save();

                if(!empty($options = Yii::$app->request->post('options'))) {
                    foreach ($options as $option) {
                        if(!isset($option['id'])) continue;
                        $opt = new LineupOption();
                        $opt->lineup_id = $model->id;
                        $opt->option_id = $option['id'];
                        $opt->price = 0;
                        $opt->save();
                    }
                }
                return $this->redirect(['update', 'id' => $model->id, 'category' => $model->brand->category_id]);
            }
            else {
                Yii::$app->session->setFlash('error', FA::i('warning').' Ошибка, попробуйте позже.');
                return $this->goBack();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'category' => $category,
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
        $model = $this->findModel($id);
        $old_cat = $model->brand->category_id;
        $unset_opt = false;
        $status = $model->status;
        $old_logo = $model->logo;
        $category = !empty(Yii::$app->request->get('category'))? Yii::$app->request->get('category'): false;

        if(empty($category = Category::findOne(['id' => $category]))) $category = false;
        if(!empty($category)) if(!empty($category->activeCategories))  $category = false;

        $info = LineupTranslation::findOne((['lineup_id' => $model->id, 'local' => 'ru-RU']));
        $info->scenario = 'create';
        $info_uz = LineupTranslation::findOne((['lineup_id' => $model->id, 'local' => 'uz-UZ']));
        $info_en = LineupTranslation::findOne((['lineup_id' => $model->id, 'local' => 'en-EN']));
        $info_oz = LineupTranslation::findOne((['lineup_id' => $model->id, 'local' => 'oz-OZ']));
        if(!$info_oz) $info_oz = new LineupTranslation();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if(Yii::$app->request->post('Lineup')['category_id'] != $old_cat) $unset_opt = true;
            $dir = (__DIR__).'/../../uploads/brands/';

            $logo = UploadedFile::getInstance($model,'logo');

            if($logo) {
                $path = $logo->baseName . '.' . $logo->extension;
                if ($logo->saveAs($dir . $path)) {
//                    $resizer = new SimpleImage();
//                    $resizer->load($dir . $path);
//                    $resizer->resize(Yii::$app->params['imageSizes']['brands']['logo'][0], Yii::$app->params['imageSizes']['brands']['logo'][1]);
                    $logo_name = uniqid() . '.' . $logo->extension;
//                    $resizer->save($dir . $logo_name);
                    rename($dir.$path, $dir.$logo_name);
                    $model->logo = '/uploads/brands/' . $logo_name;
                    if (file_exists($dir . $path)) unlink($dir . $path);
                }
            }
            else $model->logo = $old_logo;
            $model->save();

            if($model->save()) {
                $info->lineup_id = $model->id;
                $info->name = (Yii::$app->request->post('LineupTranslation')['name']['ru'] != '')? Yii::$app->request->post('LineupTranslation')['name']['ru']: '';
                $info->description = (Yii::$app->request->post('LineupTranslation')['description']['ru'] != '')? Yii::$app->request->post('LineupTranslation')['description']['ru']: '';
                $info->local = 'ru-RU';
                $info->save();

                $info_uz->lineup_id = $model->id;
                $info_uz->name = (Yii::$app->request->post('LineupTranslation')['name']['uz'] != '')? Yii::$app->request->post('LineupTranslation')['name']['uz']: $info->name;
                $info_uz->description = (Yii::$app->request->post('LineupTranslation')['description']['uz'] != '')? Yii::$app->request->post('LineupTranslation')['description']['uz']: $info->description;
                $info_uz->local = 'uz-UZ';
                $info_uz->save();


                $info_en->lineup_id = $model->id;
                $info_en->name = (Yii::$app->request->post('LineupTranslation')['name']['en'] != '')? Yii::$app->request->post('LineupTranslation')['name']['en']: $info->name;
                $info_en->description = (Yii::$app->request->post('LineupTranslation')['description']['en'] != '')? Yii::$app->request->post('LineupTranslation')['description']['en']: $info->description;
                $info_en->local = 'en-EN';
                $info_en->save();

                $info_oz->lineup_id = $model->id;
                $info_oz->name = (Yii::$app->request->post('LineupTranslation')['name']['oz'] != '')? Yii::$app->request->post('LineupTranslation')['name']['oz']: $info->name;
                $info_oz->description = (Yii::$app->request->post('LineupTranslation')['description']['oz'] != '')? Yii::$app->request->post('LineupTranslation')['description']['oz']: $info->description;
                $info_oz->local = 'oz-OZ';
                $info_oz->save();

                $model->save();
                LineupOption::deleteAll(['lineup_id' => $model->id]);
                if(!empty($options = Yii::$app->request->post('options'))) {
                    foreach ($options as $option) {
                        if(!isset($option['id'])) continue;
                        $opt = (!empty(LineupOption::findOne(['lineup_id' => $model->id, 'option_id' => $option['id']])))? LineupOption::findOne(['lineup_id' => $model->id, 'option_id' => $option['id']]) : new LineupOption();
                        $opt->lineup_id = $model->id;
                        $opt->option_id = $option['id'];
                        $opt->price = 0;
                        $opt->save();
                    }
                }
                return $this->redirect(['update', 'id' => $model->id, 'category' => $model->brand->category_id]);
            }
            else {
                Yii::$app->session->setFlash('error', FA::i('warning').' Ошибка, попробуйте позже.');
                return $this->goBack();
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'category' => $category,
                'info' => $info,
                'info_uz' => $info_uz,
                'info_oz' => $info_oz,
                'info_en' => $info_en,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $product = $this->findModel($id);
        $product->delete();
        return $this->redirect(['index']);
    }
//
//    /**
//     * Deletes an existing Product model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lineup::findOne(['id' => $id, 'deleted_at' => 0])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
