<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReportCountCluster;

/**
 * ReportCountClusterSearch represents the model behind the search form of `app\models\ReportCountCluster`.
 */
class ReportCountClusterSearch extends ReportCountCluster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'login', 'jumlah_cluster'], 'integer'],
            [['kmeans_type', 'start_date', 'end_date', 'date_report', 'report_by'], 'safe'],
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
        $query = ReportCountCluster::find();

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
        $query->joinWith("reportBy");

        $query->andFilterWhere([
            'id' => $this->id,
            'login' => $this->login,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'jumlah_cluster' => $this->jumlah_cluster,
            // 'date_report' => $this->date_report,
            // 'report_by' => $this->report_by,
        ]);

        $query->andFilterWhere(['like', 'kmeans_type', $this->kmeans_type])
            ->andFilterWhere(['like', 'user.nama_lengkap', $this->report_by])
            ->andFilterWhere(['like', 'date_report', $this->date_report]);

        return $dataProvider;
    }
}
