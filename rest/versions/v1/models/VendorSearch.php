<?php

namespace rest\versions\v1\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rest\versions\v1\models\Vendor;

/**
 * VendorSearch represents the model behind the search form about `rest\versions\v1\models\Vendor`.
 */
class VendorSearch extends Vendor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'start_date', 'end_date', 'created_at', 'updated_at'], 'integer'],
            [['name', 'address', 'description', 'contact_info', 'status', 'image_path'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Vendor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'contact_info', $this->contact_info])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
