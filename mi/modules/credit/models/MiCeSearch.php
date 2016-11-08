<?php

namespace mi\modules\credit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use mi\modules\credit\models\MiCe;

/**
 * BaoMingxiSearch represents the model behind the search form about `apk\modules\credit\models\BaoMingxi`.
 */
class MiCeSearch extends MiCe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [['browse', 'visitors', 'nvisitors', 'visits'], 'integer'],
            [['ip', 'name', 'region', 'addtime'], 'string', 'max' => 255]
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
    	
        $query = MiCe::find();

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
            'browse' => $this->browse,
            
        ]);
        $query->andFilterWhere(['like', 'addtime', $this->addtime]);
        return $dataProvider;
    }
}
