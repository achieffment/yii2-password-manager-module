<?php

namespace chieff\modules\PasswordManager\controllers;

use Yii;

class ProfileController extends \webvimark\components\BaseController
{
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    public function actionIndex()
    {
        return 'test';
    }
}