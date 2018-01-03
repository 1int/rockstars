<?php

/* @var $this yii\web\View */

$this->title = 'Rockstars! — About';
$this->params['breadcrumbs'][] = 'About';
?>
<style>
    ul.love {
        list-style: none;
        padding-left: 10px;
    }
    ul.love li:before {
        font-family: 'Glyphicons Halflings';
        content: "\e005";
        margin: 10px 5px -10px -10px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="site-about">
    <h1>Rockstars! chess team</h1>

    <p>
        We hit F7 both on the guitar and on the chessboard
    </p>

    <p>Feel free to <a href="https://join.skype.com/gsi7b9fslT9y">join us in our Skype chat!</a></p>

    <div class="well">
        <h4>Here are some things we like:</h4>
        <ul class="love">
            <li>Playing on <a href="https://lichess.org" target="_blank">lichess.org</a></li>
            <li>Chess videos by <a href="https://www.youtube.com/channel/UCgCqYuLGzp9x1m2rX6iAS3g" target="_blank">Rick from Chess to Impress</a></li>
            <li>Chess commentary by <a href="https://www.youtube.com/channel/UC6hOVYvNn79Sl1Fc1vx2mYA" target="_blank">John Bartholomew</a>
                    and <a href="https://www.youtube.com/channel/UClV9nqHHcsrm2krkFDPPr-g" target="_blank">Simon Ginger GM Williams</a>
            <li>Chess streams in Russian by <a href="https://www.youtube.com/user/Crestbook" target="_blank">GM Sergey Shipov</a></li>
            <li>Legendary chess games by <a href="https://www.youtube.com/user/Super300476" target="_blank">Alexander Gelman</a></li>
        </ul>
    </div>

</div>
