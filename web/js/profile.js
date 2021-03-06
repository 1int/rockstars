/**
 * Created by Lint on 07/05/2018.
 */

$("#avatar-input").change(function() {
    if( this.files[0] ) {
        document.getElementById('avatar-image').src = window.URL.createObjectURL(this.files[0]);
        $("#avatar-form").submit();
    }
});

$("#avatar-form").submit(function(e) {
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: new FormData(this),
        processData: false,
        contentType: false,
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
    e.preventDefault(); // avoid to execute the actual submit of the form.
});


$("#profile-description.owner").click(divClicked);

// This is from Stackoverflow: https://stackoverflow.com/questions/2441565/how-do-i-make-a-div-element-editable-like-a-textarea-when-i-click-it
function divClicked() {
    var divHtml = $(this).html().replace('<span class="edit-link"></span>', '').replace('(click to edit your bio)', '').trim();
    var width = $(this).width();
    var height = $(this).height();
    var editableText = $("<textarea />");
    editableText.css({width: width, height: height + 50});
    editableText.attr("id", "profile-description-edit");
    editableText.val(divHtml.trim());
    editableText.eq(0).scrollTop = 0;
    $(this).replaceWith(editableText);
    editableText.focus();
    setCaretPosition('profile-description-edit', 0);
    editableText.blur(editableTextBlurred);
}

function editableTextBlurred() {
    var html = $(this).val();
    var viewableText = $("<div>");
    viewableText.addClass("owner");
    viewableText.attr("id", "profile-description");
    viewableText.html(html + ' <span class="edit-link"></span>');
    $(this).replaceWith(viewableText);
    $(viewableText).click(divClicked);

    $.ajax({
        type: "POST",
        url: window.location.href,
        data: {description: html},
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}

function setCaretPosition(elemId, caretPos) {
    var elem = document.getElementById(elemId);

    if(elem != null) {
        if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else
                elem.focus();
        }
    }
}

$("#btn-add-game").click(function() {
    var $input = $("input[name=gameurl]");
    var $text  = $("textarea[name=gamedesc]");
    var url = $input.val();
    var text = $text.val();

    if( url.trim() != "" && text.trim() != "" ) {
        var regex = /https:\/\/lichess.org\/([a-zA-Z0-9]{7,20})/g;
        var match = regex.exec(url);
        var id = match[1];
        if( !id ) {
            alert("Invalid game url");
            return;
        }
        else {
            id = id.substr(0, 8);
        }
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {gameurl: url, gamedesc: text},
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            },
            success: function(theid, status, error) {
                var template = '<div class="notable-game-container owner" data-gid="' + theid + '">' +
                    '<div class="notable-game">' +
                    '<iframe width="345" height="243" frameborder="0" src="https://lichess.org/embed/' + id + '?theme=auto&amp;bg=auto"></iframe></div>' +
                    '<span class="nb-description">' + text +
                    '</span>' +
                    '<i class="btn-close glyphicon glyphicon-remove"></i>' +
                    '</div>';

                $("#notable-games-container").append(template);
                $("#notable-games-container span:first-child").html('Click the game and use ← → keys to navigate');
            }
        });
        $("#modal-add-game").modal('hide');
        $input.val('');


    }
});


$("#btn-change-password").click(function() {
    var $pass = $("input[name=pass]");
    var $repeat  = $("input[name=repeat]");
    var pass = $pass.val();
    var repeat = $repeat.val();

    if( pass.trim() == '' || repeat.trim() == '' ) {
        return;
    }

    if( pass != repeat ) {
        alert('Passwords do not match!');
        return;
    }

    $.ajax({
        type: "POST",
        url: window.location.href,
        data: {pass: pass, repeat: repeat},
        error: function (xhr, status, error) {
            alert(xhr.responseText);
        }
    });
    $("#modal-change-password").modal('hide');
    $pass.val('');
    $repeat.val('');
});

$(document).on('click', '.btn-close', function() {
    if( window.confirm("Are you sure?") ) {
        var $game = $(this).closest('.notable-game-container');
        var game_id = $game.attr('data-gid');
        $.ajax({
            type: "DELETE",
            url: window.location.href,
            data: {gid: game_id},
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        $game.remove();
        if( $('.notable-game-container').length == 0 ) {
            $("#notable-games-container span:first-child").html('No games');
        }
    }
});

function isMobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true;
    }
    return false;
}

$(function() {
    $("i.star-on").css({color: "#333"});
    if( isMobile() ) {
        $("#profile-description").detach().prependTo("#profile-left-column");
        $("#profile-title").detach().prependTo("#profile-left-column");
    }

});

