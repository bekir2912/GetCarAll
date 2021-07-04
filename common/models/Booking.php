<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property int|null $pickup_shop_id
 * @property int|null $return_shop_id
 * @property int|null $product_id
 * @property int|null $start_date
 * @property int|null $end_date
 * @property string|null $pickup_location
 * @property string|null $return_location
 * @property int $status
 *
 * @property Shops $pickupShop
 * @property Products $product
 * @property Shops $returnShop
 */
class Booking extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_shop_id', 'return_shop_id', 'product_id', 'status','start_date','end_date'], 'required'],
            [['return_shop_id', 'product_id', 'status', 'user_id', 'shop_id'], 'integer'],
            [['pickup_shop_id'], 'safe'],
            [['start_date','end_date'], 'string', 'max' => 45],
//            [['start_date','end_date'], 'date'],
            [['status'], 'required'],
            [['text'], 'safe'],

            [['pickup_location', 'return_location'], 'string', 'max' => 255],
            [['pickup_shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['pickup_shop_id' => 'id']],
            [['return_shop_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::className(), 'targetAttribute' => ['return_shop_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shop_id' => Yii::t('app', 'Компания'),
            'pickup_shop_id' => Yii::t('app', 'Место сдачи авто'),
            'return_shop_id' => Yii::t('app', 'Место полученя'),
            'product_id' => Yii::t('app', 'Авто'),
            'user_id' => Yii::t('app', 'ID пользователя'),
            'text' => Yii::t('app', 'Описание'),
            'start_date' => Yii::t('app', 'Дата получения'),
            'end_date' => Yii::t('app', 'Дата возврата'),
            'pickup_location' => Yii::t('app', 'Индивидуальное место вывоза'),
            'return_location' => Yii::t('app', 'Индивидуальное место получения'),
            'status' => Yii::t('app', 'Статус'),
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => BookingStatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }




    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }


    /**
     * Gets query for [[PickupShop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickupLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'pickup_shop_id']);
    }

    /**
     * Gets query for [[ReturnShop]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturnLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'return_shop_id']);
    }

    public function getStatus()
    {
        return $this->hasOne(BookingStatus::className(), ['id' => 'status']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }


}
