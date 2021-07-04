<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "body_style".
 *
 * @property int $id
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property BodyTranslations[] $bodyTranslations
 */
class BodyStyle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'body_style';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
        return parent::find()->with('translate');
    }
    /**
     * Gets query for [[BodyTranslations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBodyTranslations()
    {
        return $this->hasMany(BodyTranslations::className(), ['body_id' => 'id']);
    }


    public function getTranslate() {
        return
            ($this->hasOne(BodyTranslations::className(), ['body_id' => 'id'])->where(['local' => Language::getCurrent()->local])->all())?
                $this->hasOne(BodyTranslations::className(), ['body_id' => 'id'])->where(['local' => Language::getCurrent()->local]):
                $this->hasOne(BodyTranslations::className(), ['body_id' => 'id'])->where(['local' => Language::getDefaultLang()->local]);
    }



}
