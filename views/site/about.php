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
        <i> We hit F7 both on the guitar and on the chessboard! </i>
    </p>

    <br/>

    <div class="well">
        <h4>About us</h4
        <div>
            <p>We are <a href="/members">just a few friends</a> who learn and play chess together. <a href="/schedule">Usually we meet twice
                    a week</a> and give lectures to each other, studying different aspects of chess.  This proved to be
            quite effective as our skill grows over time. If you want to study endgame, tactics or positional
            play with us you are welcome to join! But I warn you: all lectures are <b>in Russian</b>. Добро пожаловать!</p>

            <p>Feel free to <a href="https://join.skype.com/gsi7b9fslT9y">join us in our Skype chat</a>
            if you like to discuss chess and share interesting chess moments.
            </p>
        </div>
    </div>

    <div class="well">
        <h4>Team events</h4>
        <div>
            <p>We love to participate in team battles!
            On our <a href="/tourney">team battles</a> page you can view our team battle history or even create your own event!
            Just fulfil  the 'New Team Battle' form and you will be redirected to your tournament page. Then simply play the games
            on lichess and hit 'update results' when you're finished! It's that easy.
            </p>
        </div>
    </div>

    <div class="well">
        <h4>Website</h4>
        <div>
            <p>This website is our home and it is open source software <a href="https://github.com/1int/rockstars">hosted on github</a>.
               As with all open source software, you are free to copy it, submit your code and ideas.
            </p>
        </div>
    </div>


    <div class="well">
        <h4>A few things we really like</h4>
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
