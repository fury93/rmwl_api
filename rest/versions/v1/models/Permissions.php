<?php

namespace rest\versions\v1\models;

use Yii;
use yii\helpers\ArrayHelper;

class Permissions
{
    const MODULE_POS = 'POS';
    const MODULE_PIM = 'PIM';
    const MODULE_EMR = 'EMR';
    const MODULE_ADMIN_TOOLS = 'ADMIN TOOLS';
    const MODULE_REPORTING = 'REPORTING';

    const SELECTED_FIELD = 'active';
    const MODULES_FIELD = 'modules';

    /**
     * @return array
     */
    public static function getModulesList()
    {
        return [
            self::MODULE_POS => self::MODULE_POS,
            self::MODULE_PIM => self::MODULE_PIM,
            self::MODULE_EMR => self::MODULE_EMR,
            self::MODULE_REPORTING => self::MODULE_REPORTING,
        ];
    }

    /**
     * Get list with all permissions for modules
     * Important!!! The permissions names must be unique.
     * It's necessary to modify the binding to the modules.
     *
     * @return array
     */
    public static function getAllPermissions()
    {
        $permissions = [
            self::MODULE_PIM => [
                'indexProduct' => 'Product Page',
                'createProduct' => 'Product Create',
                'editProduct' => 'Product Edit',
                'viewProduct' => 'Product View',
                'deleteProduct' => 'Product Delete',
            ],
            self::MODULE_EMR => [
                'indexPatient' => 'Patient Page',
                'createPatient' => 'Patient Create',
                'editPatient' => 'Patient Edit',
                'viewPatient' => 'Patient View',
                'deletePatient' => 'Patient Delete',
            ],
            self::MODULE_ADMIN_TOOLS => [
                'indexUser' => 'Users Page',
                'createUser' => 'User Create',
                'editUser' => 'User Edit',
                'viewUser' => 'User View',
                'deleteUser' => 'User Delete',

                'roles-permissionPermission' => 'Roles Permission Page',
                'update-roles-permissionPermission' => 'Update Roles Permission',
                'user-permissionPermission' => 'User Permission Page',
                'update-user-permissionPermission' => 'Update User Permission',
            ],
            self::MODULE_POS => [],
            self::MODULE_REPORTING => []
        ];

        return $permissions;
    }

    /**
     * This permissions will be able for all auth users (all roles)
     *
     * @return array
     */
    public static function getAvailablePermission()
    {
        return [
            'users-listUser'
        ];
    }

    /**
     * Array with permissions, which will be deleted from roles/user permissions list before will be send to client
     *
     * @return array
     */
    public static function getAdditionalPermission()
    {
        return [
            'loginUser',
        ];
    }

    /**
     * @return array
     */
    public static function getGuestPermission()
    {
        return [
            'check-authenticationUser',
            'reset-passwordUser',
            'change-passwordUser'
        ];
    }

    /**
     * Get permissions by module name
     *
     * @param $module
     * @return array
     */
    public static function getPermissionByModule($module)
    {
        $permissions = self::getAllPermissions();

        if (isset($permissions[$module])) {
            return $permissions[$module];
        }

        return [];
    }

    /**
     * Return active permission for roles (auth_item_child table)
     *
     * @param $roles
     * @return array
     */
    public static function getPermissionsForRoles($roles)
    {
        $rolesPermission = [];

        foreach ($roles as $roleName => $role) {
            $roleChecked = array_keys(Yii::$app->authManager->getPermissionsByRole($role));
            //todo perhaps need delete in array base action, like login
            $rolesPermission[$roleName] = $roleChecked;
        }

        return $rolesPermission;
    }

    /**
     * Return array with permissions value/description by module
     * If 2 parameter $rolePermission not empty, then delete from modules permissions which not in this array
     * It used when need get permission for user, not roles
     *
     * @param $modulesPermissions
     * @param $rolePermissions
     *
     * @return array
     */
    public static function getPermissionsTemplate($modulesPermissions, $rolePermissions = [])
    {
        $template = [];

        foreach ($modulesPermissions as $moduleName => $modulePermissions) {
            $moduleResult = [];

            foreach ($modulePermissions as $keyPermission => $descriptionPermission) {
                if (!$rolePermissions || in_array($keyPermission, $rolePermissions)) {
                    $data = [
                        'value' => $keyPermission,
                        'name' => $descriptionPermission,
                    ];

                    array_push($moduleResult, $data);
                }
            }

            $template[$moduleName] = $moduleResult;
        }

        return $template;
    }

    /**
     * Update permissions for roles
     *
     * @param $newPermission
     * @return bool
     */
    public static function updatePermissions($newPermission)
    {
        $allPermissions = ArrayHelper::map(Yii::$app->authManager->getPermissions(), 'name', 'description');

        foreach ($newPermission as $roleName => $rolePermission) {
            $role = Yii::$app->authManager->getRole($roleName);

            foreach ($allPermissions as $permit => $description) {
                $permission = Yii::$app->authManager->getPermission($permit);

                if (in_array($permit, $rolePermission)) {
                    if (!Yii::$app->authManager->hasChild($role, $permission)) {
                        Yii::$app->authManager->addChild($role, $permission);
                    }
                } elseif (Yii::$app->authManager->hasChild($role, $permission)) {
                    if (!in_array($permit, self::getAdditionalPermission())) {
                        Yii::$app->authManager->removeChild($role, $permission);
                    }
                }
            }
        }

        return true;
    }

    /**
     *  Update permissions for user
     *
     * @param $newPermission
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public static function updateForbiddenPermissions($newPermission, $userId)
    {
        $user = User::findIdentity($userId);
        $rolePermissions = array_keys(Yii::$app->authManager->getPermissionsByRole($user->role));
        $needForbidden = array_diff($rolePermissions, $newPermission);
        $connection = \Yii::$app->db;

        $transaction = $connection->beginTransaction();
        try {
            AuthItemUser::deleteByUser($userId);
            AuthItemUser::addManyItems($userId, $needForbidden);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Checks, if action forbidden for current user
     * Prohibited action in table auth_item_user
     *
     * @param $action
     * @return bool
     */
    public static function isForbiddenActionForUser($action)
    {
        $forbidden = self::getForbiddenActionForUser();

        if (in_array($action, $forbidden)) {
            return true;
        }

        return false;
    }

    /**
     * Return list wit forbidden actions
     *
     * @param null $user
     * @return array
     */
    public static function getForbiddenActionForUser($user = null)
    {
        $forbidden = [];

        if (!$user) {
            $user = \Yii::$app->user->identity;
        }

        if ($user) {
            $result = $user->getForbiddenActions()->asArray()->all();
            $forbidden = ArrayHelper::getColumn($result, 'auth_item');
        }

        return $forbidden;
    }

    /**
     * Return List permissions for current user
     *
     * @param $rolePermission
     * @param $user
     * @return array
     */
    public static function getUserPermissions($rolePermission, $user)
    {
        $forbiddenPermission = Permissions::getForbiddenActionForUser($user);
        $userHaveAccess = array_diff($rolePermission, $forbiddenPermission);

        return array_values($userHaveAccess);
    }

}