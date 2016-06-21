<?php
namespace rest\versions\v1\controllers;

use rest\versions\v1\models\LoginForm;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\QueryParamAuth;

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends Controller
{
    public $modelClass = 'rest\versions\v1\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'except' => ['login', 'register', 'password-restore', 'check-authentication']
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $controller = \Yii::$app->controller->id;

        if (parent::beforeAction($action)) {
            if (!\Yii::$app->user->can($action->id . ucfirst($controller))) {
                throw new ForbiddenHttpException('Access denied ' . $action->id . ucfirst($controller));
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * This method implemented to demonstrate the receipt of the token.
     * Do not use it on production systems.
     * @return string AuthKey or model with errors
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return \Yii::$app->user->identity->getAuthKey();
        } else {
            return $model;
        }
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        $user = Yii::$app->user->identity;
        $user->removeAccessToken();

        return \Yii::$app->user->logout();
    }
}
