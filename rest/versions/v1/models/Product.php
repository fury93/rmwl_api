<?php

namespace rest\versions\v1\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property integer $vendor_id
 * @property string $name
 * @property integer $code
 * @property string $description
 * @property string $status
 * @property string $unit_of_measure
 * @property string $product_class
 * @property string $uom
 * @property double $cost
 * @property double $cost_per_unit
 * @property double $price_per_unit
 * @property string $image_path
 * @property integer $effective_date
 * @property integer $expiration_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class Product extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor_id', 'name', 'code', 'status', 'cost', 'effective_date', 'expiration_date'], 'required'],
            [['vendor_id', 'code', 'effective_date', 'expiration_date', 'created_at', 'updated_at'], 'integer'],
            [['cost', 'cost_per_unit', 'price_per_unit'], 'number'],
            [['name', 'description', 'status', 'unit_of_measure', 'product_class', 'uom', 'image_path'],
                'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Add new or update existing product
     * @return bool
     */
    public function insertProduct()
    {
        if($this->validate() &&  $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Return all products
     *
     * @return array
     */
    public static function getProductsList()
    {
        $products = Product::find()->all();

        return $products;
    }

}
