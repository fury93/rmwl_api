<?php

namespace rest\versions\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vendor".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $description
 * @property string $contact_info
 * @property string $status
 * @property string $image_path
 * @property integer $start_date
 * @property integer $end_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class Vendor extends ActiveRecord
{
    const VENDOR_STATUS_ACTIVE = 'Active';
    const VENDOR_STATUS_EXPIRED = 'Expired';
    const VENDOR_STATUS_HOLD = 'Hold';
    const VENDOR_STATUS_OUT_OF_STOCK = 'Out of stock';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vendor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'status'], 'required'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'integer'],
            [['name', 'address', 'description', 'contact_info', 'status', 'image_path'], 'string', 'max' => 255],
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
     * @return \yii\db\ActiveQuery
     */
    public function getProductVendors()
    {
        return $this->hasMany(ProductVendor::className(), ['vendor_id' => 'id']);
    }

    /**
     * Return array with all vendor statuses
     *
     * @return array
     */
    public static function getVendorsStatuses()
    {
        return [
            self::VENDOR_STATUS_ACTIVE,
            self::VENDOR_STATUS_EXPIRED,
            self::VENDOR_STATUS_HOLD,
            self::VENDOR_STATUS_OUT_OF_STOCK,
        ];
    }

    /**
     * Add new or update existing location
     * @return bool
     */
    public function insertVendor()
    {
        if($this->validate() &&  $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Return all locations
     *
     * @return array
     */
    public static function getVendorsList()
    {
        $vendors = Vendor::find()->all();

        return $vendors;
    }

}
