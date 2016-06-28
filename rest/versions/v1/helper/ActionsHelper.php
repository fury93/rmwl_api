<?php

namespace rest\versions\v1\helper;

use yii\web\ForbiddenHttpException;

class ActionsHelper {

    //Don't check access if type options
    const OPTIONS_TYPE = 'options';

    /**
     * Check, if user have access to action
     *
     * @param $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public static function ifActionAccess($action)
    {
        $controller = \Yii::$app->controller->id;

        if ($action->id !== self::OPTIONS_TYPE  && !\Yii::$app->user->can($action->id . ucfirst($controller))) {
            throw new ForbiddenHttpException('Access denied ' . $action->id . ucfirst($controller));
        }

        return true;
    }
}