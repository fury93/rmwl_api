<?php

namespace rest\versions\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $access_token
 * @property string $token_expire
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REGISTER = 2;

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
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return mixed
     */
    public function getForbiddenActions()
    {
        return $this->hasMany(AuthItemUser::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $identity = static::findOne(['access_token' => $token]);

        if (!$identity || !$identity->token_expire || $identity->token_expire <= time()) {
            $identity = null;
        }

        if ($identity && self::isChangeTokenExpire($identity->token_expire)) {
            $identity->generateTokenExpireDate();
            $identity->save();
        }

        return $identity;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * Clear field password_reset_token for user if user set new password
     */
    public function clearPasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->access_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        //todo temp
        if (!$password) {
            $password = 'test';
        }
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @param bool $rememberMe
     */
    public function generateTokenExpireDate($rememberMe = false)
    {
        $tokenExpire = $rememberMe ?
            Yii::$app->params['user.expireSecondsRemember'] : Yii::$app->params['user.expireSecondsNotRemember'];

        $this->token_expire = time() + $tokenExpire;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
        if (($request->isPut || $request->isDelete || $request->isPost)) {
            return [Yii::$app->params['maxRateLimit'], Yii::$app->params['perRateLimit']];
        }

        return [Yii::$app->params['maxGetRateLimit'], Yii::$app->params['perGetRateLimit']];
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
        return [
            \Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_remaining'),
            \Yii::$app->cache->get($request->getPathInfo() . $request->getMethod() . '_ts')
        ];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        \Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_remaining', $allowance);
        \Yii::$app->cache->set($request->getPathInfo() . $request->getMethod() . '_ts', $timestamp);
    }

    /**
     * @return bool
     */
    public function removeAccessToken()
    {
        $this->access_token = null;
        $this->token_expire = null;
        $this->save(false);

        return true;
    }

    /**
     * @param bool $rememberMe
     * @return bool
     */
    public function updateAccessToken($rememberMe = false)
    {
        $this->generateAccessToken();
        $this->generateTokenExpireDate($rememberMe);
        $this->save(false);

        return true;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public static function isChangeTokenExpire($tokenExpire)
    {
        //Use sliding expiration approach and update token expire by any request
        //But if user set 'remember me', then token not update, while current time > token
        // expire time + expire config param (1 hour default)
        if ($tokenExpire - Yii::$app->params['user.expireSecondsNotRemember'] <= time()) {
            return true;
        }

        return false;
    }
}