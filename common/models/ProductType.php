<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "product_type".
 *
 * @property int $id
 * @property string|null $name
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Products[] $products
 */
class ProductType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_type';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['status'], 'required'],
            [['status', 'created_at', 'updated_at','id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Наименование'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата изменения'),
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Products::className(), ['type' => 'id']);
    }
}
