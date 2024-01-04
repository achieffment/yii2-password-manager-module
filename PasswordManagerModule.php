<?php

namespace chieff\modules\PasswordManager;

use Yii;

class PasswordManagerModule extends \yii\base\Module
{

    public $passphrase = '';

    /**
     * Pattern that will be applied for password.
     * Default pattern does not restrict user and can enter any set of characters.
     *
     * example of pattern :
     * '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$'
     *
     * This example pattern allow user enter only:
     *
     * ^: anchored to beginning of string
     * \S*: any set of characters
     * (?=\S{8,}): of at least length 8
     * (?=\S*[a-z]): containing at least one lowercase letter
     * (?=\S*[A-Z]): and at least one uppercase letter
     * (?=\S*[\d]): and at least one number
     * $: anchored to the end of the string
     *
     * @var string
     */
    public $passwordRegexp = '/^(.*)+$/';

    public $account_table = '{{%account}}';

    public $account_user_table = '{{%account_user}}';

    public $controllerNamespace = 'chieff\modules\PasswordManager\controllers';

    /**
     * @p
     */
    public function init()
    {
        parent::init();
    }

    /**
     * I18N helper
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (!isset(Yii::$app->i18n->translations['modules/password-manager/*'])) {
            Yii::$app->i18n->translations['modules/password-manager/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'ru',
                'basePath' => '@vendor/chieff/yii2-password-manager-module/messages',
                'fileMap' => [
                    'modules/password-manager/back' => 'back.php',
                    'modules/password-manager/front' => 'front.php',
                ],
            ];
        }
        return Yii::t('modules/password-manager/' . $category, $message, $params, $language);
    }

}