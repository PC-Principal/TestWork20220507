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
        $dataArray = [];

        foreach (Users::UserNames() as $user) {
            // GitHub требует обязательно указать UserAgent при запросе данных, если превысить лимит API по обращениям -
            // потребуется Secret Key
            $myCurl = curl_init();
            curl_setopt_array($myCurl, array(
                CURLOPT_URL => 'https://api.github.com/users/'.$user.'/repos?sort=updated&per_page=10',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'PC_Principal',
               // CURLOPT_USERPWD => "user_id:secret_key"
            ));

            $data = curl_exec($myCurl);

            $jsonData = Json::decode($data);

            foreach ($jsonData as $repo) {

                // Убираем невалидные символы в поле даты
                $dateUpdate = str_replace(['Z','T'],['',' '],$repo['updated_at']);

                array_push( $dataArray,[
                    'user' => $user,
                    'full_name' => $repo['full_name'],
                    'url' => $repo['html_url'],
                    'updated' => $dateUpdate,
                    'ava' => $repo['owner']['avatar_url'],
                    'owner_url' => $repo['owner']['html_url'],
                ]);
            }

            curl_close($myCurl);
        }

        usort($dataArray, function($z,$x) {
            return $z['updated'] < $x['updated'];
        });

        $dataArray = array_slice($dataArray, 0, 10);

        array_push($dataArray,date("Y-m-d H:i:s",time()));

        return $dataArray;
    }
}