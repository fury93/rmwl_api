<?php

namespace rest\versions\v1\controllers;

use rest\versions\v1\helper\ActionsHelper;
use rest\versions\v1\helper\ResponseHelper;
use rest\versions\v1\models\Permissions;
use rest\versions\v1\models\User;
use Yii;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\rbac\Role;
use yii\rbac\Permission;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\filters\auth\HttpBearerAuth;

class PermissionController extends ActiveController
{
    public $modelClass = 'rest\versions\v1\models\Permissions';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['options'],
        ];

        return $behaviors;
    }

    /**
     * @param $action
     * @return bool
     * @throws \rest\versions\v1\helper\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        return parent::beforeAction($action) ? ActionsHelper::ifActionAccess($action) : false;
    }

    /**
     * Return List with permissions by modules
     *
     * @return array
     */
    public function actionRolesPermission()
    {
        $permissions = Permissions::getAllPermissions();
        $roles = \rest\versions\v1\models\Role::getRolesList();

        $rolesPermissions = Permissions::getPermissionsForRoles($roles);
        $modules = Permissions::getPermissionsTemplate($permissions);

        return ResponseHelper::success([
            'modules' => $modules,
            'roles' => $rolesPermissions
        ]);
    }

    /**
     * Update permission for roles (table auth_item_child)
     *
     * @return array
     */
    public function actionUpdateRolesPermission()
    {
        $newPermission = Yii::$app->request->post('permissions', []);
        Permissions::updatePermissions($newPermission);

        return ResponseHelper::success('');
    }

    /**
     * Return permissions for user
     *
     * @param $id
     * @return array
     */
    public function actionUserPermission($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return ResponseHelper::failed('User not found');
        }

        $rolePermissions = array_keys(Yii::$app->authManager->getPermissionsByRole($user->role));

        $permissions = Permissions::getAllPermissions();
        $modules = Permissions::getPermissionsTemplate($permissions, $rolePermissions);

        $userPermission = Permissions::getUserPermissions($rolePermissions, $user);

        return ResponseHelper::success([
            'permission' => $userPermission,
            'modules' => $modules,
            'user' => $user->username
        ]);
    }

    /**
     * Update permission for user
     *
     * @param $id
     * @return array
     */
    public function actionUpdateUserPermission($id)
    {
        $newPermission = Yii::$app->request->post('permissions', []);

        Permissions::updateForbiddenPermissions($newPermission, $id);

        return ResponseHelper::success('');
    }

}