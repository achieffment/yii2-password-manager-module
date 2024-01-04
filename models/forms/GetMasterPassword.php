<?php

namespace chieff\modules\PasswordManager\models\forms;

use yii\base\Security;

use Yii;

class GetMasterPassword extends \yii\base\Model
{
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'trim'],
            ['password', 'string', 'max' => 255],
            ['password', 'match', 'pattern' => Yii::$app->getModule('password-manager')->passwordRegexp],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Password',
        ];
    }

}