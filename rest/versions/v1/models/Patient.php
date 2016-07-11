<?php

namespace rest\versions\v1\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "patient".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property integer $date_birth
 * @property integer $age
 * @property string $marital_status
 * @property string $gender
 * @property string $address
 * @property string $city
 * @property string $state
 * @property integer $zip_code
 * @property string $cell_number
 * @property string $home_number
 * @property string $email
 * @property integer $created_at
 * @property integer $updated_at
 */
class Patient extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'last_name', 'first_name', 'date_birth', 'age', 'marital_status', 'gender', 'address', 'city',
                'state', 'zip_code', 'cell_number', 'home_number', 'email', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'date_birth', 'age', 'zip_code', 'created_at', 'updated_at'], 'integer'],
            [['last_name', 'first_name', 'middle_name', 'marital_status', 'gender', 'address', 'city', 'state',
                'cell_number', 'home_number', 'email'], 'string', 'max' => 255],
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
    public function insertPatient()
    {
        if($this->validate() &&  $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Return all patients
     *
     * @return array
     */
    public static function getPatientsList()
    {
        $patients = Patient::find()->all();

        return $patients;
    }

}
