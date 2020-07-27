<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_count_cluster".
 *
 * @property int $id
 * @property int $login
 * @property string|null $kmeans_type
 * @property string $start_date
 * @property string $end_date
 * @property int $jumlah_cluster
 * @property string|null $date_report
 * @property int|null $report_by
 *
 * @property User $reportBy
 */
class ReportCountCluster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_count_cluster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'start_date', 'end_date', 'jumlah_cluster'], 'required'],
            [['login', 'jumlah_cluster', 'report_by'], 'integer'],
            [['kmeans_type'], 'string'],
            [['start_date', 'end_date', 'date_report'], 'safe'],
            [['report_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['report_by' => 'id']],
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
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'jumlah_cluster' => Yii::t('app', 'Jumlah Cluster'),
            'date_report' => Yii::t('app', 'Date Report'),
            'report_by' => Yii::t('app', 'Report By'),
        ];
    }

    public function getLogin0()
    {
        return $this->hasOne(User::className(), ['id' => 'login']);
    }
    
    public function getReportBy()
    {
        return $this->hasOne(User::className(), ['id' => 'report_by']);
    }
}
