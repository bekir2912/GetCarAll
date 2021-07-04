<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "body_translations".
 *
 * @property int $id
 * @property int $body_id
 * @property int $name
 * @property int $local
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BodyStyle $body
 */
class BodyTranslations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'body_translations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['body_id', 'name', 'local', 'created_at', 'updated_at'], 'required'],
            [['body_id', 'name', 'local', 'created_at', 'updated_at'], 'integer'],
            [['body_id'], 'exist', 'skipOnError' => true, 'targetClass' => BodyStyle::className(), 'targetAttribute' => ['body_id' => 'id']],
            [['local'], 'exist', 'skipOnError' => true, 'targetClass' => Language::className(), 'targetAttribute' => ['local' => 'local']],
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'body_id' => Yii::t('app', 'Body ID'),
            'name' => Yii::t('app', 'Наименование'),
            'local' => Yii::t('app', 'Local'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Body]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBody()
    {
        return $this->hasOne(BodyStyle::className(), ['id' => 'body_id']);
    }

    public function getLocal()
    {
        return $this->hasOne(Language::className(), ['local' => 'local']);
    }
}
