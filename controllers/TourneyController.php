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
    use yii\web\NotFoundHttpException;
    use app\classes\lichess\Api;
    use app\classes\lichess\Game;

    class TourneyController extends Controller
    {
        function actionIndex() {
            return $this->render('list', ['tourneys'=>Tourney::find()->all()]);
        }

        function actionView($slug) {
            $tourney = Tourney::findBySlug($slug);
            if( $tourney == null ) {
                throw new NotFoundHttpException('Tournament not found');
            }
            return $this->render('view', ['tourney'=>$tourney]);
        }

        function actionUpdate($id) {
            /** @var Tourney $tourney */
            $tourney = Tourney::findOne($id);
            if( $tourney == null ) {
                throw new NotFoundHttpException('Tournament not found');
            }

            // Actual update logic
            $api = new Api();

            $matches = Match::find()->where('tourney_id = :tourney_id AND href IS NULL',
                                                                    ['tourney_id'=>$id])->all();

            /** @var Match[] $matches */
            /** @var Game $game */
            foreach($matches as $match) {
                $game = $api->getGameBetweenPlayers($match->white, $match->black, $tourney->date);
                if( $game != null ) {
                    $match->href = $game->id;
                    $match->result = $game->getResult();
                    $match->save();
                }
            }

            // Show tourney as normal
            return $this->render('view', ['tourney'=>$tourney]);
        }
    }