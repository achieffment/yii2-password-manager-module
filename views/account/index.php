<?php

use chieff\modules\PasswordManager\PasswordManagerModule;

use webvimark\extensions\GridPageSize\GridPageSize;
use webvimark\extensions\DateRangePicker\DateRangePicker;

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var chieff\modules\PasswordManager\models\search\AccountSearch $searchModel
 */

$this->title = PasswordManagerModule::t('back', 'Accounts');
$this->params['breadcrumbs'][] = $this->title;

?>
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="cms-backend-index">
        <div class="panel panel-default">
            <div class="row">
                <div class="col-sm-6">
                    <p>
                        <?= Html::a(
                            '<i class="fa fa-plus-circle"></i> ' . PasswordManagerModule::t('back', 'Create'),
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>
                </div>
                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'password-manager-grid-pjax']) ?>
                    <p></p>
                </div>
            </div>
            <div class="panel-body">
                <?php Pjax::begin([
                    'id' => 'password-manager-grid-pjax',
                ]) ?>
                <?= GridView::widget([
                    'id' => 'password-manager-grid',
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => 'yii\bootstrap4\LinkPager',
                        'hideOnSinglePage' => true,
                        'lastPageLabel' => '>>',
                        'firstPageLabel' => '<<',
                    ],
                    'layout' => '
                        {items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width: 10px']],
                        [
                            'attribute' => 'name',
                            'value' => function($model) use ($passphrase) {
                                return Html::a($model->getAttributeValue('name', $passphrase) . ' ' . \rmrevin\yii\fontawesome\FAS::icon('edit'), ['update', 'id' => $model->id], ['data-pjax' => 0]);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'site',
                            'value' => function($model) use ($passphrase)  {
                                return $model->getAttributeValue('site', $passphrase);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'email',
                            'value' => function($model) use ($passphrase)  {
                                return $model->getAttributeValue('email', $passphrase);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'login',
                            'value' => function($model) use ($passphrase)  {
                                return $model->getAttributeValue('login', $passphrase);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'password',
                            'value' => function($model) use ($passphrase)  {
                                return $model->getAttributeValue('password', $passphrase);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'comment',
                            'value' => function($model) use ($passphrase)  {
                                return $model->getAttributeValue('comment', $passphrase);
                            },
                            'format' => 'raw'
                        ],
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
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'contentOptions' => ['style' => 'width: 70px; text-align: center;'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
<?php DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'created_at',
]) ?>
<?php DateRangePicker::widget([
    'model' => $searchModel,
    'attribute' => 'updated_at',
]) ?>