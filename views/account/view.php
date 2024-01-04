<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

use yii\widgets\DetailView;
use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var chieff\modules\PasswordManager\models\Account $model
 */

$this->title = $model->name ? $model->name : $model->login;
$this->params['breadcrumbs'][] = ['label' => PasswordManagerModule::t('back', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <p>
            <?= Html::a(PasswordManagerModule::t('back', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a(PasswordManagerModule::t('back', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger pull-right',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'site',
                    'email',
                    'login',
                    'password',
                    'comment',
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => function($model) {
                            $user = $model->createdBy;
                            if ($user) {
                                return Html::a($user->username, ['/user-management/user/view', 'id' => $user->id], ['data-pjax' => 0]);
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function($model) {
                            $user = $model->updatedBy;
                            if ($user) {
                                return Html::a($user->username, ['/user-management/user/view', 'id' => $user->id], ['data-pjax' => 0]);
                            }
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>