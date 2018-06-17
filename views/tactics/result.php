<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    use app\models\TacticsTestResult;
    use yii\web\View;

    /**
     * Crafted by Pavel Lint 25/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level;
     * @var TacticsTest $test;
     * @var TacticsTestResult $result;
     * @var TacticsTestResult[] $highscores;
     * @var yii\web\View $this
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number . ' results';

?>
<div id="score-content">
    <div id="result-answers" class="col-sm-3">
        <div id="result-board"></div>
        <?php
            $uid = Yii::$app->user->getId();
            $i = 1;
            foreach($test->tacticsPositions as $p) {
                $answer = $p->answerForPlayer($uid);
                $isCorrect = $p->isAnswerCorrect($answer);
                $skip = $answer == null;
                printf("<div class='result-answer %s'>\n", $isCorrect ? "correct" : "wrong");
                if( $skip ) {
                    printf('<span><i class="glyphicon glyphicon-remove"></i> <p>%d.</p> — <b>%s</b></span>',
                                                                                            $i, $p->prettyAnswer);
                }
                else {
                   if( !$isCorrect ) {
                       printf('<span><i class="glyphicon glyphicon-remove"></i> <p>%d.</p> <s>%s</s> <b>%s</b></span>',
                           $i, $answer->answer, $p->prettyAnswer);
                   }
                   else {
                       printf('<span><i class="glyphicon glyphicon-ok"></i> <b><p>%d.</p> %s</b></span>', $i, $p->prettyAnswer);
                   }
                }
                print("</div>\n");
                $i++;
            }
        ?>
    </div>

    <div class="col-sm-6">
        <h2 class="score-title">Your score</h2>
        <span id="your-result"><?=$result->score?></span>
        <!--<div id="tactics-comment">You are doing O.K.</div>-->
    </div>


    <div class="col-sm-3">
        <table id="tactics-best" style="text-align: left">
            <caption>Best&nbsp;results</caption>
            <tbody>
                <?php foreach($highscores as $r) { ?>
                    <tr><td><img class="round" src="<?=$r->player->smallAvatar?>"></td><td><?=$r->player->usernameWithLink?></td><td><?=$r->score?></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
            var fens = [];
            var blackToMove = [];
            var moves = [];
            var prettyMoves = [];
        <?php foreach($test->tacticsPositions as $p) { ?>
            fens.push('<?=$p->fullFen?>');
            blackToMove.push(<?=$p->dotdotdot? 'true':'false'?>);
            moves.push('<?=$p->stockfish_answer?>');
            prettyMoves.push('<?=$p->answer?>');
        <?php } ?>
    </script>
</div>

<link rel="stylesheet" href="/css/vendor/chessboard.min.css"/>
<?php $this->registerJsFile('/js/vendor/chessboard.min.js', ['position'=>View::POS_END], 'chessboardjs'); ?>

<?php ob_start(); ?>

    var board = new ChessBoard('result-board', {position: 'start'});
    var moveTimer = setTimeout(showMove, 1300);
    var goBackTimer = null;

    function showPosition(index) {
        board.position(fens[index], false);
        board.orientation( blackToMove[index] ? 'black':'white' );
    }

    $("div.result-answer").click(function() {
        if( !$(this).hasClass("selected") ) {
            $("div.result-answer").removeClass("selected");
            $(this).addClass("selected");
            var index = $(this).index() - 1;
            showPosition(index);
            clearTimeout(moveTimer);
            clearTimeout(goBackTimer);
            moveTimer = setTimeout(showMove, 800);
        }
    });

    var gotWrongPosition = false;
    $('div.result-answer').each(function() {
        if( $(this).hasClass('wrong') && !gotWrongPosition ) {
            var index = $(this).index() - 1;
            $(this).addClass('selected');
            gotWrongPosition = true;
            showPosition(index);
        }
    });

    if( !gotWrongPosition ) {
        $('div.result-answer').eq(0).addClass('selected');
        showPosition(0);
    }

    function showMove() {
        $elem = $("div.result-answer.selected");
        var index = $elem.index() - 1;
        board.move(moves[index]);
        clearTimeout(moveTimer);
        moveTimer = null;
        goBackTimer = setTimeout(resetBoard, 3000);
        setTimeout( showPromotion, 300 );
    }

    function resetBoard() {
        board.position(fens[currentPosition()], true);
        clearTimeout(goBackTimer);
        goBackTimer = null;
        moveTimer = setTimeout(showMove, 3000);
    }

    function currentFen() {
        return fens[currentPosition()];
    }

    function currentPrettyAnswer() {
        return prettyMoves[currentPosition()];
    }

    function currentAnswer() {
        return moves[currentPosition()];
    }

    function currentPosition() {
        return $("div.result-answer.selected").index() - 1;
    }

    function isPromotion() {
        return currentPrettyAnswer().indexOf('=') > 0;
    }

    function showPromotion() {
        if( isPromotion() ) {
           var position = board.position();
           var square = currentAnswer().substr(-2);
           var promoted = board.orientation().substr(0,1) + currentPrettyAnswer().substr(-1).toUpperCase();
           position[square] = promoted;
           board.position(position, true);
        }
    }

<?php $this->registerJs(ob_get_clean(), View::POS_END);