<?php

namespace app\models;

use Yii;
use yii\base\Model;

class IndexForm extends Model
{
    public $file;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['file'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->file->saveAs('uploads/target.txt');

            return true;
        } else {
            return false;
        }
    }
}
