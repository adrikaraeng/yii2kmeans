<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class ImportFormCase extends Model
{
    public $jenis_case;
    public $file_case;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['file_case'], 'required', 'message'=>''],
            [['file_case'],
                'file',
                'extensions' => 'xls, xlsx', 
                'skipOnEmpty' => true,
                'maxSize' => 1024*1024*100,
                'tooBig' => 'File tidak boleh lebih dari 10 Mb',
            ],
        ];
    }
    public function attributeLabels()
    {
        return [
            'file_case' => Yii::t('app', 'Import file'),
        ];
    }
}
