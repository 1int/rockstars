<?php
    /**
     * Crafted by Pavel Lint 23/12/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;
    use yii\web\Controller;

    use linslin\yii2\curl;
    use DateTime;
    use app\models\Member;

    class TourneyController extends Controller
    {

        function actionIndex() {
            return $this->render('index');
        }

    }