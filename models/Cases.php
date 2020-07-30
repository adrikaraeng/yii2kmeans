<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cases".
 *
 * @property int $id
 * @property string|null $date_open
 * @property string|null $trouble_ticket
 * @property int|null $symptomp
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
 * @property int|null $login
 *
 * @property Regional $regional0
 * @property Symptom $symptomp0
 * @property Witel $witel0
 */
class Cases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_open', 'date_closed', 'range_day_service', 'val_uniqx', 'val_uniqy'], 'safe'],
            [['symptomp', 'segment', 'regional', 'witel', 'login', 'range_day_service'], 'integer'],
            [['packet', 'status'], 'string'],
            [['trouble_ticket', 'internet_number', 'speed'], 'string', 'max' => 15],
            [['ncli'], 'string', 'max' => 10],
            [['pstn'], 'string', 'max' => 14],
            [['datel', 'workzone_amcrew', 'amcrew'], 'string', 'max' => 200],
            [['regional'], 'exist', 'skipOnError' => true, 'targetClass' => Regional::className(), 'targetAttribute' => ['regional' => 'id']],
            [['symptomp', 'segment'], 'exist', 'skipOnError' => true, 'targetClass' => Symptom::className(), 'targetAttribute' => ['symptomp' => 'id']],
            [['witel'], 'exist', 'skipOnError' => true, 'targetClass' => Witel::className(), 'targetAttribute' => ['witel' => 'id']],
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
            'login' => Yii::t('app', 'Login'),
            'range_day_service' => Yii::t('app', 'Status Service'),
        ];
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

    /**
     * Gets query for [[Symptomp0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSymptomp0()
    {
        return $this->hasOne(Symptom::className(), ['id' => 'symptomp']);
    }

    public function getSegment0()
    {
        return $this->hasOne(Segment::className(), ['id' => 'segment']);
    }

    public function getWitel0()
    {
        return $this->hasOne(Witel::className(), ['id' => 'witel']);
    }
}
