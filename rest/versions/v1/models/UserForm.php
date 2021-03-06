<?php

namespace rest\versions\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $access_token
 * @property string $token_expire
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
//    const SCENARIO_REGISTER = 'register';
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
            ['username', 'unique'],
            ['email', 'email'],
            [['username', 'password_hash', 'email', 'role'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'role'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 32],
            [['username', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],
        ];
    }

    /**
     * @return mixed
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = ['email', 'password'];
//        $scenarios[self::SCENARIO_REGISTER] = ['username', 'email', 'password'];

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

    /**
     * @return bool
     */
    public function createUser()
    {
        $this->generateAccessToken();
        $this->setPassword($this->password);

        if ($this->validate() && $this->save()) {
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

        if ($this->validate() && $this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Return base user configurations and constant information for application
     *
     * @return array
     */
    public static function getConfigurations($userData = null)
    {
        if (!$userData) {
            $userData = \Yii::$app->user->identity;
        }

        $userConfigs = self::getUserConfiguration($userData);
        $roles = Role::getRolesValues();
        $vendorStatus = Vendor::getVendorsStatuses();
        $locations = Location::getLocationsListSelect();

        return [
            'user' => $userConfigs,
            'roles' => $roles,
            'vendorStatus' => $vendorStatus,
            'locations' => $locations
        ];
    }

    /**
     * Return information about user
     *
     * @param $userData
     * @return array
     */
    public static function getUserConfiguration($userData)
    {
        return [
            'user' => $userData->username,
            'id' => $userData->id,
            'role' => $userData->role,
            'token' => $userData->access_token,
            'email' => $userData->email
        ];
    }

    /**
     * Return user data for table
     *
     * @return array
     */
    public static function getUsersList()
    {
        $usersData = [];
/*        $users = UserForm::findAll([
            'status' => self::STATUS_ACTIVE
        ]);*/
        $users = UserForm::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orWhere(['status' => self::STATUS_REGISTER])
            ->all();

        foreach ($users as $user) {
            array_push($usersData, self::filterUserData($user));
        }

        return $usersData;
    }

    /**
     * Filter user data (token, password and another fields)
     *
     * @param $user
     * @return array
     */
    public static function filterUserData($user)
    {
        $userInfo = [
            'username' => $user->username,
            'id' => $user->id,
            'role' => $user->role,
            'email' => $user->email
        ];

        return $userInfo;
    }

    /**
     * Get users list in format value => label
     *
     * @return mixed
     */
    public static function getUsersListSelect()
    {
        $users = UserForm::find()
            ->select('id as value, username as label')
            ->where(['status' => self::STATUS_ACTIVE])
            ->asArray()
            ->all();

        return $users;
    }
}