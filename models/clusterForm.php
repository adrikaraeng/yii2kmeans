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
class clusterForm extends Model
{
    public $jumlah_cluster;
    public $start_date;
    public $end_date;
    
    public function rules()
    {
        return [
            // username and password are both required
            [['start_date','end_date','jumlah_cluster'], 'required', 'message'=>''],
        ];
    }
    public function attributeLabels()
    {
        return [
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'jumlah_cluster' => Yii::t('app','Jumlah Cluster'),
        ];
    }
}
