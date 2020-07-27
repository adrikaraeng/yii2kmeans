<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_data".
 *
 * @property int $id
 * @property string|null $date_open
 * @property string|null $trouble_ticket
 * @property int|null $symptomp
 * @property int|null $segment
 * @property string|null $ncli
 * @property string|null $internet_number
 * @property string|null $pstn
 * @property int|null $regional
 * @property int|null $witel
 * @property string|null $datel
 * @property string|null $speed
 * @property string|null $workzone_amcrew
 * @property string|null $amcrew
 * @property string|null $packet
 * @property string|null $status
 * @property string|null $date_closed
 * @property int|null $range_day_service
 * @property int|null $login
 *
 * @property User $login0
 * @property Segment $segment0
 * @property Symptom $symptomp0
 */
class ReportData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_open', 'date_closed'], 'safe'],
            [['symptomp', 'segment', 'regional', 'witel', 'range_day_service', 'login'], 'integer'],
            [['packet', 'status'], 'string'],
            [['trouble_ticket', 'internet_number', 'speed'], 'string', 'max' => 15],
            [['ncli'], 'string', 'max' => 10],
            [['pstn'], 'string', 'max' => 14],
            [['datel', 'workzone_amcrew', 'amcrew'], 'string', 'max' => 200],
            [['login'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['login' => 'id']],
            [['segment'], 'exist', 'skipOnError' => true, 'targetClass' => Segment::className(), 'targetAttribute' => ['segment' => 'id']],
            [['symptomp'], 'exist', 'skipOnError' => true, 'targetClass' => Symptom::className(), 'targetAttribute' => ['symptomp' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date_open' => Yii::t('app', 'Date Open'),
            'trouble_ticket' => Yii::t('app', 'Trouble Ticket'),
            'symptomp' => Yii::t('app', 'Symptomp'),
            'segment' => Yii::t('app', 'Segment'),
            'ncli' => Yii::t('app', 'Ncli'),
            'internet_number' => Yii::t('app', 'Internet Number'),
            'pstn' => Yii::t('app', 'Pstn'),
            'regional' => Yii::t('app', 'Regional'),
            'witel' => Yii::t('app', 'Witel'),
            'datel' => Yii::t('app', 'Datel'),
            'speed' => Yii::t('app', 'Speed'),
            'workzone_amcrew' => Yii::t('app', 'Workzone Amcrew'),
            'amcrew' => Yii::t('app', 'Amcrew'),
            'packet' => Yii::t('app', 'Packet'),
            'status' => Yii::t('app', 'Status'),
            'date_closed' => Yii::t('app', 'Date Closed'),
            'range_day_service' => Yii::t('app', 'Range Day Service'),
            'login' => Yii::t('app', 'Login'),
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
     * Gets query for [[Segment0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSegment0()
    {
        return $this->hasOne(Segment::className(), ['id' => 'segment']);
    }

    /**
     * Gets query for [[Symptomp0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSymptomp0()
    {
        return $this->hasOne(Symptom::className(), ['id' => 'symptomp']);
    }
}
