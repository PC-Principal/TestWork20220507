<?php
/**
 * Created by PhpStorm.
 * User: basil
 * Date: 07.05.2022
 * Time: 15:16
 */

namespace app\components;
use app\models\Users;
use yii\helpers\Json;
use yii\base\Component;
use Yii;

/**
 * Аналогичный консольной команде код, однако его можно вызывать через Yii приложение
 */
class ParseRepoComponent extends Component
{
    /**
     * Время кеширования списка в секундах
     */
    const TIME = 600;

    /**
     * Начинаем работу компонента парсинга данных
     * @return array
     */
    public function TakeData(): array
    {
        // Кеш не сработает из приложения, если не выставить правильные права на папку runtime
        Yii::$app->cache->set('repoData', self::RepoList(),self::TIME);
        return self::RepoList();
    }

    /**
     * Получаем данные пользовательских репозиториев и собираем их в массив
     * @return array
     */
    private function RepoList(): array
    {
        // todo: ТЗ на 10 пользователей GitHub из списка нужно переделать
    }
}