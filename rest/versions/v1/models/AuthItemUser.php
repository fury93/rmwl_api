<?php

namespace rest\versions\v1\models;

use Yii;

/**
 * This is the model class for table "auth_item_user".
 *
 * @property integer $user_id
 * @property string $auth_item
 */
class AuthItemUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'auth_item'], 'required'],
            [['user_id'], 'integer'],
            [['auth_item'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param $userId
     * @return bool
     */
    public static function deleteByUser($userId)
    {
        if ($userId) {
            AuthItemUser::deleteAll('user_id = :userId', [':userId' => $userId]);
        }

        return true;
    }

    /**
     * @param $userId
     * @param $authItem
     * @return bool
     */
    public static function addItem($userId, $authItem)
    {
        $model = new AuthItemUser();
        $model->user_id = $userId;
        $model->auth_item = $authItem;

        $model->save();

        return true;
    }

    /**
     * @param $userId
     * @param $items
     * @return bool
     */
    public static function addManyItems($userId, $items)
    {
        $insertArray = [];

        foreach ($items as $item) {
            $insertArray[] = [
                'user_id' => $userId,
                'auth_item' => $item
            ];
        }

        if (count($insertArray) > 0) {
            $columnNameArray = ['user_id', 'auth_item'];
            $insertCount = Yii::$app->db->createCommand()
                ->batchInsert(
                    self::tableName(), $columnNameArray, $insertArray
                )
                ->execute();
        }

        return true;
    }
}
