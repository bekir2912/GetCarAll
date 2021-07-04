<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "user_black".
 *
 * @property int $id
 * @property int $shop_id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 */
class UserBlack extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_black';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop_id', 'user_id', 'name'], 'required'],
            [['shop_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', 'Пользователь'),
            'name' => Yii::t('app', 'Наименование '),
            'description' => Yii::t('app', 'Описание'),
            'created_at' => Yii::t('app', 'Созданно'),
            'updated_at' => Yii::t('app', 'Измененно'),
        ];
    }


    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }



    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
}
