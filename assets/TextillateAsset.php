<?php
    /**
     * Crafted by Pavel Lint 07/11/2017
     * Mail to: pavel@1int.org
     */

    namespace app\assets;
    use yii\web\AssetBundle;

    class TextillateAsset extends AssetBundle
    {
        public $sourcePath = '@bower/textillate';
        public $js = [
            'assets/jquery.fittext.js', 'assets/jquery.lettering.js', 'jquery.textillate.js'
        ];
        public $css = [
            'assets/animate.css', 'assets/style.css'
        ];

        public $depends = [
            'yii\web\JqueryAsset'
        ];
    }

