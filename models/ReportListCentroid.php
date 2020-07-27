<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_list_centroid".
 *
 * @property int $id
 * @property int $login
 * @property string|null $kmeans_type
 * @property int|null $count_symptom
 * @property int|null $iterasi
 * @property int|null $cluster
 * @property string|null $c1
 * @property string|null $c2
 * @property string|null $c3
 * @property string|null $c4
 * @property string|null $c5
 *
 * @property ReportCountSymptom $countSymptom
 * @property User $login0
 */
class ReportListCentroid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_list_centroid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['login', 'count_symptom', 'iterasi', 'cluster'], 'integer'],
            [['kmeans_type'], 'string'],
            [['date_report'], 'safe'],
            [['c1', 'c2', 'c3', 'c4', 'c5'], 'string', 'max' => 11],
            [['count_symptom'], 'exist', 'skipOnError' => true, 'targetClass' => ReportCountSymptom::className(), 'targetAttribute' => ['count_symptom' => 'id']],
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
            'kmeans_type' => Yii::t('app', 'Kmeans Type'),
            'count_symptom' => Yii::t('app', 'Count Symptom'),
            'iterasi' => Yii::t('app', 'Iterasi'),
            'cluster' => Yii::t('app', 'Cluster'),
            'c1' => Yii::t('app', 'C1'),
            'c2' => Yii::t('app', 'C2'),
            'c3' => Yii::t('app', 'C3'),
            'c4' => Yii::t('app', 'C4'),
            'c5' => Yii::t('app', 'C5'),
        ];
    }

    /**
     * Gets query for [[CountSymptom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountSymptom()
    {
        return $this->hasOne(ReportCountSymptom::className(), ['id' => 'count_symptom']);
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
