<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "witel".
 *
 * @property int $id
 * @property string|null $nama_witel
 * @property int|null $regional
 *
 * @property Cases[] $cases
 * @property Regional $regional0
 */
class Witel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'witel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['regional'], 'integer'],
            [['nama_witel'], 'string', 'max' => 50],
            [['regional'], 'exist', 'skipOnError' => true, 'targetClass' => Regional::className(), 'targetAttribute' => ['regional' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nama_witel' => Yii::t('app', 'Nama Witel'),
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
        return $this->hasMany(Cases::className(), ['witel' => 'id']);
    }

    /**
     * Gets query for [[Regional0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegional0()
    {
        return $this->hasOne(Regional::className(), ['id' => 'regional']);
    }
}
