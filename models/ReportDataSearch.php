<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReportData;

/**
 * ReportDataSearch represents the model behind the search form of `app\models\ReportData`.
 */
class ReportDataSearch extends ReportData
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'symptomp', 'segment', 'regional', 'witel', 'range_day_service', 'login'], 'integer'],
            [['date_open', 'trouble_ticket', 'ncli', 'internet_number', 'pstn', 'datel', 'speed', 'workzone_amcrew', 'amcrew', 'packet', 'status', 'date_closed'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ReportData::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date_open' => $this->date_open,
            'symptomp' => $this->symptomp,
            'segment' => $this->segment,
            'regional' => $this->regional,
            'witel' => $this->witel,
            'date_closed' => $this->date_closed,
            'range_day_service' => $this->range_day_service,
            'login' => $this->login,
        ]);

        $query->andFilterWhere(['like', 'trouble_ticket', $this->trouble_ticket])
            ->andFilterWhere(['like', 'ncli', $this->ncli])
            ->andFilterWhere(['like', 'internet_number', $this->internet_number])
            ->andFilterWhere(['like', 'pstn', $this->pstn])
            ->andFilterWhere(['like', 'datel', $this->datel])
            ->andFilterWhere(['like', 'speed', $this->speed])
            ->andFilterWhere(['like', 'workzone_amcrew', $this->workzone_amcrew])
            ->andFilterWhere(['like', 'amcrew', $this->amcrew])
            ->andFilterWhere(['like', 'packet', $this->packet])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
