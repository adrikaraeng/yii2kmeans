<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Centroid;

/**
 * CentroidSearch represents the model behind the search form of `app\models\Centroid`.
 */
class CentroidSearch extends Centroid
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'login', 'iterasi', 'cluster'], 'integer'],
            [['reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7', 'id_count_symptom'], 'safe'],
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

    public function searchMax($params,$user,$title)
    {
        $connection = \Yii::$app->db;
        $max_iterasi = $connection->createCommand("SELECT max(iterasi) FROM centroid WHERE login='$user' AND kmeans_type='$title'")->queryScalar();
        // $query = Centroid::find()->where("iterasi='$max_iterasi'");
        $query = Centroid::find()->where("iterasi='1' AND kmeans_type='$title'");

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

        $query->joinWith("symptom");

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'login' => $this->login,
            'iterasi' => $this->iterasi,
            'cluster' => $this->cluster,
        ]);

        $query->andFilterWhere(['like', 'reg1', $this->reg1])
            ->andFilterWhere(['like', 'reg2', $this->reg2])
            ->andFilterWhere(['like', 'reg3', $this->reg3])
            ->andFilterWhere(['like', 'reg4', $this->reg4])
            ->andFilterWhere(['like', 'reg5', $this->reg5])
            ->andFilterWhere(['like', 'reg6', $this->reg6])
            ->andFilterWhere(['like', 'reg7', $this->reg7])
            ->andFilterWhere(['like', 'symptom.symptom', $this->id_count_symptom]);

        return $dataProvider;   
    }
    
    public function search($params)
    {
        $query = Centroid::find();

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
            'login' => $this->login,
            'iterasi' => $this->iterasi,
            'cluster' => $this->cluster,
        ]);

        $query->andFilterWhere(['like', 'reg1', $this->reg1])
            ->andFilterWhere(['like', 'reg2', $this->reg2])
            ->andFilterWhere(['like', 'reg3', $this->reg3])
            ->andFilterWhere(['like', 'reg4', $this->reg4])
            ->andFilterWhere(['like', 'reg5', $this->reg5])
            ->andFilterWhere(['like', 'reg6', $this->reg6])
            ->andFilterWhere(['like', 'reg7', $this->reg7]);

        return $dataProvider;
    }
}
