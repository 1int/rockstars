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
        <p id="tactics-timer" style="display: none">10:00</p>
        <div id="board"></div>
        <div id="pos-number">Position 1/12</div>
        <!--<button class="btn btn-large btn-primary" id="btn-prev" style="display: none"><i class="glyphicon glyphicon-circle-arrow-left"></i> Previous</button>-->
      <!--  <button class="btn btn-large btn-primary" id="btn-next">Next <i class="glyphicon glyphicon-circle-arrow-right"></i></button> -->
    </div>


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

<svg height="0" xmlns="http://www.w3.org/2000/svg">
    <filter id="drop-shadow">
        <feGaussianBlur in="SourceAlpha" stdDeviation="4"/>
        <feOffset dx="12" dy="12" result="offsetblur"/>
        <feFlood flood-color="rgba(0,0,0,0.5)"/>
        <feComposite in2="offsetblur" operator="in"/>
        <feMerge>
            <feMergeNode/>
            <feMergeNode in="SourceGraphic"/>
        </feMerge>
    </filter>
</svg>


<?php
    $this->registerJsFile('/js/vendor/chess.min.js', ['position'=>View::POS_END, 'depends'=>[JqueryAsset::className()]], 'chessjs');
    $this->registerJsFile('/js/vendor/chessboard.min.js', ['position'=>View::POS_END], 'chessboardjs');
    $this->registerJsFile('/js/tactics.js', ['position'=>View::POS_END, 'depends'=>[JqueryAsset::className()]]);
?>
