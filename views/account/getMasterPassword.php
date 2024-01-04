<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\widgets\ActiveForm $form
 * @var chieff\modules\PasswordManager\models\forms\GetMasterPassword $model
 */

$this->title = PasswordManagerModule::t('back', 'Get master password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="get-master-password">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id' => 'get-master-password-form',
                'layout' => 'horizontal',
                'validateOnBlur' => false,
            ]) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

            <?= Html::submitButton(
                PasswordManagerModule::t('back', 'Get'),
                ['class' => 'btn btn-success']
            ) ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>