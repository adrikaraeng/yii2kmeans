<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "centroid".
 *
 * @property int $id
 * @property int $login
 * @property string $reg1
 * @property string $reg2
 * @property string $reg3
 * @property string $reg4
 * @property string $reg5
 * @property string $reg6
 * @property string $reg7
 * @property int $iterasi
 */
class Centroid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'centroid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7', 'iterasi', 'cluster', 'id_count_symptom'], 'required'],
            [['login', 'iterasi', 'id_count_symptom'], 'integer'],
            [['reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7'], 'string', 'max' => 11],
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
            'reg1' => Yii::t('app', 'Regional 1'),
            'reg2' => Yii::t('app', 'Regional 2'),
            'reg3' => Yii::t('app', 'Regional 3'),
            'reg4' => Yii::t('app', 'Regional 4'),
            'reg5' => Yii::t('app', 'Regional 5'),
            'reg6' => Yii::t('app', 'Regional 6'),
            'reg7' => Yii::t('app', 'Regional 7'),
            'id_count_symptom' => Yii::t('app', 'Symptom'),
            'iterasi' => Yii::t('app', 'Iterasi'),
            'cluster' => Yii::t('app', 'Cluster'),
        ];
    }
    public function getSymptom()
    {
        return $this->hasOne(Symptom::className(), ['id' => 'id_count_symptom']);
    }
}
