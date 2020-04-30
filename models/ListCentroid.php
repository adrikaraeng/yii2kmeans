<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "list_centroid".
 *
 * @property int $id
 * @property int $count_symptom
 * @property int $iterasi
 * @property int $cluster
 *
 * @property CountSymptom $countSymptom
 */
class ListCentroid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'list_centroid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count_symptom'], 'required'],
            [['count_symptom', 'iterasi', 'cluster'], 'integer'],
            [['count_symptom'], 'exist', 'skipOnError' => true, 'targetClass' => CountSymptom::className(), 'targetAttribute' => ['count_symptom' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'count_symptom' => Yii::t('app', 'Count Symptom'),
            'iterasi' => Yii::t('app', 'Iterasi'),
            'cluster' => Yii::t('app', 'Cluster'),
        ];
    }

    /**
     * Gets query for [[CountSymptom]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountSymptom()
    {
        return $this->hasOne(CountSymptom::className(), ['id' => 'count_symptom']);
    }
}
