<?php
    /**
     * Crafted by Pavel Lint 23/12/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;

    use Yii;
    use yii\web\Controller;
    use yii\web\HttpException;
    use yii\web\NotFoundHttpException;

    use app\models\Tourney;
    use app\models\Match;
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
            $password = Yii::$app->request->post('admin-password');
            if( $password != '666' ) {
                throw new HttpException(403, 'Wrong admin password');
            }

            if( !Yii::$app->request->isPost ) {
                throw new HttpException(403, 'Everything is made to be broken');
            }

            /** @var Tourney $tourney */
            $tourney = Tourney::findOne($id);
            if( $tourney == null ) {
                throw new NotFoundHttpException('Tournament not found');
            }

            // Actual update logic
            $api = new Api();

            for($round = 1; $round <= $tourney->totalRounds; $round++) {
                if($round > 1 && !$tourney->isRoundFinished($round-1)) {
                    break;
                }

                $matches = Match::find()->where('tourney_id = :tourney_id AND href IS NULL AND round=:round',
                    ['tourney_id'=>$id, 'round'=>$round])->all();

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
            }

            $this->redirect('/tourney/' . $tourney->slug);
        }

        function actionNew() {
            $password = Yii::$app->request->post('admin-password');
            if( $password != '666' ) {
                throw new HttpException(403, 'Wrong admin password');
            }

            $tourney = new Tourney();
            $tourney->setAttributes($_POST);
            //Konstantin_Zinkowski,nodiko500,Arevalz,DavidCecxladze,Aleksandr_Kalugin,Pirs07
           // $tourney->date = (\DateTime::createFromFormat('d/m/Y', $tourney->date))->format('Y-m-d');

            $logo1 = $_FILES['logo1']['tmp_name'];
            if( $logo1 && $tourney->team2name ) {
                $type = strtolower(pathinfo($_FILES['logo1']['name'],PATHINFO_EXTENSION));
                $path = __DIR__ . '/../web/images/teams/' . str_replace(' ', '-',$tourney->team1name) . '.' . $type;
                move_uploaded_file($_FILES["logo1"]["tmp_name"], $path);
                $tourney->team1logo = '/images/teams/' . str_replace(' ', '-',$tourney->team1name) . '.' . $type;
            }
            else {
                $tourney->team1logo = '/images/logo.jpg';
            }

            $logo2 = $_FILES['logo2']['tmp_name'];
            if( $logo2 && $tourney->team2name ) {
                $type = strtolower(pathinfo($_FILES['logo2']['name'],PATHINFO_EXTENSION));
                $path = __DIR__ . '/../web/images/teams/' . str_replace(' ', '-',$tourney->team2name) . '.' . $type;
                move_uploaded_file($_FILES["logo2"]["tmp_name"], $path);
                $tourney->team2logo = '/images/teams/' .  str_replace(' ', '-',$tourney->team2name) . '.' . $type;
            }
            else {
                $tourney->team2logo = '/images/teams/friends.png';
            }

            if($tourney->save()) {
                $tourney->generateRounds();
                $this->redirect('/tourney/' . $tourney->slug);
            }
            else {
                throw new HttpException(400, explode(',',$tourney->getErrors()));
            }

        }



    }