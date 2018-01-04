<?php
    /**
     * Crafted by Pavel Lint 24/12/2017
     * Mail to: pavel@1int.org
     */

    namespace app\classes\lichess;



    use yii\base\Exception;

    class Game
    {
        public $id;
        public $variant;
        public $speed;
        public $perf;
        public $rated;
        public $status;
        public $createdAt;
        public $lastMoveAt;
        public $turns;
        public $url;
        public $player1;
        public $player2;
        public $opening;
        public $moves;
        public $winner;
        public $timeControl;

        /**
         * @param object $json
         */
        function __construct($json){
            foreach($json as $key => $value) {
                if(property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }

            try {
                $this->player1 = strtolower($json['players']['white']['userId']);
                $this->player2 = strtoloweR($json['players']['black']['userId']);
                $this->timeControl = intval($json['clock']['initial']) / 60;
                $add = intval($json['clock']['increment']);
                $this->timeControl .= '+' . $add;
            } catch(\Exception $e) {

            }
        }

        /**
         * @return float
         * @throws \Exception
         */
        function getResult() {
            if(strtolower($this->status) == 'draw') {
                return 0.5;
            }
            if($this->winner == 'white') {
                return 1;
            }
            else if($this->winner == 'black') {
                return -1;
            }
            else {
                throw new \Exception('Everything is broken');
            }
        }

    }