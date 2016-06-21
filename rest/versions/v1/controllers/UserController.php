<?php
namespace rest\versions\v1\controllers;

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

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends Controller
{
    public $modelClass = 'rest\versions\v1\models\UserForm';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);

        $behaviors['authenticator'] = [
            /*'class' => QueryParamAuth::className(),
            'except' => ['login', 'register', 'password-restore', 'check-authentication', 'update']*/
            'class' => HttpBearerAuth::className(),
            'except' => ['login', 'register', 'password-restore', 'check-authentication'],
            /*'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],*/
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'delete' => ['POST'],
            ]
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
     * @return mixed
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['update']);
        unset($actions['create']);

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
    public function actionRegister()
    {
        $model = new UserForm();
        $model->scenario  = UserForm::SCENARIO_REGISTER;

        $params = \Yii::$app->getRequest()->getBodyParams();

        if ($model->load($params, '') && $model->createUser()) {
            return ResponseHelper::success($model->auth_key);
        } else {
            return ResponseHelper::failed($model->getErrors());
        }
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $params = \Yii::$app->getRequest()->getBodyParams();

        if ($model->load($params, '') && $model->updateUser()) {
            return ResponseHelper::success($model->auth_key);
        } else {
            return ResponseHelper::failed($model->getErrors());
        }
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
}
