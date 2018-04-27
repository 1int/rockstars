<?php

namespace app\controllers;

use app\models\InviteForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;


class SiteController extends Controller
{


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
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
            if (isset($_REQUEST['returnUrl'])) {
                $url = $_REQUEST['returnUrl'];
            }
            else {
                $url = null;
            }
            return $this->goBack($url);
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionInvite() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new InviteForm();
        if($model->load(Yii::$app->request->post()) && $model->registerUser()) {
            if (isset($_REQUEST['returnUrl'])) {
                $url = $_REQUEST['returnUrl'];
            }
            else {
                $url = null;
            }

            return $this->goBack($url);
        }
        return $this->render('invite', ['model'=>$model]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goBack();
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

}
