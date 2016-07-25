<?php

namespace console\controllers;

use common\rbac\UserUpdateRule;
use rest\versions\v1\models\Role;
use Yii;
use yii\console\Controller;
use \common\rbac\UserGroupRule;

class RbacController extends Controller
{
    /**
     * All permissions name will be unique
     * Names are duplicated in Permissions model
     */
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
        $guest = $authManager->createRole(Role::ROLE_GUEST);
        $admin = $authManager->createRole(Role::ROLE_ADMIN);
        $patient = $authManager->createRole(Role::ROLE_PATIENT);
        $entry = $authManager->createRole(Role::ROLE_ENTRY);
        $management = $authManager->createRole(Role::ROLE_MANAGEMENT);
        $inventoryManagement = $authManager->createRole(Role::ROLE_INVENTORY_MANAGEMENT);

        //$guest->ruleName = $userGroupRule->name;
        $admin->ruleName = $userGroupRule->name;
        $patient->ruleName = $userGroupRule->name;
        $entry->ruleName = $userGroupRule->name;
        $management->ruleName = $userGroupRule->name;
        $inventoryManagement->ruleName = $userGroupRule->name;

        $authManager->add($guest);
        $authManager->add($admin);
        $authManager->add($patient);
        $authManager->add($entry);
        $authManager->add($management);
        $authManager->add($inventoryManagement);

        //Inherit rules
        //$authManager->addChild($admin, $employee);
        //$authManager->addChild($admin, $patient);

        //User
        //Auth user
        $indexUser = $authManager->createPermission('indexUser');
        $logoutUser = $authManager->createPermission('logoutUser');
        $createUser = $authManager->createPermission('createUser');
        $deleteUser = $authManager->createPermission('deleteUser');
        $viewUser = $authManager->createPermission('viewUser');
        $editUser = $authManager->createPermission('editUser');
        $editUser->ruleName = $ruleUserUpdate->name;

        //Guest
        $loginUser = $authManager->createPermission('loginUser');
        $checkAuth = $authManager->createPermission('check-authenticationUser');
        $resetPassword = $authManager->createPermission('reset-passwordUser');
        $changePassword = $authManager->createPermission('change-passwordUser');

        //Product
        $indexProduct = $authManager->createPermission('indexProduct');
        $createProduct = $authManager->createPermission('createProduct');
        $deleteProduct = $authManager->createPermission('deleteProduct');
        $viewProduct = $authManager->createPermission('viewProduct');
        $editProduct = $authManager->createPermission('editProduct');

        //Patient
        $indexPatient = $authManager->createPermission('indexPatient');
        $createPatient = $authManager->createPermission('createPatient');
        $deletePatient = $authManager->createPermission('deletePatient');
        $viewPatient = $authManager->createPermission('viewPatient');
        $editPatient = $authManager->createPermission('editPatient');

        //Permission
        $rolesPermission = $authManager->createPermission('roles-permissionPermission');
        $updateRolesPermission = $authManager->createPermission('update-roles-permissionPermission');
        $userPermission = $authManager->createPermission('user-permissionPermission');
        $updateUserPermission = $authManager->createPermission('update-user-permissionPermission');

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

        //Add permission for Permission
        $authManager->add($rolesPermission);
        $authManager->add($updateRolesPermission);
        $authManager->add($userPermission);
        $authManager->add($updateUserPermission);

        //Guest permission
        $authManager->addChild($guest, $loginUser);
        $authManager->addChild($guest, $checkAuth);
        $authManager->addChild($guest, $resetPassword);
        $authManager->addChild($guest, $changePassword);

        //Base permission. This permission you can't change in permission module
        $authManager->addChild($admin, $logoutUser);
        $authManager->addChild($patient, $logoutUser);
        $authManager->addChild($entry, $logoutUser);
        $authManager->addChild($management, $logoutUser);
        $authManager->addChild($inventoryManagement, $logoutUser);

        //Permissions for permission actions
        $authManager->addChild($admin, $rolesPermission);
        $authManager->addChild($admin, $updateRolesPermission);
        $authManager->addChild($admin, $userPermission);
        $authManager->addChild($admin, $updateUserPermission);
        //Get list of user
        $authManager->addChild($admin, $indexUser);

    }

}