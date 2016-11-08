<?php

namespace mi\modules\credit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mi\modules\credit\models\MiOpinion;

/**
 * MiOpinionSearch represents the model behind the search form about `mi\modules\credit\models\MiOpinion`.
 */
class MiOpinionSearch extends MiOpinion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contact'], 'integer'],
            [['content', 'addtime'], 'safe'],
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
        $query = MiOpinion::find();

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
            'contact' => $this->contact,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'addtime', $this->addtime]);

        return $dataProvider;
    }
}
