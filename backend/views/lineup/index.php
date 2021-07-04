<?php

use common\models\Product;
use common\models\Sale;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel store\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Модели';

$brands = \common\models\Lineup::find()->select('brand_id')->orderBy('brand_id')->groupBy('brand_id')->all();

$brand_filter = [];
if(!empty($brands)) {
    foreach ($brands as $brand) {
        $brand_filter[$brand->brand->id] = $brand->brand->name;
    }
}
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
<div class="product-index">
    <div class="page-header" id="teb_shop">
        <?= Html::encode($this->title) ?>
        <small class="pull-right">
            <?= Html::a('Добавить '.FA::i('plus'), ['create'], ['class' => 'btn btn-success']) ?>
        </small>
    </div>
    <p></p>

<?php Pjax::begin(); ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterRowOptions' => ['class' => 'filters prod-filter'],
            'columns' => [

                'id',
                [
                    'attribute' => 'name',
                    'label' => 'Название',
                    'format' => 'html',
                    'value' => function($data) {
                        return $data->translate->name;
                    },
                ],
                [
                    'attribute' => 'brand_id',
                    'value' => function($data) {
                        return $data->brand->name;
                    },
                    'filter' => $brand_filter,
                ],
                [
                    'attribute' => 'status',
                    'format'=> 'html',
                    'filter' => array("0" => "Не активен", "1" => "Активен"),
                    'value' => function($data) {
                            if($data->status == 0) {
                                return '<span class="text-warning"><i class="fa fa-info"></i> Не активен</span>';
                            }
                            if($data->status == 1) {
                                return '<span class="text-success"><i class="fa fa-check"></i> Активен</span>';
                            }
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete} {update}',
                    'buttons' => [
                        'update' => function ($url, $data)  {
                            return Html::a(
                                FA::i('arrow-right')->size('2x'),
                                $url."&category=".$data->brand->category_id, ['class' => 'text-secondary']);
                        },
                        'delete' => function ($url, $data)  {
                            return Html::a(
                                FA::i('remove')->size('2x'),
                                $url, ['class' => 'text-danger',
                                'title'=>"Удалить", 'aria-label'=>"Удалить", 'data-pjax'=>"0", 'data-confirm'=>"Вы уверены, что хотите удалить этот элемент?", 'data-method'=>"post"
                            ]);
                        },
                    ]
                ],

            ],
        ]); ?>
    </div>
<?php Pjax::end(); ?></div>
</div>
</div>
</div>
</div>

<?php $this->registerJs('
    $(document).ready(function() {
        $(\'select.form-control\').select2(
            {
                language: {
                  noResults: function () {
                    return "Ничего не найдено";
                  }
                }
            }
        );
    });
    $(document).on(\'ready pjax:success\', function() {
        $(\'select.form-control\').select2(
            {
                language: {
                  noResults: function () {
                    return "Ничего не найдено";
                  }
                }
            }
        );
    });
');?>