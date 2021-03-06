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
     * Github Token
     */
    const CREDENTIALS = "???";

    /**
     * Начинаем работу компонента парсинга данных, если данные не пришли - то парсим весь список, если пришли то
     * парсим пользователей у которых нет доступных репозиториев, также проверяем количество процессов, запущенных по php
     * @param array
     * @return int
     */
    public function actionTakeData(array $arr = null)
    {

        $cmd = 'pgrep -c php';
        $countProc = shell_exec($cmd);

        if ($countProc == 6) {
            echo 'Досрочное завершение процесса';
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
               // CURLOPT_USERPWD => self::CREDENTIALS
            ));

            $data = curl_exec($myCurl);

            $jsonData = Json::decode($data);

            foreach ($jsonData as $repo) {

                // Если по какой то причине не удастся обработать репозиторий пользователя - переходим к следующей итерации цикла
                try {
                    // Убираем невалидные символы в поле даты
                    $dateUpdate = str_replace(['Z','T'],['',' '],$repo['updated_at']);
                }
                catch(\Exception $e) {
                    continue;
                }

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

        // Удаляем пользователей с пустыми репозиториями
        Users::RemoveEmptyRepoUsers();

        return 0;
    }
}