<?php

namespace frontend\controllers;

use common\models\Brand;
use common\models\Lineup;
use common\models\News;
use common\models\Category;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class WikiController extends Controller
{
    public function actionList()
    {
        Yii::$app->session->set('root_category', null);
        Yii::$app->session->set('page', 'wiki-list');
        $category = Yii::$app->request->get('cat_id', '1');
        if (empty($category = Category::findOne(['id' => $category, 'status' => 1]))) $category = false;

        $query = Brand::find();

        if ($category->on_main == 1 || $category->spec == 1) return $this->redirect(['site/error']);
        if ($category) {
            $temp_category = $category;
            $ids = [$category->id];

            if ($temp_category->activeCategories) {
                $ids = array_merge($ids, $this->getIds($temp_category));
                foreach ($temp_category->activeCategories as $activeCategory) {
                    $ids = array_merge($ids, $this->getIds($activeCategory));
                }
            }
            $query->where(['in', 'category_id', $ids]);
        }

        $q = trim(Yii::$app->request->get('q', ''));

        if ($q) {
            $query->andWhere(['like', 'name' , $q]);
        }

        $count = $query->count();

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => 24]);
        $brands = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy('name')
            ->all();
        return $this->render('list', [
            'q' => $q,
            'brands' => $brands,
            'filter_cat' => $category,
            'pages' => $pages,
            ]);
    }

    public function actionIndex($id)
    {
        Yii::$app->session->set('root_category', null);
        Yii::$app->session->set('page', 'wiki');
        $brand = Brand::findOne(['status' => 1, 'id' => $id]);
        if (empty($brand)) return $this->redirect(['site/error']);

        $q = trim(Yii::$app->request->get('q', ''));

        return $this->render('index', [
            'brand' => $brand,
            'q' => $q,
            ]);
    }

    public function actionShow($id)
    {
        Yii::$app->session->set('root_category', null);
        Yii::$app->session->set('page', 'wiki-show');
        $lineup = Lineup::findOne(['status' => 1, 'id' => $id]);
        if (empty($lineup)) return $this->redirect(['site/error']);

        return $this->render('show', [
            'lineup' => $lineup,
        ]);
    }

    protected function getIds($category)
    {
        $ids = ArrayHelper::map(ArrayHelper::toArray($category->activeCategories), 'id', 'user_id');
        return array_keys($ids);
    }
}
