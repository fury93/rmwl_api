<?php
namespace rest\versions\v1\controllers;

use rest\versions\v1\helper\ActionsHelper;
use rest\versions\v1\helper\ResponseHelper;
use rest\versions\v1\models\LoginForm;
use rest\versions\v1\models\Role;
use rest\versions\v1\models\User;
use rest\versions\v1\models\UserForm;
use rest\versions\v1\models\Vendor;
use yii\web\ForbiddenHttpException;
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
        unset($behaviors['rateLimiter']);
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => [
                'login',
                'reset-password',
                'change-password',
                'check-authentication',
                'options'
            ],
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
            $userConfigs = UserForm::getUserConfigurations();

            return ResponseHelper::success(['user' => $userConfigs]);
        }

        return ResponseHelper::failed(['message' => 'Password or login not correct']);
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        $user = \Yii::$app->user->identity;
        if ($user) {
            $user->removeAccessToken();
        }

        return ResponseHelper::success([]);
    }

    /**
     * Check if user have valid token (token expire date check)
     * If token valid, then return user configurations
     *
     * @return array
     */
    public function actionCheckAuthentication()
    {
        $accessToken = Yii::$app->request->post('token');

        if ($accessToken && $userData = User::findIdentityByAccessToken($accessToken)) {
            $userConfigs = UserForm::getUserConfigurations($userData);
            $roles = Role::getRolesValues();
            $vendorStatus = Vendor::getVendorsStatuses();

            return ResponseHelper::success([
                'user' => $userConfigs,
                'roles' => $roles,
                'vendorStatus' => $vendorStatus
            ]);
        }

        return ResponseHelper::failed(['message' => 'Token is not valid']);
    }

    /**
     * Prepare data for index
     *
     * @return array
     */
    public function prepareDataProvider()
    {
        $users = UserForm::getUsersList();

        return ResponseHelper::success($users);
    }

    /**
     * Register user
     *
     * @return UserForm|string
     */
    public function actionCreate()
    {
        $model = new UserForm();

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

        if ($model) {
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
     * Action for reset password
     *
     * @return array
     */
    public function actionResetPassword()
    {
        $email = Yii::$app->request->post('email');

        $user = User::findByEmail($email);
        if (!$user) {
            return ResponseHelper::failed(['message' => 'The user with this email does not exist.']);
        }

        $user->generatePasswordResetToken();
        $user->save(false);

        \Yii::$app->mailer->compose()
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Password recovery')
            ->setTextBody('Plain text content')
            ->setHtmlBody($this->render('@rest/versions/v1/views/resetPassword', compact('user')))
            ->send();

        return ResponseHelper::success(['message' => 'An email has been sent message.']);
    }

    /**
     * Change user password
     *
     * @return array
     */
    public function actionChangePassword()
    {
        $password = Yii::$app->request->post('password');
        $resetToken = Yii::$app->request->post('resetToken');

        if ($resetToken && $password && $user = User::findByPasswordResetToken($resetToken)) {
            $user->setPassword($password);
            $user->clearPasswordResetToken();

            if ($user->save(false)) {
                return ResponseHelper::success(['message' => 'Password was successfully changed.']);
            }
        }

        return ResponseHelper::failed(['message' => 'Password reset token not valid.']);
    }

    /**
     * Get users list for select's
     *
     * @return array
     */
    public function actionUsersList()
    {
        $users = UserForm::getUsersListSelect();

        return ResponseHelper::success($users);
    }
}
