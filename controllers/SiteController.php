<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Users;
use app\models\Data;
use app\models\LoginForm;
use app\commands\Parse;

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
        // Если нет репозиториев - ошибка
        // Если есть репозитории, но есть пустые пользователи - обновить и отрендерить
        // Если все ок, просто отрендерить
        if(count(Data::ActualRepo()) == 0) {
            throw new NotFoundHttpException('Данные не были загружены или отсутствует список пользователей. Попробуйте обновить позже.');
        } elseif(Data::ActualRepo() > 0 && !empty(Users::WithoutData())) {
            $consoleApp = Parse::getInstance();
            $consoleApp->actionTakeData(Users::WithoutData());
            $list = Data::ActualRepo();
            $time = Yii::$app->cache->get('updated');
            return $this->render('index',['list' => $list,'time' => $time]);
        } else {
            $list = Data::ActualRepo();
            $time = (Yii::$app->cache->get('updated'))?Yii::$app->cache->get('updated'):Yii::$app->cache->get('timing');
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
