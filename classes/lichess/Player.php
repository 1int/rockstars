<?php
    /**
     * Crafted by Pavel Lint 08/05/2018
     * Mail to: pavel@1int.org
     */

    namespace app\classes\lichess;

    class Player {
        /** @property string[] $rating */
        /** @property string $blitzRating */
        /** @property string $rapidRating */
        /** @property string $bulletRating */

        public $id;
        public $username;
        public $title;
        public $online;
        public $playing;
        public $streaming;
        public $createdAt;
        public $seenAt;
        public $bio;
        public $country;
        public $firstName;
        public $lastName;
        public $links;
        public $location;
        /** @var Performance[] $perfs */
        public $perfs;
        public $patron;
        public $disabled;
        public $engine;
        public $booster;
        public $playtime;
        public $tvtime;

        /**
         * @param object $json
         */
        function __construct($json){
            foreach($json as $key => $value) {
                if(property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }

            if( isset($json['profile']) ) {
                $profile = $json['profile'];
                if ($profile) {
                    foreach ($profile as $key => $value) {
                        if (property_exists($this, $key)) {
                            $this->{$key} = $value;
                        }
                    }
                }
            }

            try {
                foreach($json['perfs'] as $k => $v) {
                    $this->perfs[$k] = new Performance($k, $v);
                }
                $this->playtime = $json['playTime']['total'];
                $this->tvtime = $json['playTime']['tv'];
            } catch(\Exception $e) {
                print $e->getMessage();
            }
        }

        /**
         * @return array
         */
        function getRating() {
            $categories = ["blitz", "bullet", "rapid", "classical", "puzzle"];
            $ret = [];
            foreach( $categories as $c ) {
                if( array_key_exists($c, $this->perfs) ) {
                    $ret[$c] = $this->perfs[$c]->rating;
                }
                else {
                    $ret[$c] = '1500?';
                }
            }
            return $ret;
        }

        function getBlitzRating() {
            return $this->getRating()["blitz"];
        }

        function getRapidRating() {
            return $this->getRating()['rapid'];
        }

        function getBulletRating() {
            return $this->getRating()['bullet'];
        }
    }