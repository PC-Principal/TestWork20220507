<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\LoginForm;
use yii\web\Response;
use app\components\ParseRepoComponent;

class SiteController extends Controller
{

    /**
     * Список 10 актуальных репозиториев на главной странице, если кеш на 10 минут устаревает - запускается компонент
     * получения репозиториев - после чего кеш снова активен на 10 минут
     *
     * @return string
     * @throws HttpException
     */
    public function actionIndex()
    {
        $list = Yii::$app->cache->get('repoData');

        if($list == false) {
            $component = new ParseRepoComponent();
            $list = $component->TakeData();
            return $this->render('index',['list' => $list]);
        } else {
            return $this->render('index',['list' => $list]);
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
