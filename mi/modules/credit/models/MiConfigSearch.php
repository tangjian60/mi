<?php

namespace mi\modules\credit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mi\modules\credit\models\MiConfig;

/**
 * MiConfigSearch represents the model behind the search form about `mi\modules\credit\models\MiConfig`.
 */
class MiConfigSearch extends MiConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pingoff', 'postovertime', 'bookoff', 'mood', 'ishits', 'iscopyfrom', 'isauthor', 'artlistnum'], 'integer'],
            [['sitetitle', 'sitetitle2', 'sitedescription', 'siteurl', 'sitekeywords', 'sitetcp', 'sitelx', 'sitelogo'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = MiConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pingoff' => $this->pingoff,
            'postovertime' => $this->postovertime,
            'bookoff' => $this->bookoff,
            'mood' => $this->mood,
            'ishits' => $this->ishits,
            'iscopyfrom' => $this->iscopyfrom,
            'isauthor' => $this->isauthor,
            'artlistnum' => $this->artlistnum,
        ]);

        $query->andFilterWhere(['like', 'sitetitle', $this->sitetitle])
            ->andFilterWhere(['like', 'sitetitle2', $this->sitetitle2])
            ->andFilterWhere(['like', 'sitedescription', $this->sitedescription])
            ->andFilterWhere(['like', 'siteurl', $this->siteurl])
            ->andFilterWhere(['like', 'sitekeywords', $this->sitekeywords])
            ->andFilterWhere(['like', 'sitetcp', $this->sitetcp])
            ->andFilterWhere(['like', 'sitelx', $this->sitelx])
            ->andFilterWhere(['like', 'sitelogo', $this->sitelogo]);

        return $dataProvider;
    }
}
