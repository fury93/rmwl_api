<?php

namespace rest\versions\v1\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $name
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
            [['name', 'expiration_date'], 'required'],
            [['expiration_date', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
