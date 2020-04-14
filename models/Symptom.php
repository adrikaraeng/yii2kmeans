<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "symptom".
 *
 * @property int $id
 * @property string|null $symptom
 *
 * @property Cases[] $cases
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
            [['symptom'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'symptom' => Yii::t('app', 'Symptom'),
        ];
    }

    /**
     * Gets query for [[Cases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCases()
    {
        return $this->hasMany(Cases::className(), ['symptomp' => 'id']);
    }
}
