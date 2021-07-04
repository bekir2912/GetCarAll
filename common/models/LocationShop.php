<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "location_shop".
 *
 * @property int $id
 * @property int $shop_id
 * @property string $location
 * @property string|null $discription
 * @property int|null $created_at
 *
 * @property Shops $shop
 */
class LocationShop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location_shop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'location'], 'required'],
            [['shop_id', 'created_at'], 'integer'],
            [['location'], 'string'],
            [['discription'], 'string', 'max' => 255],
            [['shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shops::className(), 'targetAttribute' => ['shop_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', 'Shop ID'),
            'location' => Yii::t('app', 'Location'),
            'discription' => Yii::t('app', 'Discription'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Shop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shops::className(), ['id' => 'shop_id']);
    }
}
