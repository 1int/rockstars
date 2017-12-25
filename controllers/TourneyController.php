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

            /**
             * @var Tourney $tourney
             */
            $tourney = Tourney::findOne(1);
           // $tourney->generateRounds();
            $game = $tourney->matches[0];
            print "Getting match between " . $game->white . ' and ' .  $game->black;
            $ret = Api::getGameBetweenPlayers($game->white, $game->black, $tourney->date);

            var_dump($ret);

            die;
            return $this->render('index');
        }

    }