<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "booking_statuses".
 *
 * @property int $id
 * @property string|null $name
 *
 * @property Booking[] $bookings
 */
class BookingStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * Gets query for [[Bookings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::className(), ['status' => 'id']);
    }
}
