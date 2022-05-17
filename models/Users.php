<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя пользователя',
        ];
    }
    /**
     * Возвращает список всех пользователей в таблице Users
     * @return array
     */
    static public function UserList() {
        $users = Users::find()->asArray()->all();
        return $users;
    }

    /**
     * Возвращает список имен всех пользователей в таблице Users
     * @return array
     */
    static public function UserNames() {
        $usernames = Users::find()
            ->select('name')
            ->column();
        return $usernames;
    }

    /**
     * Возвращает id пользователя по имени пользователя
     * @param $name
     * @return int
     */
    static public function UserByUsername($name) {
        $id = Users::find()
            ->select(['id'])
            ->where(['name' => $name])
            ->scalar();
        return $id;
    }

    /**
     * Возвращает список пользователей, чьи репозитории еще не были загружены
     * @return array
     * @throws \yii\db\Exception
     */
    static public function WithoutData()
    {
        $dataIds = Data::find()->select('user_id')->column();
        $users = Users::find()->select(['name'])->where(['not in','id',$dataIds])->column();
        return $users;
    }
}
