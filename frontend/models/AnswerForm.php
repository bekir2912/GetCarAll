<?php
namespace frontend\models;

use common\models\Answer;
use common\models\Question;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Signup form
 */
class AnswerForm extends Model
{
    public $answer;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['answer', 'trim'],
            ['answer', 'required'],
        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'answer' => Yii::t('frontend', 'Answer text'),
        ];
    }

    public function addAnswer($id)
    {
        if (!$this->validate()) {
            return null;
        }
        $question = Question::findOne($id);
        if($question) {

            $answer = new Answer();
            $answer->file = null;
            $file = UploadedFile::getInstanceByName('file');
            if($file){
                $dir = (__DIR__).'/../../uploads/users/';
                $path = $file->baseName.'.'.$file->extension;
                if($file->saveAs($dir.$path)) {
                    $image_name = uniqid().'.'.$file->extension;
                    rename($dir.$path, $dir.$image_name);
//                    $resizer->save($dir.$image_name);
                    $answer->file = '/uploads/users/'.$image_name;
                    if(file_exists($dir.$path)) unlink($dir.$path);
                }
            }

            $answer->user_id = Yii::$app->user->id;
            $answer->question_id = $id;
            $answer->answer = $this->answer;
            if ((!Yii::$app->user->isGuest) && ($question->user_id == Yii::$app->user->identity->id)) {
                $answer->is_read = 1;
            }
            return $answer->save() ? $answer : null;
        }
        return null;
    }
}
