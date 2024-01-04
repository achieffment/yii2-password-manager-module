<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\PasswordManager\models\Account $model
 */

$this->title = PasswordManagerModule::t('back', 'Editing account: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => PasswordManagerModule::t('back', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h2 class="lte-hide-title"><?= $this->title ?></h2>
<div class="panel panel-default">
    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>