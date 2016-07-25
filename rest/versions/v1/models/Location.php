<?php

namespace rest\versions\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "location".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $description
 * @property string $lat
 * @property string $lon
 * @property string $cell_number
 * @property integer $created_at
 * @property integer $updated_at
 */
class Location extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'address', 'description', 'lat', 'lon', 'cell_number'], 'string', 'max' => 255],
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
     * Add new or update existing location
     * @return bool
     */
    public function insertLocation()
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
    public static function getLocationsList()
    {
        $locations = Location::find()->all();

        return $locations;
    }
}
