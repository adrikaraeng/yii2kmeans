<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "count_cluster".
 *
 * @property int $id
 * @property int $login
 * @property string $start_date
 * @property string $end_date
 * @property int $jumlah_cluster
 *
 * @property User $login0
 */
class CountCluster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'count_cluster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'start_date', 'end_date', 'jumlah_cluster'], 'required','message'=>''],
            [['login', 'jumlah_cluster'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['login'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['login' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'login' => Yii::t('app', 'Login'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'jumlah_cluster' => Yii::t('app', 'Jumlah Cluster'),
        ];
    }

    /**
     * Gets query for [[Login0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogin0()
    {
        return $this->hasOne(User::className(), ['id' => 'login']);
    }
}
