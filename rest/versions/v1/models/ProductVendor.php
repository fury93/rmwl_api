<?php

namespace rest\versions\v1\models;

use Yii;

/**
 * This is the model class for table "product_vendor".
 *
 * @property integer $product_id
 * @property integer $vendor_id
 * @property integer $total
 *
 * @property Product $product
 * @property Vendor $vendor
 */
class ProductVendor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_vendor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'vendor_id'], 'required'],
            [['product_id', 'vendor_id', 'total'], 'integer'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['vendor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vendor::className(), 'targetAttribute' => ['vendor_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(Vendor::className(), ['id' => 'vendor_id']);
    }
}
