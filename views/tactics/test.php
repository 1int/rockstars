<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    use yii\web\JqueryAsset;
    use yii\web\View;

    /**
     * Crafted by Pavel Lint 24/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level
     * @var TacticsTest $test
     * @var int $timeLeft
     * @var bool $isStarted
     * @var View $this
     */


    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number;

    $uid = Yii::$app->user->identity->getId();

?>


    <div id="start-test"><span>Start</span></div>
    <div id="countdown3" class="countdown" style="display: none">3</div>
    <div id="countdown2" class="countdown" style="display: none">2</div>
    <div id="countdown1" class="countdown" style="display: none">1</div>


    <div id="test-container" style="display: none">
        <ul id="answers-list">
            <?php for($i = 1; $i <= 12; $i++) { ?>
                <li><?=$i?>. <?=$test->answerFor($i, $uid);?></li>
            <?php } ?>
        </ul>
        <p id="tactics-timer" style="display: none">10:00</p>
        <div id="board">
        </div>
        <div id="pos-number">Position 1/12</div>
        <div id="results-mobile-holder">
        </div>
    </div>


<div id="promote-popup" style="display: none">
    <ul id="promote-white" style="display: none">
        <li><img src="/images/chesspieces/wikipedia/wN.png"</li>
        <li><img src="/images/chesspieces/wikipedia/wB.png"</li>
        <li><img src="/images/chesspieces/wikipedia/wR.png"</li>
        <li><img src="/images/chesspieces/wikipedia/wQ.png"</li>
    </ul>
    <ul id="promote-black">
        <li><img src="/images/chesspieces/wikipedia/bN.png"</li>
        <li><img src="/images/chesspieces/wikipedia/bB.png"</li>
        <li><img src="/images/chesspieces/wikipedia/bR.png"</li>
        <li><img src="/images/chesspieces/wikipedia/bQ.png"</li>
    </ul>
</div>

<?php ob_start();?>
        $("div#start-test").click(function(){
            $(this).hide();
            startCountdown();
        });
        if(isMobile()) {
            $("#answers-list").detach().appendTo($("#results-mobile-holder"));
            var width = $(window).width() - 40;
            $("#board").css({width:width + 'px'});
            board.resize();
        }
<?php $this->registerJs(ob_get_clean()); ?>

<?php ob_start();?>

var timeLeft = <?=$timeLeft?>;
var isStarted = <?=$isStarted ? 'true' : 'false'?>;
var fens = [];
var blackToMove = [];
<?php foreach($test->tacticsPositions as $p) { ?>
fens.push('<?=$p->fullFen?>');
blackToMove.push(<?=$p->dotdotdot? 'true':'false'?>);
<?php } ?>
<?php $this->registerJs(ob_get_clean(), View::POS_BEGIN); ?>


<link rel="stylesheet" href="/css/vendor/chessboard.min.css"/>
<link rel="stylesheet" href="/css/vendor/animate.css"/>

<?php
    $this->registerJsFile('/js/vendor/chess.min.js', ['position'=>View::POS_END, 'depends'=>[JqueryAsset::className()]], 'chessjs');
    $this->registerJsFile('/js/vendor/chessboard.min.js', ['position'=>View::POS_END], 'chessboardjs');
    $this->registerJsFile('/js/tactics.js?2', ['position'=>View::POS_END, 'depends'=>[JqueryAsset::className()]]);
?>
