<?php

namespace chieff\modules\PasswordManager\controllers;

use chieff\modules\PasswordManager\PasswordManagerModule;
use chieff\modules\PasswordManager\models\Account;
use chieff\modules\PasswordManager\models\AccountUser;
use chieff\modules\PasswordManager\models\forms\SetMasterPassword;
use chieff\modules\PasswordManager\models\forms\GetMasterPassword;

use webvimark\modules\UserManagement\components\GhostAccessControl;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Yii;

class AccountController extends \webvimark\components\AdminDefaultController
{
    public $userId;
    public $accountUser;
    public $passphrase;

    public $modelClass = 'chieff\modules\PasswordManager\models\Account';

    public $modelSearchClass = 'chieff\modules\PasswordManager\models\search\AccountSearch';

    public $enableOnlyActions = ['index', 'create', 'update', 'view', 'delete'];

    public function actionIndex()
    {
        $passphrase = $this->passphrase;

        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;
        if ($searchModel) {
            $dataProvider = $searchModel->search($this->userId, $passphrase, Yii::$app->request->getQueryParams());
        } else {
            $modelClass = $this->modelClass;
            $dataProvider = new ActiveDataProvider([
                'query' => $modelClass::find()->where(['user_id' => $this->userId]),
            ]);
        }

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel', 'passphrase'));
    }

    public function actionSetMasterPassword()
    {
        $userId = $this->getUserId();
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
            return $this->render('/forms/setMasterPassword', compact('model'));
        }
        return $this->redirect(['index']);
    }

    public function actionGetMasterPassword()
    {
        $userId = $this->getUserId();
        $accountUser = AccountUser::findOne(['user_id' => $userId]);
        if ($accountUser) {
            $model = new GetMasterPassword();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($accountUser->compareMasterPassword($model->password)) {
                    $accountUser->password = $model->password;
                    $accountUser->rememberMasterPassword();
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', PasswordManagerModule::t('back', 'Incorrect password'));
                }
            }
            return $this->render('/forms/getMasterPassword', compact('model'));
        }
        return $this->redirect(['set-master-password']);
    }

    public function actionCreate()
    {
        $model = new $this->modelClass;
        $model->user_id = $this->userId;
        $model->passphrase = $this->passphrase;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $redirect = $this->getRedirectPage('create', $model);
            return $redirect === false ? '' : $this->redirect($redirect);
        }
        return $this->renderIsAjax('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->accountUser->user_id != $model->user_id) {
            Yii::$app->session->setFlash('error', 'You can not update other people accounts');
            return $this->redirect(['index']);
        }
        $model->passphrase = $this->passphrase;
        $model->decodeAttributes();
        if ($model->load(Yii::$app->request->post()) and $model->save()) {
            $redirect = $this->getRedirectPage('update', $model);
            return $redirect === false ? '' : $this->redirect($redirect);
        }
        return $this->renderIsAjax('update', compact('model'));
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($this->accountUser->user_id != $model->user_id) {
            Yii::$app->session->setFlash('error', 'You can not view other people accounts');
            return $this->redirect(['index']);
        }
        $model->passphrase = $this->passphrase;
        $model->decodeAttributes();
        return $this->renderIsAjax('view', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($this->accountUser->user_id != $model->user_id) {
            Yii::$app->session->setFlash('error', 'You can not delete other people accounts');
            return $this->redirect(['index']);
        }
        $model->delete();
        $redirect = $this->getRedirectPage('delete', $model);
        return $redirect === false ? '' : $this->redirect($redirect);
    }

    public function getUserId()
    {
        $userId = Yii::$app->user->id;
        if (!$userId) {
            throw new NotFoundHttpException(Yii::t('yii', 'You must be authorized'));
        }
        return $userId;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (
                ($action->id != 'set-master-password') &&
                ($action->id != 'get-master-password')
            ) {
                $this->userId = $this->getUserId();
                $this->accountUser = AccountUser::findOne(['user_id' => $this->userId]);
                if (!$this->accountUser) {
                    return $this->owner->redirect(['set-master-password']);
                }
                if (!$this->accountUser->compareMasterPassword()) {
                    return $this->owner->redirect(['get-master-password']);
                }
                $this->passphrase = $this->accountUser->passphrase;
                if (!$this->passphrase) {
                    return $this->owner->redirect(['get-master-password']);
                }
            }
            return true;
        }
        return false;
    }
}