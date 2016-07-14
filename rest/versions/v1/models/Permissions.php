<?php

namespace rest\versions\v1\models;

use Yii;
use yii\helpers\ArrayHelper;

class Permissions
{
    const MODULE_POS = 'POS';
    const MODULE_PIM = 'PIM';
    const MODULE_EMR = 'EMR';
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
                'indexProduct' => 'indexProduct',
                'createProduct' => 'createProduct',
                'editProduct' => 'editProduct',
                'viewProduct' => 'viewProduct',
                'deleteProduct' => 'deleteProduct',
            ],
            self::MODULE_EMR => [
                'indexPatient' => 'indexPatient',
                'createPatient' => 'createPatient',
                'editPatient' => 'editPatient',
                'viewPatient' => 'viewPatient',
                'deletePatient' => 'deletePatient',
            ],
            self::MODULE_POS => [],
            self::MODULE_REPORTING => []
        ];

        return $permissions;
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

    public static function getPermissionsForRoles($roles, $modulesPermissions)
    {
        $rolesPermission = [];

        foreach ($roles as $roleName => $role) {
            //$modulesResult = [];
            $roleChecked = array_keys(Yii::$app->authManager->getPermissionsByRole($role));

            /*foreach($modulesPermissions as $moduleName => $modulePermissions) {
                $moduleResult = [];

                foreach($modulePermissions as $permission) {
                    $data = [
                        'key' => $permission,
                        'value' => $permission, //add description
                        //'status' => in_array($permission, $roleChecked) ? true : false
                    ];

                    array_push($moduleResult, $data);
                }

                $modulesResult[self::MODULES_FIELD][$moduleName] = $moduleResult;
                $modulesResult[self::SELECTED_FIELD] = $roleChecked;
            }*/

            $rolesPermission[$roleName] = $roleChecked;
        }

        return $rolesPermission;
    }

    /**
     * @param $modulesPermissions
     * @return array
     */
    public static function getPermissionsTemplate($modulesPermissions)
    {
        $template = [];

        foreach ($modulesPermissions as $moduleName => $modulePermissions) {
            $moduleResult = [];

            foreach ($modulePermissions as $keyPermission => $descriptionPermission) {
                $data = [
                    'value' => $keyPermission,
                    'name' => $descriptionPermission, //add description
                ];

                array_push($moduleResult, $data);
            }

            $template[$moduleName] = $moduleResult;
        }

        return $template;
    }

    public static function ifAllowedPermission()
    {
        //todo
        /*$premissions = Yii::$app->authManager->getPermissions();
        $roles = Yii::$app->authManager->getRoles();
        $userRole = Yii::$app->authManager->getRolesByUser(1);
        //$permissions = ArrayHelper::map(Yii::$app->authManager->getPermissions(), 'name', 'description');
        $role_permit = array_keys(Yii::$app->authManager->getPermissionsByRole('Guest'));
        $guestChild = Yii::$app->authManager->getChildren('Guest');*/
    }

    /**
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
                    Yii::$app->authManager->removeChild($role, $permission);
                }
            }
        }

        return true;
    }

}