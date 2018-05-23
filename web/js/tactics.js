/**
 * Created by Lint on 25/04/2018.
 */


    var countdown = 3;
    var initialSeconds = timeLeft;
    var timerRef;
    var currentPosition = 0;
    const TOTAL_POSITIONS = 12;
    var answers = [];

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
                finish();
            });
            return;
        }
        submitAnswer();
        answers[currentPosition] = $("input#answer").val();
        currentPosition++;
        if( answers[currentPosition] !== undefined ) {
            $("input#answer").val(answers[currentPosition]);
        }
        else {
            $("input#answer").val('');
        }

        $("#btn-prev").show();

        if( currentPosition == TOTAL_POSITIONS - 1 ) {
            $("#btn-next").html("Finish");
        }

        $("#img-position").attr("src", window.location.href + "/image" + (currentPosition+1).toString());
    }

    function previousPosition() {
        if( currentPosition <= 0 ) {
            return;
        }

        var currentAnswer = $("input#answer").val();
        answers[currentPosition] = currentAnswer;

        if( currentPosition == TOTAL_POSITIONS -1 ) {
            submitAnswer();
        }

        currentPosition--;
        if( currentPosition == 0 ) {
            $("#btn-prev").hide();
        }
        if( currentPosition == TOTAL_POSITIONS - 2 ) {
            $("#btn-next").html('Next <i class="glyphicon glyphicon-circle-arrow-right"></i>');
        }
        if( answers[currentPosition] !== undefined ) {
            $("input#answer").val(answers[currentPosition]);
        }
        else {
            $("input#answer").val('');
        }

        $("#img-position").attr("src", window.location.href + "/image" + (currentPosition+1).toString());
    }

    function startTest() {
        startTimer();
        $("#img-position").attr("src", window.location.href + "/image1");
        $("#test-container").show();
        $("#tactics-timer").show();
    }

    function finish() {
        $.post(window.location.href.toString() + "/finish").done(function() {
           window.location.href = window.location.href + "/result";
        });
    }