<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CountSymptom;

/**
 * CountsymptomSearch represents the model behind the search form of `app\models\CountSymptom`.
 */
class CountsymptomSearch extends CountSymptom
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'login', 'reg1', 'reg2', 'reg3', 'reg4', 'reg5', 'reg6', 'reg7'], 'integer'],
            [['kmeans_type', 'symptom'], 'safe'],
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
        $query = CountSymptom::find()->where("kmeans_type='symtomp'");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->joinWith('symptom0');

        $query->andFilterWhere([
          'id' => $this->id,
          'login' => $this->login,
          // 'symptom' => $this->symptom,
          'reg1' => $this->reg1,
          'reg2' => $this->reg2,
          'reg3' => $this->reg3,
          'reg4' => $this->reg4,
          'reg5' => $this->reg5,
          'reg6' => $this->reg6,
          'reg7' => $this->reg7,
        ]);

        $query->andFilterWhere(['like', 'kmeans_type', $this->kmeans_type])
        ->andFilterWhere(['like', 'symptom.symptom', $this->symptom]);

        return $dataProvider;
    }

    public function searchTeknisi($params)
    {
        $query = CountSymptom::find()->where("kmeans_type='teknisi'");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->joinWith('symptom0');

        $query->andFilterWhere([
          'id' => $this->id,
          'login' => $this->login,
          // 'symptom' => $this->symptom,
          'reg1' => $this->reg1,
          'reg2' => $this->reg2,
          'reg3' => $this->reg3,
          'reg4' => $this->reg4,
          'reg5' => $this->reg5,
          'reg6' => $this->reg6,
          'reg7' => $this->reg7,
        ]);

        $query->andFilterWhere(['like', 'kmeans_type', $this->kmeans_type])
        ->andFilterWhere(['like', 'symptom.symptom', $this->symptom]);

        return $dataProvider;
    }
}
