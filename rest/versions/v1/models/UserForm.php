<?php

namespace rest\versions\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property string $role
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserForm extends User
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_LOGIN = 'login';

    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'isEmailUnique'],
            ['email', 'email'],
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'role'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],
            [['username', 'password','email'], 'required', 'on' => self::SCENARIO_REGISTER],
        ];
    }

    /**
     * @return mixed
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = ['email', 'password'];
        $scenarios[self::SCENARIO_REGISTER] = ['username', 'email', 'password'];

        return $scenarios;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function isEmailUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findByEmail($this->email);

            if ($user && $user->id !== $this->id) {
                $this->addError($attribute, 'User with this email already exist');
            }
        }
    }

    public function createUser()
    {
        $this->generateAuthKey();
        /*if(isset($this->password)) {
            $this->setPassword($this->password);
        }*/
        $this->setPassword($this->password);

        if($this->validate() &&  $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Create/update user model
     *
     * @return bool
     */
    public function updateUser()
    {
        $this->setPassword($this->password);

        if($this->validate() &&  $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Return user data after authorization
     *
     * @return array
     */
    public static function prepareUserDate()
    {
        $userData = \Yii::$app->user->identity;

        return [
            'user' => $userData->username,
            'id' => $userData->id,
            'userRole' => $userData->role,
            'token' => $userData->auth_key
        ];
    }
}