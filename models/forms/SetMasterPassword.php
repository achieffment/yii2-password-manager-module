<?php

namespace chieff\modules\PasswordManager\models\forms;

use Yii;

class SetMasterPassword extends \yii\base\Model
{
    public $password;
    public $repeat_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'repeat_password'], 'required'],
            [['password', 'repeat_password'], 'trim'],
            [['password', 'repeat_password'], 'string', 'max' => 255],
            ['password', 'match', 'pattern' => Yii::$app->getModule('password-manager')->passwordRegexp],
            ['repeat_password', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Password',
            'repeat_password' => 'Repeat Password',
        ];
    }

}