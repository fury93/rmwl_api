<?php

namespace rest\versions\v1\helper;

use rest\versions\v1\models\Permissions;
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

        if ($action->id !== self::OPTIONS_TYPE ) {
            //unique will be
            $actionName = $action->id . ucfirst($controller);

            if(!\Yii::$app->user->can($actionName) || Permissions::isForbiddenActionForUser($actionName)) {
                throw new ForbiddenHttpException('Access denied ' . $action->id . ucfirst($controller));
            }
        }

        return true;
    }
}