<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\db\Connection;

/**
 * This is the model class for table "data".
 *
 * @property int $id
 * @property string|null $data
 * @property int|null $user_id
 * @property string|null $repo_date_update
 *
 * @property Users $user
 */
class Data extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['user_id'], 'integer'],
            [['repo_date_update'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'user_id' => 'User ID',
            'repo_date_update' => 'Repo Date Update',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * Очистка таблицы с данными репозиториев
     */
    static public function TruncateTable()
    {
        $command = Yii::$app->db;
        $command->createCommand('TRUNCATE TABLE `data`;')->execute();
        $command->createCommand('ALTER TABLE `data` AUTO_INCREMENT=1;')->execute();
    }

    /**
     * Возвращает 10 актуальных репозиториев пользователей GitHub, предварительно декодируем Json строки
     * @return array|\yii\db\ActiveRecord[]
     */
    static public function ActualRepo()
    {
        $list = Data::find()->joinWith('user')->orderBy(['repo_date_update' => SORT_DESC])->limit(10)->asArray()->all();

        $readyArr = [];

        foreach ($list as $item) {
            $data = Json::decode($item['data']);
            $item['data'] = $data;
            array_push($readyArr,$item);
        }

        return $readyArr;
    }
}
