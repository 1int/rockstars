<?php
    /**
     * Crafted by Pavel Lint 24/12/2017
     * Mail to: pavel@1int.org
     */

    namespace app\classes\lichess;

    use linslin\yii2\curl;
    use yii\helpers\Json;

    class Api
    {

        const APIURL = "https://lichess.org/api";

        /**
         * @param string $player1
         * @param string $player2
         * @param string $gameDate
         * @return Game|null
         */
        public static function getGameBetweenPlayers($player1, $player2, $gameDate) {
            $player1 = strtolower($player1);
            $player2 = strtolower($player2);
            $url = sprintf("%s/games/vs/%s/%s?nb=99", self::APIURL, $player1, $player2);
            $curl = new curl\Curl();

            $response = $curl->get($url);
            if( $curl->errorCode === null ) {
                $json = Json::decode($response);
                $games = $json['currentPageResults'];
                foreach($games as $g) {
                    $game = new Game($g);

                    // Check who played black and who played white
                    if($game->player1 == $player1 && $game->player2 == $player2) {
                        $date = new \DateTime();

                        // Check if the game was played on the day of the tournament
                        $date->setTimestamp(intval($game->createdAt)/1000);
                        if($date->format('Y-m-d') == $gameDate) {
                            return $game;
                        }
                    }
                }
            }
            else {
                var_dump($curl->errorText);
            }

            return null;
        }

    }