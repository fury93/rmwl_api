<?php

namespace common\rbac;

use rest\versions\v1\models\UserForm;
use yii\rbac\Rule;

/**
 * User can update any users info, if admin, else he can update only him user info
 *
 * Class UserUpdateRule
 * @package common\rbac
 */
class UserUpdateRule extends Rule
{
    public $name = 'isCanUpdateUser';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated width.
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $userEditId = \Yii::$app->getRequest()->get('id');
        $currentRole = \Yii::$app->user->identity->role;

        return ($userEditId == $user || $currentRole == UserForm::ROLE_ADMIN);
    }
}