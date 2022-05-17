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
    // 4 procceess
    /**
     * Время кеширования списка в секундах
     */
    const TIME = 600;

    /**
     * Github Token
     */
    const CREDENTIALS = "1cb3ac2852b137fbc132:dd85f7ed98e78cecbfe79d3ad84fd6a6b16630ba";

    /**
     * Начинаем работу компонента парсинга данных, если данные не пришли - то парсим весь список, если пришли то
     * парсим пользователей у которых нет доступных репозиториев
     * @param array
     * @return int
     */
    public function actionTakeData(array $arr = null)
    {

        $cmd = 'pgrep -c php';
        $countProc = shell_exec($cmd);

        if ($countProc == 5) {
            return ExitCode::SOFTWARE;
        } else {
            if($arr == null) {
                Data::TruncateTable();
                Yii::$app->cache->set('timing', date('Y-m-d H:i:s',time()),self::TIME);
                return self::RepoList(Users::UserNames());
            } else {
                Yii::$app->cache->set('updated', date('Y-m-d H:i:s',time()),self::TIME);
                return self::RepoList($arr);
            }
        }

        return ExitCode::OK;
    }

    /**
     * Получаем данные пользовательских репозиториев и собираем их в массив
     * @param array
     * @return int
     */
    private function RepoList(array $array)
    {
        foreach ($array as $user) {
            // GitHub требует обязательно указать UserAgent при запросе данных, если превысить лимит API по обращениям -
            // потребуется Secret Key
            $myCurl = curl_init();
            curl_setopt_array($myCurl, array(
                CURLOPT_URL => 'https://api.github.com/users/'.$user.'/repos?sort=updated&per_page=10',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'PC_Principal',
                CURLOPT_USERPWD => self::CREDENTIALS
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