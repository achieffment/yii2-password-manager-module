<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\widgets\ActiveForm $form
 * @var chieff\modules\PasswordManager\models\forms\SetMasterPassword $model
 */

$this->title = PasswordManagerModule::t('back', 'Set master password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="set-master-password">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id' => 'set-master-password-form',
                'layout' => 'horizontal',
                'validateOnBlur' => false,
            ]) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

            <?= Html::submitButton(
                '<i class="fa fa-plus-circle"></i> ' . PasswordManagerModule::t('back', 'Create'),
                ['class' => 'btn btn-success']
            ) ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>