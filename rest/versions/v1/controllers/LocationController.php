<?php

namespace rest\versions\v1\controllers;

use Yii;
use rest\versions\v1\helper\ActionsHelper;
use rest\versions\v1\helper\ResponseHelper;
use rest\versions\v1\models\Location;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\filters\auth\HttpBearerAuth;

/**
 * Location implements the CRUD actions for Product model.
 */
class LocationController extends ActiveController
{
    public $modelClass = 'rest\versions\v1\models\Location';

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
/*    public function beforeAction($action)
    {
        return parent::beforeAction($action) ? ActionsHelper::ifActionAccess($action) : false;
    }*/

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
        unset($actions['view']);

        return $actions;
    }

    /**
     * Prepare data for index
     *
     * @return array
     */
    public function prepareDataProvider()
    {
        $locations = Location::getLocationsList();

        return ResponseHelper::success($locations);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        $params = \Yii::$app->getRequest()->getBodyParams();

        if ($model->load($params, '') && $model->insertLocation()) {
            return ResponseHelper::success($model);
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

        if ($model->load($params, '') && $model->insertLocation()) {
            return ResponseHelper::success($model);
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
        $model = $this->findModel($id);

        if($model && $model->delete()) {
            return ResponseHelper::success(['id' => $id]);
        }

        return ResponseHelper::failed(null);
    }


    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            return false;
        }
    }
}