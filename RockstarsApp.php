<?php
    /**
     * Crafted by Pavel Lint 11/1/17
     * Mail to: pavel@1int.org
     */

    use \yii\web\Application;
    use \app\models\Member;


    class RockstarsApp extends Application
    {
        /**
         * @inheritdoc
         */
        function __construct($config = []) {
            parent::__construct($config);
            $this->name = "Rockstars! chess team";
        }

        /**
         * @param $a string the source answer (i.e. 1. ...Nxd7)
         * @return string the canonical answer (Nd7)
         */
        static public function ClearTacticsAnswer($a) {
            $ret = mb_strtolower($a);
            $ret = str_replace(['кр', 'с', 'к', 'ф', 'л'], ['k', 'b', 'n', 'q', 'r'], $ret);
            $ret = preg_replace( '/[+!\. xXхХ#:]*/', '' , $ret);
            $ret = preg_replace( '/^1/', '' , $ret);
            return $ret;
        }
    }
