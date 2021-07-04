<?php
namespace frontend\models;

use common\models\Order;
use common\models\OrderProduct;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SupportForm extends Model
{
    public $name;
    public $contact;
    public $text;
    public $type;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'contact', 'text', 'type'], 'trim'],
            [['name', 'contact', 'text', 'type'], 'required'],
            [['name', 'contact', 'text'], 'string', 'min' => 2],
        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'contact' => 'E-mail или телефон',
            'text' => 'Текст обращения',
            'type' => 'Тип обращения',
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return null;
        }
        $types = [
            'Помощь',
            'Жалоба',
            'Предложение',
        ];

        $this->type = isset($types[$this->type])? $types[$this->type]: 'Не указано';
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'support-html', 'text' => 'support-text'],
                [
                    'name' => $this->name,
                    'contact' => $this->contact,
                    'text' => $this->text,
                    'type' => $this->type,
                ]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo(Yii::$app->params['supportMail'])
            ->setSubject('Обращение от пользователя сайта ' . Yii::$app->name)
            ->send();
    }
}
