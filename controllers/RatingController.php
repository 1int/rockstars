<?php
    /**
     * Crafted by Pavel Lint 05/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\controllers;
    use yii\web\Controller;

    use linslin\yii2\curl;
    use DateTime;
    use DateInterval;
    use yii\helpers\Json;

    use app\models\Member;

    class RatingController extends Controller
    {

        /**
         * Display team rating graph
         *
         * @param string $type blitz, bullet or classical
         * @return string
         */
        public function actionIndex($type='blitz')
        {

            //0. Get all users that play on lichess
            $members = Member::find()->where('plays_on_lichess=1')->all();
            $users = array_map( function($m) {
                return $m->username;
            }, $members);

            $curl = new curl\Curl();


            $allUsersData = [];

            //1. Get lichess rating history for each user
            //   TODO: maybe switch to lichess api here
            foreach( $users as $user ) {
                $response = $curl->get(sprintf("https://lichess.org/@/%s", $user));

                if( $curl->errorCode === null )
                {
                    $matches = [];
                    if( preg_match('/{"name":"'. ucfirst($type) .'","points":([^}]+)/', $response, $matches) > 0 )
                    {
                        $json = $matches[1];
                        $ret = Json::decode($json);
                        //[year, month, day, rating]

                        $data = array_map(function ($elem)
                        {
                            $date = ($elem[1] + 1) . '/' . $elem[2] . '/' . $elem[0];
                            $data = $elem[3];
                            $t = strtotime($date);

                            return [ $t * 1000, $data ];
                        }, $ret);

                        // get data for each day, not only days played
                        $data = $this->fillTimeSpaces($data);
                        $allUsersData[] = $data;
                    }
                }
                else {
                    die ("Failed to get user data for " . $user );
                }
            }

            //2. Find the earliest day when we had everybody playing
            $maxDate = new DateTime();
            $maxDate->setTimestamp($allUsersData[0][0][0] / 1000);


            foreach($allUsersData as $userData) {
                $date = new DateTime();
                $date->setTimestamp($userData[0][0] / 1000);
                if( $date > $maxDate ) {
                    $maxDate = $date;
                }
            }


            //3. Remove everything prior to the earliest date
            foreach($allUsersData as &$userData) {
                do {
                    $elem = $userData[0];
                    $date = new DateTime();
                    $date->setTimestamp($elem[0] / 1000);
                    if( $date < $maxDate ) {
                        array_shift($userData);
                    }
                    else {
                        break;
                    }
                }while(true);
            }

            /*$index = count($allUsersData[0]) - 1;
            for($i = 0; $i < count($users); $i++)
            {
                print $users[$i] . ': ' . $allUsersData[$i][$index - 1][1] . "\n";
                print $users[$i] . ': ' . $allUsersData[$i][$index][1] . "\n";
            }

            die;*/
            //4. Append data till today (in case nobody played today yet)
            $date = date('m/d/Y 00:00');
            $today = new DateTime();
            $today->setTimestamp(strtotime($date));

            foreach($allUsersData as &$userData) {
                do {
                    $elem = end($userData);
                    $date = new DateTime();
                    $date->setTimestamp($elem[0] / 1000);
                    if( $date < $today ) {
                        $date->add(new DateInterval("P1D"));
                        $userData[] = [1000 * $date->getTimestamp(), $elem[1]];
                    }
                    else {
                        break;
                    }
                }while(true);
            }

            //5. Combine data, find average and rating change since yesterday
            $avg = array_fill(0, count($allUsersData[0]), 0);


            for( $i = 0; $i < count($allUsersData); $i++) {
                for( $j = 0; $j < count($allUsersData[0]); $j++) {
                    $avg[$j] += $allUsersData[$i][$j][1];
                }
            }

            $avg = array_map(function($e)  use ($allUsersData) {
                return intval($e / count($allUsersData));
            }, $avg);

            for( $i = 0; $i < count($avg); $i++ ) {
                $avg[$i] = [$allUsersData[0][$i][0], $avg[$i]];
            }

            $rating = end($avg)[1];
            $dr = $rating - prev($avg)[1];

            //6. Render
            return $this->render('ratings-view', ['data'=>$avg, 'rating'=>$rating, 'dr'=>$dr, 'type'=>$type]);
        }

        /**
         * @param array $dataArray the array in the [timestamp, rating] format
         * @return array same array with time spaces filled
         */
        protected function fillTimeSpaces($dataArray) {
            $count = count($dataArray);
            $full = [];
            for( $i = 0; $i < $count; $i++) {
                if( $i == 0 ) {
                    $full[] = $dataArray[$i];
                    continue;
                }

                $prevdate = $dataArray[$i-1][0];
                $date = $dataArray[$i][0];

                $d =  new DateTime();
                $pd = new DateTime();
                $d->setTimestamp($date / 1000);
                $pd->setTimestamp($prevdate / 1000);
                $pd = $pd->add(new DateInterval("P1D"));

                while($pd < $d) {
                    $full[] = [1000*$pd->getTimestamp(), $dataArray[$i-1][1]];
                    $pd = $pd->add(new DateInterval("P1D"));
                }
                $full[] = [1000*$d->getTimestamp(), $dataArray[$i][1]];
            }

            return $full;
        }

        public function actionBlitz() {
            return $this->actionIndex('blitz');
        }

        public function actionClassical() {
            return $this->actionIndex('classical');
        }

        public function actionRapid() {
            return $this->actionIndex('classical');
        }

        public function actionBullet() {
            return $this->actionIndex('bullet');
        }
    }

