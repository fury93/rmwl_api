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
        //$authManager->addChild($admin, $employee);
        //$authManager->addChild($admin, $patient);

        //Create permissions for User
        $indexUser = $authManager->createPermission('indexUser');
        $loginUser = $authManager->createPermission('loginUser');
        $logoutUser = $authManager->createPermission('logoutUser');
        $createUser = $authManager->createPermission('createUser');
        $deleteUser = $authManager->createPermission('deleteUser');
        $viewUser = $authManager->createPermission('viewUser');
        $editUser = $authManager->createPermission('editUser');
        $editUser->ruleName = $ruleUserUpdate->name;
        $checkAuth = $authManager->createPermission('check-authenticationUser');
        $resetPassword = $authManager->createPermission('reset-passwordUser');
        $changePassword = $authManager->createPermission('change-passwordUser');

        //Create permissions for Product
        $indexProduct = $authManager->createPermission('indexProduct');
        $createProduct = $authManager->createPermission('createProduct');
        $deleteProduct = $authManager->createPermission('deleteProduct');
        $viewProduct = $authManager->createPermission('viewProduct');
        $editProduct = $authManager->createPermission('editProduct');

        //Create permissions for Patient
        $indexPatient = $authManager->createPermission('indexPatient');
        $createPatient = $authManager->createPermission('createPatient');
        $deletePatient = $authManager->createPermission('deletePatient');
        $viewPatient = $authManager->createPermission('viewPatient');
        $editPatient = $authManager->createPermission('editPatient');

        //Add permissions for User
        $authManager->add($indexUser);
        $authManager->add($loginUser);
        $authManager->add($logoutUser);
        $authManager->add($createUser);
        $authManager->add($editUser);
        $authManager->add($deleteUser);
        $authManager->add($viewUser);
        $authManager->add($checkAuth);
        $authManager->add($resetPassword);
        $authManager->add($changePassword);

        //Add permissions for Product
        $authManager->add($indexProduct);
        $authManager->add($createProduct);
        $authManager->add($deleteProduct);
        $authManager->add($viewProduct);
        $authManager->add($editProduct);

        //Add permissions for Patient
        $authManager->add($indexPatient);
        $authManager->add($createPatient);
        $authManager->add($deletePatient);
        $authManager->add($viewPatient);
        $authManager->add($editPatient);

        //Guest permission
        $authManager->addChild($guest, $loginUser);
        $authManager->addChild($guest, $checkAuth);
        $authManager->addChild($guest, $resetPassword);
        $authManager->addChild($guest, $changePassword);

        //Patient permission
        //$authManager->addChild($patient, $logoutUser);

        //Employee permission
        //$authManager->addChild($employee, $logoutUser);

        //Admin permission

        //User
        $authManager->addChild($admin, $logoutUser);
        $authManager->addChild($admin, $indexUser);
        $authManager->addChild($admin, $createUser);
        $authManager->addChild($admin, $editUser);
        $authManager->addChild($admin, $viewUser);
        $authManager->addChild($admin, $deleteUser);

        //Products
        $authManager->addChild($admin, $indexProduct);
        $authManager->addChild($admin, $createProduct);
        $authManager->addChild($admin, $editProduct);
        $authManager->addChild($admin, $viewProduct);
        $authManager->addChild($admin, $deleteProduct);

        //Patients
        $authManager->addChild($admin, $indexPatient);
        $authManager->addChild($admin, $createPatient);
        $authManager->addChild($admin, $editPatient);
        $authManager->addChild($admin, $viewPatient);
        $authManager->addChild($admin, $deletePatient);
    }

}