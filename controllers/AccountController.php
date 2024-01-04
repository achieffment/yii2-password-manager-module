<?php

namespace chieff\modules\PasswordManager\controllers;

use chieff\modules\PasswordManager\PasswordManagerModule;
use chieff\modules\PasswordManager\models\Account;
use chieff\modules\PasswordManager\models\AccountUser;
use chieff\modules\PasswordManager\models\forms\SetMasterPassword;
use chieff\modules\PasswordManager\models\forms\GetMasterPassword;

use webvimark\modules\UserManagement\components\GhostAccessControl;

use yii\web\NotFoundHttpException;
use Yii;

class AccountController extends \yii\web\Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => GhostAccessControl::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = $this->checkUser();

        // creating account user
        $accountUser = AccountUser::findOne(['user_id' => $userId]);
        if (!$accountUser) {
            return $this->redirect(['set-master-password']);
        }

        if (!$accountUser->compareMasterPassword()) {
            return $this->redirect(['get-master-password']);
        }

        return $this->render('index');
    }

    public function actionSetMasterPassword()
    {
        $userId = $this->checkUser();

        $accountUser = AccountUser::findOne(['user_id' => $userId]);
        if (!$accountUser) {
            $model = new SetMasterPassword();

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $accountUser = new AccountUser();
                $accountUser->user_id = $userId;
                $accountUser->password = $model->password;

                if ($accountUser->save()) {
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', PasswordManagerModule::t('back', 'Something went wrong'));
                }
            }

            return $this->render('setMasterPassword', compact('model'));
        }

        return $this->redirect(['index']);
    }

    public function actionGetMasterPassword()
    {
        $userId = $this->checkUser();

        $accountUser = AccountUser::findOne(['user_id' => $userId]);
        if ($accountUser) {
            $model = new GetMasterPassword();

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($accountUser->compareMasterPassword($model->password)) {
                    $accountUser->rememberMasterPassword();

                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', PasswordManagerModule::t('back', 'Incorrect password'));
                }
            }

            return $this->render('getMasterPassword', compact('model'));
        }

        return $this->redirect(['set-master-password']);
    }

    public function checkUser()
    {
        $userId = Yii::$app->user->id;
        if (!$userId) {
            throw new NotFoundHttpException(Yii::t('yii', 'You must be authorized'));
        }
        return $userId;
    }
}