<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_count_symptom".
 *
 * @property int $id
 * @property int $login
 * @property int|null $symptom
 * @property string $kmeans_type
 * @property int $reg1
 * @property int $reg2
 * @property int $reg3
 * @property int $reg4
 * @property int $reg5
 * @property int $reg6
 * @property int $reg7
 *
 * @property User $login0
 * @property Symptom $symptom0
 * @property ReportListCentroid[] $reportListCentros
 */
class ReportCountSymptom extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_count_symptom';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'kmeans_type', 'reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7'], 'required'],
            [['login', 'symptom', 'reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7'], 'integer'],
            [['kmeans_type'], 'string'],
            [['date_report'], 'safe'],
            [['login'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['login' => 'id']],
            [['symptom'], 'exist', 'skipOnError' => true, 'targetClass' => Symptom::className(), 'targetAttribute' => ['symptom' => 'id']],
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
            'symptom' => Yii::t('app', 'Symptom'),
            'kmeans_type' => Yii::t('app', 'Kmeans Type'),
            'reg1' => Yii::t('app', 'Reg1'),
            'reg2' => Yii::t('app', 'Reg2'),
            'reg3' => Yii::t('app', 'Reg3'),
            'reg4' => Yii::t('app', 'Reg4'),
            'reg5' => Yii::t('app', 'Reg5'),
            'reg6' => Yii::t('app', 'Reg6'),
            'reg7' => Yii::t('app', 'Reg7'),
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

    /**
     * Gets query for [[Symptom0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSymptom0()
    {
        return $this->hasOne(Symptom::className(), ['id' => 'symptom']);
    }

    /**
     * Gets query for [[ReportListCentros]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReportListCentros()
    {
        return $this->hasMany(ReportListCentroid::className(), ['count_symptom' => 'id']);
    }
}
