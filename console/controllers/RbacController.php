<?php

namespace console\controllers;

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

        $authManager->addChild($admin, $employee);
        $authManager->addChild($admin, $patient);

        //Create permissions
        $loginUser = $authManager->createPermission('loginUser');

        //Add permissions
        $authManager->add($loginUser);

        //Guest permission
        $authManager->addChild($guest, $loginUser);


        //test permission
        /*        $createPost = $authManager->createPermission('createPost');
                $createPost->description = 'Create a post';
                $authManager->add($createPost);

                $updatePost = $authManager->createPermission('updatePost');
                $updatePost->description = 'Update post';
                $authManager->add($updatePost);

                $authManager->addChild($admin, $updatePost);
                $authManager->addChild($admin, $employee);*/

        //$authManager->assign($admin, 1);
    }

}