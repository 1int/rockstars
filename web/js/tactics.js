/**
 * Created by Lint on 25/04/2018.
 */


    var countdown = 3;
    var initialSeconds = 600; //TODO: get from the html
    var timerRef;
    var currentPosition = 0;
    const TOTAL_POSITIONS = 12; //TODO: same

    function startCountdown() {
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
            return;
        }
    }

    function submitAnswer() {
        var a = $("input#answer").val();
        $.post(window.location.href.toString() + "/answer", {answer: a, position: currentPosition});
    }

    function nextPosition() {
        if( currentPosition == TOTAL_POSITIONS - 1 ) {
            $("#btn-next").hide();
            $("#btn-prev").hide();
            var a = $("input#answer").val();
            $.post(window.location.href.toString() + "/answer", {answer: a, position: currentPosition}).done(function() {
                debugger;
                finish();
            });
            return;
        }
        submitAnswer();
        $("input#answer").val('');
        currentPosition++;
        $("#btn-prev").show();

        if( currentPosition == TOTAL_POSITIONS - 1 ) {
            $("#btn-next").html("Finish");
        }

        //
       // var src = $("#img-position").attr("src");
       // src = src.replace(/(\/images\/tests\/test[\d]*_)([\d]*)(.jpeg)/g, "$1" + (currentPosition+1).toString() + "$3");
        $("#img-position").attr("src", window.location.href + "/image" + (currentPosition+1).toString());

    }

    function previousPosition() {
        if( currentPosition <= 0 ) {
            return;
        }

        currentPosition--;
        if( currentPosition == 0 ) {
            $("#btn-prev").hide();
        }
        if( currentPosition == TOTAL_POSITIONS - 2 ) {
            $("#btn-next").html('Next <i class="glyphicon glyphicon-circle-arrow-right"></i>');
        }
        $("input#answer").val('');

        //var src = $("#img-position").attr("src");
        //src = src.replace(/(\/images\/tests\/test[\d]*_)([\d]*)(.jpeg)/g, "$1" + (currentPosition+1).toString() + "$3");
        $("#img-position").attr("src", window.location.href + "/image" + (currentPosition+1).toString());
    }

    function startTest() {
        startTimer();
        $.post(window.location.href.toString() + "/start");
        $("#test-container").show();
        $("#tactics-timer").show();
    }

    function finish() {
        $.post(window.location.href.toString() + "/finish").done(function() {
           window.location.href = window.location.href + "/result";
        });
    }