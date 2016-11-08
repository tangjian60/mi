<?php

namespace mi\modules\credit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mi\modules\credit\models\MiAdvertising;

/**
 * MiAdvertisingSearch represents the model behind the search form about `mi\modules\credit\models\MiAdvertising`.
 */
class MiAdvertisingSearch extends MiAdvertising
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'type', 'align'], 'integer'],
            [['title', 'content', 'description', 'addtime'], 'safe'],
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
        $query = MiAdvertising::find();

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
            'status' => $this->status,
            'type' => $this->type,
            'align' => $this->align,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'addtime', $this->addtime]);

        return $dataProvider;
    }
}
