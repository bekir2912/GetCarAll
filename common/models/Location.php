<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pickup_and_return".
 *
 * @property int $id
 * @property int $shop_id
 * @property string $address
 * @property string|null $discription
 * @property int $category
 * @property int|null $status
 *
 * @property Shop $shop
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pickup_and_return';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'address'], 'required'],
            [['shop_id', 'category', 'status'], 'integer'],
            [['discription'], 'string'],
            [['address'], 'string', 'max' => 255],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['shop_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', 'ID компании'),
            'address' => Yii::t('app', 'Адресс'),
            'discription' => Yii::t('app', 'Описание'),
            'category' => Yii::t('app', 'Категории'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * Gets query for [[Shop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
}
