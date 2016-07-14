<?php

namespace common\rbac;

use rest\versions\v1\models\Role;
use Yii;
use yii\rbac\Rule;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        if (!\Yii::$app->user->isGuest) {
            $group = \Yii::$app->user->identity->role;

            if ($item->name === Role::ROLE_ADMIN) {
                return $group == Role::ROLE_ADMIN;
            } elseif ($item->name === Role::ROLE_MANAGEMENT) {
                return $group == Role::ROLE_ADMIN || $group == Role::ROLE_MANAGEMENT;
            } elseif ($item->name === Role::ROLE_INVENTORY_MANAGEMENT) {
                return $group == Role::ROLE_ADMIN || $group == Role::ROLE_INVENTORY_MANAGEMENT;
            } elseif ($item->name === Role::ROLE_ENTRY) {
                return $group == Role::ROLE_ADMIN || $group == Role::ROLE_MANAGEMENT || $group == Role::ROLE_ENTRY;
            } elseif ($item->name === Role::ROLE_PATIENT) {
                return $group == Role::ROLE_PATIENT;
            }
        }

        return false;
    }
}
