<?php

namespace rest\versions\v1\models;

use rest\versions\v1\helper\FormatHelper;
use rest\versions\v1\helper\ResponseHelper;
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
        return 'product';
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
            [
                ['name', 'description', 'status', 'unit_of_measure', 'product_class', 'uom', 'image_path'],
                'string',
                'max' => 255
            ],
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

    public function fields()
    {
        return [
            'id',
            'vendor_id',
            'name',
            'code',
            'description',
            'status',
            'unit_of_measure',
            'product_class',
            'uom',
            'cost',
            'cost_per_unit',
            'price_per_unit',
            'image_path',
            'effective_date' => function () {
                return date('m/d/y', $this->effective_date);
            },
            'expiration_date' => function () {
                return date('m/d/y', $this->expiration_date);
            }
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductLocations()
    {
        return $this->hasMany(ProductLocation::className(), ['product_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductVendors()
    {
        return $this->hasMany(ProductVendor::className(), ['product_id' => 'id']);
    }

    /**
     * Add new or update existing product
     * @return bool
     */
    public function insertProduct()
    {
        $this->expiration_date = FormatHelper::toTimestamp($this->expiration_date);
        $this->effective_date = FormatHelper::toTimestamp($this->effective_date);

        if ($this->validate() && $this->save()) {
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
