<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "segment".
 *
 * @property int $id
 * @property string $segment
 *
 * @property Symptom[] $symptoms
 */
class Segment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['segment'], 'required'],
            [['segment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'segment' => Yii::t('app', 'Segment'),
        ];
    }

    /**
     * Gets query for [[Symptoms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSymptoms()
    {
        return $this->hasMany(Symptom::className(), ['segment' => 'id']);
    }
}
