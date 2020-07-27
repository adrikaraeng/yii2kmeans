<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_centroid".
 *
 * @property int $id
 * @property int $login
 * @property string|null $kmeans_type
 * @property string $reg1
 * @property string $reg2
 * @property string $reg3
 * @property string $reg4
 * @property string $reg5
 * @property string $reg6
 * @property string $reg7
 * @property int $iterasi
 * @property int $cluster
 * @property int|null $id_count_symptom
 * @property string|null $date_report
 */
class ReportCentroid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_centroid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7', 'iterasi', 'cluster'], 'required'],
            [['login', 'iterasi', 'cluster', 'id_count_symptom'], 'integer'],
            [['kmeans_type'], 'string'],
            [['date_report'], 'safe'],
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
            'kmeans_type' => Yii::t('app', 'Kmeans Type'),
            'reg1' => Yii::t('app', 'Reg1'),
            'reg2' => Yii::t('app', 'Reg2'),
            'reg3' => Yii::t('app', 'Reg3'),
            'reg4' => Yii::t('app', 'Reg4'),
            'reg5' => Yii::t('app', 'Reg5'),
            'reg6' => Yii::t('app', 'Reg6'),
            'reg7' => Yii::t('app', 'Reg7'),
            'iterasi' => Yii::t('app', 'Iterasi'),
            'cluster' => Yii::t('app', 'Cluster'),
            'id_count_symptom' => Yii::t('app', 'Id Count Symptom'),
            'date_report' => Yii::t('app', 'Date Report'),
        ];
    }
}
