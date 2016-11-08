<?php

namespace mi\modules\credit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mi\modules\credit\models\MiBduser;

/**
 * BaoBduserSearch represents the model behind the search form about `apk\modules\credit\models\BaoBduser`.
 */
class BMiBduserSearch extends MiBduser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'create_time', 'updte_time', 'status'], 'integer'],
            [['username', 'pwd', 'phone', 'remarks'], 'safe'],
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
        $query = MiBduser::find();

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
            'group_id' => $this->group_id,
            'create_time' => $this->create_time,
            'updte_time' => $this->updte_time,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'pwd', $this->pwd])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
