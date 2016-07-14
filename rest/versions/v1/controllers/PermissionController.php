<?php

namespace rest\versions\v1\controllers;

use rest\versions\v1\helper\ResponseHelper;
use rest\versions\v1\models\Permissions;
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

    //Temporarily all methods from this controller have access without check auth
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['options', 'roles-permission', 'update-permission'],
        ];

        return $behaviors;
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

        $rolesPermissions = Permissions::getPermissionsForRoles($roles, $permissions);
        $modules = Permissions::getPermissionsTemplate($permissions);

        return ResponseHelper::success([
            'modules' => $modules,
            'roles' => $rolesPermissions
        ]);
    }

    public function actionUpdatePermission()
    {
        $newPermission = Yii::$app->request->post('permissions', []);

        Permissions::updatePermissions($newPermission);

        return ResponseHelper::success('');
    }

}