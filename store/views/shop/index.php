<?php

use common\models\Shop;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel store\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Компании';

if(empty(Shop::findOne(['id' => Yii::$app->session->get('shop_id'), 'deleted_at' => 0]))) {
    Yii::$app->session->setFlash('info', FA::i('info-circle').' Добро пожаловать! Для начала работы в нашем сервисе, добавьте свой первый магазин.');
}
?>
<div class="white-block">
    <div class="row">
        <div class="col-sm-12">
            <div class="news_body">
<div class="shop-index">
    <div class="page-header">
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
            'columns' => [
                'id',
                [
                    'attribute' => 'name',
                    'format' => 'html',
                    'value' => function($data) {
                        $link = '';
                        return $data->name.$link;
                    }
                ],
                [
                    'label' => 'Просмотров',
                    'attribute' => 'view'
                ],
                [
                    'label' => 'Прос-ов номера',
                    'attribute' => 'view_phone'
                ],
                [
                    'label' => 'Прос-ов объявлений',
                    'attribute' => 'view_prods'
                ],
                'rating',
                [
                    'attribute' => 'status',
                    'format'=> 'html',
                    'filter' => array("0" => "На модерацции", "1" => "Активный", "-1" => "Заблокированный",),
                    'value' => function($data) {
                        if($data->status == '-1') {
                            return '<span class="text-danger"><i class="fa fa-warning"></i> Заблокированный</span>';
                        }
                        if($data->status == '1') {
                            return '<span class="text-success"><i class="fa fa-check"></i> Активный</span>';
                        }
                        if($data->status == '0') {
                            return '<span class="text-warning"><i class="fa fa-clock"></i> На модерации</span>';
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
                                $url, ['class' => 'text-secondary']);
                        },
                        'delete' => function ($url, $data)  {
                            return Html::a(
                                FA::i('trash')->size('2x'),
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
