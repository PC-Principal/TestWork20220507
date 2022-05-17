<?php
/**
 * Created by PhpStorm.
 * User: basil
 * Date: 17.05.2022
 * Time: 6:17
 */

namespace app\commands;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Users;
use app\models\Data;
use yii\helpers\Json;
use Yii;

/**
 * Аналогичный консольной команде код, однако его можно вызывать через Yii приложение
 */
class ParseRepoController extends Controller
{
    /**
     * Время кеширования списка в секундах
     */
    const TIME = 600;

    /**
     * Начинаем работу компонента парсинга данных
     * @return int
     */
    public function actionTakeData($username = null)
    {

        if($username == null) {
            Data::TruncateTable();
            Yii::$app->cache->set('timing', date('Y-m-d H:i:s',time()),self::TIME);
            return self::RepoList();
        } else {
            // some custom logic
            // todo: Сделать возможность дергать скрипт с параметром пользователя
        }

        return ExitCode::OK;
    }

    /**
     * Получаем данные пользовательских репозиториев и собираем их в массив
     * @return int
     */
    private function RepoList()
    {
        // todo: В конце убрать отсюда SecretToken

        foreach (Users::UserNames() as $user) {
            // GitHub требует обязательно указать UserAgent при запросе данных, если превысить лимит API по обращениям -
            // потребуется Secret Key
            $myCurl = curl_init();
            curl_setopt_array($myCurl, array(
                CURLOPT_URL => 'https://api.github.com/users/'.$user.'/repos?sort=updated&per_page=10',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'PC_Principal',
                // CURLOPT_USERPWD => "1cb3ac2852b137fbc132:dd85f7ed98e78cecbfe79d3ad84fd6a6b16630ba"
            ));

            $data = curl_exec($myCurl);

            $jsonData = Json::decode($data);

            foreach ($jsonData as $repo) {

                // Убираем невалидные символы в поле даты
                $dateUpdate = str_replace(['Z','T'],['',' '],$repo['updated_at']);

                // Сохраняем Json в поле таблицы Data
                $dataModel = new Data();
                $dataModel->user_id = Users::UserByUsername($user);
                $dataModel->data = Json::encode([
                    'full_name' => $repo['full_name'],
                    'url' => $repo['html_url'],
                ]);
                $dataModel->repo_date_update = date('Y-m-d H:i:s',strtotime($dateUpdate));
                $dataModel->save();
            }

            curl_close($myCurl);
        }

        return 0;
    }
}