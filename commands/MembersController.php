<?php
    /**
     * Crafted by Pavel Lint 09/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\commands;

    use yii\console\Controller;
    use app\models\Member;
    use app\classes\lichess\Api;
    use app\classes\lichess\Player;

    class MembersController extends Controller {

        public function actionUpdate() {
            $members = Member::find()->all();
            $uids = array_map( function($e){
                /** @var Member $e */
                return $e->username;
            }, $members);

            $players = Api::getPlayersInfo($uids);
            foreach($players as $p) {
                $member = Member::findByUsername($p->id);
                /** @var Member $member */
                /** @var Player $p */
                $member->updateLichessData($p);
            }
        }

    }