<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $category_id
 * @property integer $price
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property ProductImages[] $productImages
 * @property LineupTranslation[] $LineupTranslation
 * @property Category $category
 * @property Shop $shop
 */
class Lineup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lineups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id'], 'required'],
            [['year'], 'integer', 'max' => date('Y'), 'min' => 1900],
            [['brand_id',  'status', 'warranty', 'created_at', 'updated_at'], 'integer'],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'brand_id' => 'Марка',
            'warranty' => 'Гарантия (мес)',
            'year' => 'Год',
            'logo' => 'Фото',
            'status' => 'Статус',
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
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

    public static function find() {
        return parent::find()->with('translate')
            ->with('brand')
            ->with('activeOptions');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLineupTranslation()
    {
        return $this->hasMany(LineupTranslation::className(), ['lineup_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }


    public function getTranslate() {
        return ($this->hasOne(LineupTranslation::className(), ['lineup_id' => 'id'])->where(['local' => Language::getCurrent()->local])->all())? $this->hasOne(LineupTranslation::className(), ['lineup_id' => 'id'])->where(['local' => Language::getCurrent()->local]): $this->hasOne(LineupTranslation::className(), ['lineup_id' => 'id'])->where(['local' => Language::getDefaultLang()->local]);
    }


    public function getActiveOptions()
    {
        return $this->hasMany(LineupOption::className(), ['lineup_id' => 'id']);
    }
}
