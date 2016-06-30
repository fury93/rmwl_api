<?php

namespace console\controllers;

use common\rbac\UserUpdateRule;
use rest\versions\v1\models\User;
use Yii;
use yii\console\Controller;
use \common\rbac\UserGroupRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = \Yii::$app->authManager;
        $authManager->removeAll();

        $userGroupRule = new UserGroupRule();
        $authManager->add($userGroupRule);

        //Add rules
        $ruleUserUpdate = new UserUpdateRule();
        $authManager->add($ruleUserUpdate);

        // Create roles
        $guest = $authManager->createRole(User::ROLE_GUEST);
        $employee = $authManager->createRole(User::ROLE_EMPLOYEE);
        $patient = $authManager->createRole(User::ROLE_PATIENT);
        $admin = $authManager->createRole(User::ROLE_ADMIN);

        $employee->ruleName = $userGroupRule->name;
        $patient->ruleName = $userGroupRule->name;
        $admin->ruleName = $userGroupRule->name;

        $authManager->add($guest);
        $authManager->add($employee);
        $authManager->add($patient);
        $authManager->add($admin);

        //Inherit rules
        $authManager->addChild($admin, $employee);
        $authManager->addChild($admin, $patient);

        //Create permissions for User
        $indexUser = $authManager->createPermission('indexUser');
        $loginUser = $authManager->createPermission('loginUser');
        $logoutUser = $authManager->createPermission('logoutUser');
        $createUser = $authManager->createPermission('createUser');
        $deleteUser = $authManager->createPermission('deleteUser');
        $viewUser = $authManager->createPermission('viewUser');
        $editUser = $authManager->createPermission('editUser');
        $editUser->ruleName = $ruleUserUpdate->name;

        //Add permissions for User
        $authManager->add($indexUser);
        $authManager->add($loginUser);
        $authManager->add($logoutUser);
        $authManager->add($createUser);
        $authManager->add($editUser);
        $authManager->add($deleteUser);
        $authManager->add($viewUser);

        //Guest permission
        $authManager->addChild($guest, $loginUser);


        //Patient permission
        $authManager->addChild($patient, $logoutUser);

        //Employee permission
        $authManager->addChild($employee, $logoutUser);

        //Admin permission
        $authManager->addChild($admin, $logoutUser);
        $authManager->addChild($admin, $indexUser);
        $authManager->addChild($guest, $createUser);
        $authManager->addChild($employee, $editUser);
        $authManager->addChild($admin, $viewUser);
        $authManager->addChild($admin, $deleteUser);
    }

}