<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "symptom".
 *
 * @property int $id
 * @property int|null $segment
 * @property string|null $symptom
 *
 * @property CountSymptom[] $countSymptoms
 * @property Segment $segment0
 */
class Symptom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'symptom';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['segment'], 'integer'],
            [['symptom'], 'string', 'max' => 150],
            [['segment'], 'exist', 'skipOnError' => true, 'targetClass' => Segment::className(), 'targetAttribute' => ['segment' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'segment' => Yii::t('app', 'Segment'),
            'symptom' => Yii::t('app', 'Symptom'),
        ];
    }

    public function getCountSymptoms()
    {
        return $this->hasMany(CountSymptom::className(), ['symptom' => 'id']);
    }
    
    public function getSegment0()
    {
        return $this->hasOne(Segment::className(), ['id' => 'segment']);
    }

    public function getCases()
    {
        return $this->hasMany(Cases::className(), ['symptomp' => 'id']);
    }
}
