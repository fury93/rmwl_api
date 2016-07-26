<?php

namespace rest\versions\v1\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rest\versions\v1\models\Product;

/**
 * ProductSearch represents the model behind the search form about `rest\versions\v1\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'vendor_id', 'code', 'effective_date', 'expiration_date', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description', 'status', 'unit_of_measure', 'product_class', 'uom', 'image_path'], 'safe'],
            [['cost', 'cost_per_unit', 'price_per_unit'], 'number'],
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
        $query = Product::find();

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
            'vendor_id' => $this->vendor_id,
            'code' => $this->code,
            'cost' => $this->cost,
            'cost_per_unit' => $this->cost_per_unit,
            'price_per_unit' => $this->price_per_unit,
            'effective_date' => $this->effective_date,
            'expiration_date' => $this->expiration_date
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'unit_of_measure', $this->unit_of_measure])
            ->andFilterWhere(['like', 'product_class', $this->product_class])
            ->andFilterWhere(['like', 'uom', $this->uom]);

        return $dataProvider;
    }
}
