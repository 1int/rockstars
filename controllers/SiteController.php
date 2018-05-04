<?php

namespace app\controllers;

use app\models\InviteForm;
use app\models\TacticsPosition;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\Member;
use yii\Web\HttpException;


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

    /**
     *
     */
    public function actionAnswers() {
        if( Yii::$app->user->isGuest ) {
            return Yii::$app->user->loginRequired();
        }

        /** @var Member $user */
        $user = Yii::$app->user->identity;
        if( $user->username != 'marisha' ) {
           // return new HttpException(403, 'You cannot do this to me');
        }

        if( Yii::$app->request->isPost ) {
            $test_id = Yii::$app->request->post('test_id');
            $answers = Yii::$app->request->post('answers');
            $points = Yii::$app->request->post('points');


            for($i = 0; $i < 12; $i++) {
                $t = new TacticsPosition();
                $t->test_id = $test_id;
                $t->answer = $answers[$i];
                $t->points = intval($points[$i]);
                $t->dotdotdot = Yii::$app->request->post('dot' . $i) == 'on' ? 1 : 0;
                if( !$t->save() ) {
                    Yii::$app->session->addFlash('error', print_r($t->getErrors(), true));
                }
            }
            Yii::$app->session->addFlash('success', 'Test #' . $test_id . ' is saved!');

        }

        return $this->render('answers');
    }

}
