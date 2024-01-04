<?php

namespace chieff\modules\PasswordManager\models\search;

use chieff\helpers\SecurityHelper;
use chieff\modules\PasswordManager\models\Account;

use yii\base\Model;
use yii\data\ActiveDataProvider;

use Yii;

class AccountSearch extends Account
{

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'site', 'email', 'login', 'password', 'comment'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($userId, $passphrase, $params)
    {
        $query = Account::find();

        $query->where(['user_id' => $userId]);
        $query->joinWith(['createdBy', 'updatedBy']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name ? SecurityHelper::encode($this->name, 'aes-256-ctr', $passphrase) : $this->name]);
        $query->andFilterWhere(['like', 'site', $this->site ? SecurityHelper::encode($this->site, 'aes-256-ctr', $passphrase) : $this->site]);
        $query->andFilterWhere(['like', 'email', $this->email ? SecurityHelper::encode($this->email, 'aes-256-ctr', $passphrase) : $this->email]);
        $query->andFilterWhere(['like', 'login', $this->login ? SecurityHelper::encode($this->login, 'aes-256-ctr', $passphrase) : $this->login]);
        $query->andFilterWhere(['like', 'password', $this->password ? SecurityHelper::encode($this->password, 'aes-256-ctr', $passphrase) : $this->password]);
        $query->andFilterWhere(['like', 'comment', $this->comment ? SecurityHelper::encode($this->comment, 'aes-256-ctr', $passphrase) : $this->comment]);

        if ($this->created_at) {
            $tmp = explode(' - ', $this->created_at);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.created_at', strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }
        if ($this->updated_at) {
            $tmp = explode(' - ', $this->updated_at);
            if (isset($tmp[0], $tmp[1])) {
                $query->andFilterWhere(['between', static::tableName() . '.updated_at', strtotime($tmp[0]), strtotime($tmp[1])]);
            }
        }

        $query->andFilterWhere([
            'or',
            ['like', Yii::$app->getModule('password-manager')->account_table . '.created_by', $this->created_by],
            ['like', Yii::$app->getModule('user-management')->user_table . '.username', $this->created_by]
        ]);
        $query->andFilterWhere([
            'or',
            ['like', Yii::$app->getModule('password-manager')->account_table . '.updated_by', $this->updated_by],
            ['like', Yii::$app->getModule('user-management')->user_table . '.username', $this->updated_by]
        ]);

        return $dataProvider;
    }

}