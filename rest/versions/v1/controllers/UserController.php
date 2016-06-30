<?php
namespace rest\versions\v1\controllers;

use rest\versions\v1\helper\ActionsHelper;
use rest\versions\v1\helper\ResponseHelper;
use rest\versions\v1\models\LoginForm;
use rest\versions\v1\models\User;
use rest\versions\v1\models\UserForm;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\ActiveController;
use \Yii;

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends ActiveController
{
    public $modelClass = 'rest\versions\v1\models\UserForm';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //unset($behaviors['rateLimiter']);
        // remove authentication filter

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        //$behaviors['authenticator']['except'] = ['options'];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'register', 'password-restore', 'check-authentication', 'options'],
        ];

        /*$behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'update' => ['POST'],
            ]
        ];*/

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
     * @return mixed
     */
    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['update']);
        unset($actions['create']);
        unset($actions['delete']);

        return $actions;
    }

    /**
     * This method implemented to demonstrate the receipt of the token.
     * Do not use it on production systems.
     * @return string AuthKey or model with errors
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $params = \Yii::$app->request->getBodyParams();

        if ($model->load($params, '') && $model->login()) {
            $userData = UserForm::getUserConfigurations();

            return ResponseHelper::success(['user' => $userData]);
        }

        return ResponseHelper::failed('Password or login not correct');
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        $user = \Yii::$app->user->identity;
        if($user) {
            $user->removeAccessToken();
        }

        return \Yii::$app->user->logout();
    }

    /**
     * @return array
     */
    public function actionCheckAuthentication()
    {
        $response = [];
        $accessToken = Yii::$app->request->getQueryParam('access-token');

        try {
            if (!$accessToken) {
                throw new Exception('Invalid access token');
            }

            $user = User::findIdentityByAccessToken($accessToken);

            if (!$user) {
                throw new Exception('Authenticated is failed');
            }

            $response['isAuthenticated'] = true;
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * Register user
     *
     * @return UserForm|string
     */
    public function actionCreate()
    {
        $model = new UserForm();
//        $model->scenario  = UserForm::SCENARIO_REGISTER;

        $params = \Yii::$app->getRequest()->getBodyParams();

        if ($model->load($params, '') && $model->createUser()) {
            return ResponseHelper::success(UserForm::filterUserData($model));
        }

        return ResponseHelper::failed($model->getErrors());
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $params = \Yii::$app->getRequest()->getBodyParams();

        if ($model->load($params, '') && $model->updateUser()) {
            return ResponseHelper::success(UserForm::filterUserData($model));
        }

        return ResponseHelper::failed($model->getErrors());
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if($model) {
            return ResponseHelper::success($model);
        } else {
            return ResponseHelper::failed(null);
        }
    }

    /**
     * Deletes an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return ResponseHelper::success(['id' => $id]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Prepare data for index
     *
     * @return array
     */
    public function prepareDataProvider()
    {
        $users = UserForm::getUsersList();

        return ResponseHelper::success(['users' =>$users]);
    }
}
