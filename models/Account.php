<?php

namespace chieff\modules\PasswordManager\models;

use chieff\helpers\SecurityHelper;
use chieff\modules\PasswordManager\PasswordManagerModule;
use webvimark\modules\UserManagement\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $site
 * @property string|null $email
 * @property int $login
 * @property string $password
 * @property string|null $comment
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Account extends \yii\db\ActiveRecord
{
    public $passphrase = '';

    protected $encodedAttributes = ['name', 'site', 'email', 'login', 'password', 'comment'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return Yii::$app->getModule('password-manager')->account_table;
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
            [['name', 'site', 'email', 'login', 'comment'], 'trim'],
            [['name', 'site', 'email', 'login', 'comment'], 'purgeXSS'],
            [['user_id', 'login', 'password'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'site', 'email', 'login', 'password'], 'string', 'max' => 300],
            [['comment'], 'string', 'max' => 1500],
            [['email'], 'email'],
            [['name', 'site', 'email', 'login', 'password', 'comment'], 'validateEncode']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Id',
            'name' => 'Name',
            'site' => 'Site',
            'email' => 'Email',
            'login' => 'Login',
            'password' => 'Password',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public function validateEncode($attribute)
    {
        if (
            ($this->$attribute != '') &&
            ($this->$attribute != null) &&
            $this->passphrase
        ) {
            $value = SecurityHelper::encode($this->$attribute, 'aes-256-ctr', $this->passphrase);
            if (!$value) {
                $this->addError($attribute, PasswordManagerModule::t('front', 'Can not encode field'));
            } else {
                $length = mb_strlen($value);
                if (
                    (in_array($this->$attribute, ['name', 'site', 'email', 'login', 'password', 'comment'])) &&
                    ($length > 300)
                ) {
                    $this->addError($attribute, PasswordManagerModule::t('front', 'Max exception'));
                } else if (
                    ($attribute == 'comment') &&
                    ($length > 1500)
                ) {
                    $this->addError($attribute, PasswordManagerModule::t('front', 'Max exception'));
                }
            }
        }
    }

    public function beforeSave($insert)
    {
        foreach ($this->encodedAttributes as $attribute) {
            if ($this->getOldAttribute($attribute) == $this->$attribute)
                continue;
            $this->setAttributeValue($attribute);
        }

        return parent::beforeSave($insert);
    }

    public function getAttributeValue($attribute, $passphrase = '') {
        if (
            ($this->$attribute != '') &&
            ($this->$attribute != null) &&
            (
                $this->passphrase ||
                $passphrase
            )
        ) {
            return SecurityHelper::decode($this->$attribute, 'aes-256-ctr', $this->passphrase ? $this->passphrase : $passphrase);
        }
        return $this->$attribute;
    }

    public function setAttributeValue($attribute) {
        if (
            ($this->$attribute != '') &&
            ($this->$attribute != null) &&
            $this->passphrase
        ) {
            $this->$attribute = SecurityHelper::encode($this->$attribute, 'aes-256-ctr', $this->passphrase);
        }
    }

    public function decodeAttributes(array $attributes = []) {
        if (!$attributes)
            $attributes = $this->encodedAttributes;
        foreach ($attributes as $attribute) {
            if (
                ($this->$attribute != '') &&
                ($this->$attribute != null) &&
                $this->passphrase
            ) {
                $this->$attribute = SecurityHelper::decode($this->$attribute, 'aes-256-ctr', $this->passphrase);
            }
        }
        return true;
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

    /**
     * Remove possible XSS stuff
     *
     * @param $attribute
     */
    public function purgeXSS($attribute)
    {
        $this->$attribute = Html::encode($this->$attribute);
    }
}