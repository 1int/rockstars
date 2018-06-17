/**
 * Created by Lint on 09/06/2018.
 */
var board,
    game = new Chess();

var countdown = 3;
var initialSeconds = timeLeft;
var timerRef;
var currentPosition = 0;
const TOTAL_POSITIONS = 12;
const AFTERMOVE_DELAY = 1000;
var answers = [];
var disableMoves = false;
var disableStartTime = 0;


var removeGreySquares = function() {
    var $el = $('#board .square-55d63');
    if( $el.hasClass('move-dest') ) {
        $el.removeClass('move-dest');
    }
}

var greySquare = function(square) {
    $('#board .square-' + square).addClass("move-dest");
};

var onDragStart = function(source, piece) {
    // do not pick up pieces if the game is over
    // or if it's not that side's turn
    if (game.game_over() === true ||
        (game.turn() === 'w' && piece.search(/^b/) !== -1) ||
        (game.turn() === 'b' && piece.search(/^w/) !== -1) ||
        disableMoves) {
        return false;
    }

    var square = source;
    var moves = game.moves({
        square: square,
        verbose: true
    });

    // exit if there are no moves available for this square
    if (moves.length === 0) return;

    // highlight the possible squares for this piece
    for (var i = 0; i < moves.length; i++) {
        greySquare(moves[i].to);
    }

    $(".piece-417db").each(function() {
        if( this.style.display == 'none' ) {
            $(this).addClass("animated enlarge");
        }
    });
};

var latestMove = null;
var doAnimate = false;
var isPromoting = false;
var promote = null;

var onDrop = function(source, target) {
    removeGreySquares();
    $(".piece-417db").removeClass("animated enlarge");




    var piece = game.get(source).type;
    var source_rank = source.substring(2,1);
    var target_rank = target.substring(2,1);
    if (piece === 'p' &&
        ((source_rank === '7' && target_rank === '8') || (source_rank === '2' && target_rank === '1'))) {

        isPromoting = true;
        promote = {from: source, to: target};

        if( game.turn() == 'b' ) {
            $("#promote-black").show();
            $("#promote-white").hide();
        }
        else {
            $("#promote-black").hide();
            $("#promote-white").show();
        }

        $("#promote-popup").fadeIn();
        return true;
    }


        // see if the move is legal
    var move = game.move({
        from: source,
        to: target,
        promotion: 'q' // NOTE: always promote to a queen for example simplicity
    });


    // illegal move
    if (move === null)
        return 'snapback';
    else {
        latestMove = target;
        submitAnswer(moveToString(move));
    }
};

var moveToString = function(move, promoted = '') {
    var strMove = '';
    if( move.piece != 'p' ) {
        strMove = move.piece.toUpperCase() + move.to;
    }
    else {
        var source_column = move.from.substr(0,1);
        var target_column = move.to.substr(0,1);
        if( source_column == target_column ) {
            strMove = move.to;
        }
        else {
            strMove = source_column + target_column;
        }
        if( promoted ) {
            strMove += '=' + promoted.toUpperCase();
        }
    }
    return strMove;
};


var onMoveOver = function() {
    $(".piece-417db").each(function() {
        if($(this).closest('.square-55d63').data('square') != latestMove) {
            if( Math.random() >= 0.5 ) {
                $(this).addClass('animated chessdrop');
            }
            else {
                $(this).addClass('animated chessdrop-left');
            }
        }
    });

    setTimeout(function() {
        $(".piece-417db").removeClass("animated chessdrop chessdrop-left");
    }, 500);
};

var onSnapEnd = function() {
    if( !isPromoting ) {
        onMoveOver();
        board.position(game.fen(), true);
    }
};

var makePromotionMove = function(figure) {
    $("#promote-popup").hide();
    var move = game.move({
        from: promote.from,
        to: promote.to,
        promotion: figure
    });
    board.position('clear', false);
    board.position(game.fen(), false);
    latestMove = promote.to;
    submitAnswer(moveToString(move, figure));
    isPromoting = false;
    promote = null;

    onMoveOver();
};

var cfg = {
    draggable: true,
    position: '',
    onDragStart: onDragStart,
    onDrop: onDrop,
    onSnapEnd: onSnapEnd,
    moveSpeed: 0,
    appearSpeed: 0,
    snapbackSpeed: 0
};
board = ChessBoard('board', cfg);


$("#promote-popup").prependTo($("#board div"));

$("#promote-popup ul li").click(function() {
    var index = $(this).index();
    var figure = ['n', 'b', 'r', 'q'][index];
    makePromotionMove(figure);
});

//////////////


function startCountdown() {
    if( isStarted ) {
        $("#countdown").hide();
        startTest();
        return;
    }
    $.post(window.location.href.toString() + "/start");
    $("#countdown" + countdown).html(countdown).show().css({"font-size": "300px", "color":"rgba(33,33,33,0)"});
    setTimeout(nextNumber, 1000);
}

function nextNumber() {
    $("#countdown" + countdown).remove();
    countdown--;
    $("#countdown" + countdown).html(countdown).show().css({"font-size": "300px", "color":"rgba(33,33,33,0)"});
    if (countdown > 0) {
        setTimeout(nextNumber, 1000);
    }
    else {
        startTest();
    }
}

function startTimer() {
    timerRef = setInterval(updateTimer, 1000);
}

function updateTimer() {
    initialSeconds--;

    var minutes = parseInt(initialSeconds/60);
    var seconds = initialSeconds % 60;
    if( minutes < 10 ) {
        minutes = "0" + minutes.toString();
    }
    if( seconds < 10 ) {
        seconds = "0" + seconds.toString();
    }
    $("#tactics-timer").html( minutes.toString() + ":" + seconds.toString());
    if( initialSeconds == 0 ) {
        finish();
        clearInterval(timerRef);
    }
}

function submitAnswer(answer) {
    disableMoves = true;
    disableStartTime = (new Date()).getTime();
    $("#answers-list").find('li').eq(currentPosition).html((currentPosition+1).toString() + ". " + answer);
    $.post(window.location.href.toString() + "/answer", {answer: answer, position: currentPosition}).done(function() {
        var now = (new Date()).getTime();
        var diff = disableStartTime - now + AFTERMOVE_DELAY;
        if( diff > 0 ) {
            setTimeout( nextPosition, diff );
        }
        else {
            nextPosition();
        }
    });
}


function nextPosition() {
    if( currentPosition == TOTAL_POSITIONS - 1 ) {
        finish();
        return;
    }
    currentPosition++;

    var animated = (blackToMove[currentPosition] == blackToMove[currentPosition-1]);
    board.position(fens[currentPosition], animated);
    board.orientation(blackToMove[currentPosition] ? 'black':'white');
    game.load(fens[currentPosition]);
    $("#pos-number").html("Position " + (currentPosition+1) + "/12")
    disableMoves = false;
}

function startTest() {
    startTimer();
    $("#board").show();
    board.position(fens[currentPosition], true);
    game.load(fens[currentPosition]);
    board.orientation(blackToMove[currentPosition] ? 'black':'white');
    $("#btn-next").show();
    $("#test-container").show();
    $("#tactics-timer").show();
}

function finish() {
    $.post(window.location.href.toString() + "/finish").done(function() {
        window.location.href = window.location.href + "/result";
    });
}