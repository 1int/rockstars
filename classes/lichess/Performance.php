<?php
    /**
     * Crafted by Pavel Lint 08/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\classes\lichess;


    class Performance {

        public $name;
        public $rating;
        public $games;
        public $progress;
        public $rd;

        function __construct($name, $json) {
            $this->name =  $name;
            $this->games = $json["games"];
            $this->progress =$json["prog"];
            $this->rating = $json["rating"];
            $this->rd = $json["rd"];
        }
    }