<?php
    /**
     * Crafted by Pavel Lint 11/1/17
     * Mail to: pavel@1int.org
     */

    use  \yii\web\Application;


    class RockstarsApp extends Application
    {
        function __construct($config = []) {
            parent::__construct($config);
            $this->name = "Rockstars! chess team";
        }

        static public function ClearTacticsAnswer($a) {
            $ret = preg_replace( '/[+!\. xX]*/', '' , $a);
            $ret = preg_replace( '/^1/', '' , $ret);
            return strtolower($ret);
        }
    }