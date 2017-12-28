<?php
    /**
     * Crafted by Pavel Lint 23/12/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;
    use app\models\Tourney;
    use app\models\Match;
    use yii\web\Controller;

    use linslin\yii2\curl;
    use app\classes\lichess\Api;

    class TourneyController extends Controller
    {
        function actionIndex() {
            return $this->render('list', ['tourneys'=>Tourney::find()->all()]);
        }
    }