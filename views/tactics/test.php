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


?>


    <div id="start-test"><span>Start</span></div>
    <div id="countdown3" class="countdown" style="display: none">3</div>
    <div id="countdown2" class="countdown" style="display: none">2</div>
    <div id="countdown1" class="countdown" style="display: none">1</div>


    <div id="test-container" style="display: none">
        <ul id="answers-list">
            <li>1. —</li>
            <li>2. —</li>
            <li>3. —</li>
            <li>4. —</li>
            <li>5. —</li>
            <li>6. —</li>
            <li>7. —</li>
            <li>8. —</li>
            <li>9. —</li>
            <li>10. —</li>
            <li>11. —</li>
            <li>12. —</li>
        </ul>
        <p id="tactics-timer" style="display: none">10:00</p>
        <div id="board">
        </div>
        <div id="pos-number">Position 1/12</div>
        <!--<button class="btn btn-large btn-primary" id="btn-prev" style="display: none"><i class="glyphicon glyphicon-circle-arrow-left"></i> Previous</button>-->
      <!--  <button class="btn btn-large btn-primary" id="btn-next">Next <i class="glyphicon glyphicon-circle-arrow-right"></i></button> -->
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



<!-- 6/1 r1qr11k1/1b111pb1/p1pBpnpp/11111111/111PN111/1Q1111P1/PP111PBP/11RR11K1 -->


<?php ob_start();?>
        $("div#start-test").click(function(){
            $(this).hide();
            startCountdown();
        });
        //$("#tactics-timer").detach().appendTo($("ul.breadcrumb"));
        //$("#btn-prev").click(previousPosition);
        $("#btn-next").click(nextPosition);
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
