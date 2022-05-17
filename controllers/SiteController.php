<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Data;
use app\models\LoginForm;
use app\components\ParseRepoComponent;


class SiteController extends Controller
{

    /**
     * Список 10 актуальных репозиториев на главной странице из БД
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        // Если нет репозиториев или кеша - ошибка
        if(count(Data::ActualRepo()) == 0) {
            throw new NotFoundHttpException('Данные не были загружены или отсутствует список пользователей. Попробуйте обновить позже.');
        } else {
            // todo: Сделать выборку по параметру нового пользователя
            $list = Data::ActualRepo();
            $time = Yii::$app->cache->get('timing');
            return $this->render('index',['list' => $list,'time' => $time]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/crud-users/index');
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

}
