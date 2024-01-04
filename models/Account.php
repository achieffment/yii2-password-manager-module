<?php

namespace chieff\modules\PasswordManager\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $site
 * @property string|null $email
 * @property int $login
 * @property string $password_hash
 * @property string|null $comment
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Account extends \yii\db\ActiveRecord
{
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
            [['login', 'password_hash'], 'required'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'site'], 'string', 'max' => 100],
            [['email', 'login'], 'string', 'max' => 128],
            [['email'], 'email'],
            [['password_hash'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'site' => 'Site',
            'email' => 'email',
            'login' => 'Login',
            'password_hash' => 'Password Hash',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
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