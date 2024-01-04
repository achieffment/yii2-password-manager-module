<?php

namespace chieff\modules\PasswordManager\models;

use webvimark\modules\UserManagement\models\User;

use yii\base\Security;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "account_user".
 *
 * @property int $id
 * @property int $user_id
 * @property string $password_hash
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class AccountUser extends \yii\db\ActiveRecord
{
    public $password;
    public $repeat_password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Yii::$app->getModule('password-manager')->account_user_table;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],

            [['password', 'repeat_password'], 'required', 'on' => 'newUser'],
            [['password', 'repeat_password'], 'trim', 'on' => 'newUser'],
            [['password', 'repeat_password'], 'string', 'max' => 255, 'on' => 'newUser'],
            ['password', 'match', 'pattern' => Yii::$app->getModule('password-manager')->passwordRegexp, 'on' => 'newUser'],
            ['repeat_password', 'compare', 'compareAttribute' => 'password', 'on' => 'newUser'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'password' => 'Password',
            'repeat_password' => 'Repeat Password',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        // If password has been set, than create password hash
        if ($this->password) {
            $this->setPassword($this->password);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->rememberMasterPassword();

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if (php_sapi_name() == 'cli') {
            $security = new Security();
            $this->password_hash = $security->generatePasswordHash($password);
        } else {
            $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function rememberMasterPassword()
    {
        if ($this->user_id && $this->password_hash) {
            Yii::$app->session->set('master_id', sha1($this->user_id));
            Yii::$app->session->set('master_password', sha1($this->password_hash));
            return true;
        }
        return false;
    }

    public function compareMasterPassword($password = '')
    {
        if (
            !$password &&
            $this->user_id && $this->password_hash &&
            ($master_id = Yii::$app->session->get('master_id')) && ($master_password = Yii::$app->session->get('master_password')) &&
            (sha1($this->user_id) == $master_id) && (sha1($this->password_hash) == $master_password)
        ) {
            return true;
        } else if (
            $password &&
            $this->password_hash &&
            ($this->validatePassword($password))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
