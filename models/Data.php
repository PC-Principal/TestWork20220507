<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "data".
 *
 * @property int|null $id
 * @property string|null $data
 *
 * @property Users $id0
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
            [['id'], 'integer'],
            [['data'], 'string'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['id' => 'id']],
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
        ];
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

    /**
     * Gets query for [[Id0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(Users::className(), ['id' => 'id']);
    }

    /**
     * Возвращает 10 последних созданных пользователей и по одному актуальному репозиторию
     * @return array
     */
    static public function ActualUsersWithDesc() {
        $users = Users::find()->select('id')->orderBy(['id' => SORT_DESC])->limit(10)->asArray()->all();
        $keys = ArrayHelper::map($users,'id','id');

        $data = Data::find()->where(['in','id',$keys])->asArray()->all();

        // todo: maybe continue
        return $data;
    }
}
