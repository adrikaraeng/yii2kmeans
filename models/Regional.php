<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regional".
 *
 * @property int $id
 * @property int|null $regional
 *
 * @property Cases[] $cases
 * @property Witel[] $witels
 */
class Regional extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regional';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['regional'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'regional' => Yii::t('app', 'Regional'),
        ];
    }

    /**
     * Gets query for [[Cases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCases()
    {
        return $this->hasMany(Cases::className(), ['regional' => 'id']);
    }

    /**
     * Gets query for [[Witels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWitels()
    {
        return $this->hasMany(Witel::className(), ['regional' => 'id']);
    }
}
