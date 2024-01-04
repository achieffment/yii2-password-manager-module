<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use Yii;

/**
 * @var yii\widgets\ActiveForm $form
 * @var chieff\modules\PasswordManager\models\Account $model
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'account-form',
    'layout' => 'horizontal',
    'validateOnBlur' => false,
]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
    <?= $form->field($model, 'site')->textInput(['maxlength' => 100]) ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'login')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'password')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'comment')->textarea(['maxlength' => 500]) ?>

    <div class="form-group">
        <?php if ($model->isNewRecord): ?>
            <?= Html::submitButton(
                '<i class="fa fa-plus-circle"></i> ' . PasswordManagerModule::t('back', 'Create'),
                ['class' => 'btn btn-success']
            ) ?>
        <?php else: ?>
            <?= Html::submitButton(
                '<i class="fa fa-check"></i> ' . PasswordManagerModule::t('back', 'Save'),
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    </div>

<?php ActiveForm::end() ?>