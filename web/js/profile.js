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
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: {gameurl: url, gamedesc: text},
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        $("#modal-add-game").modal('hide');
        $input.val('');

        //TODO: add game locally
    }
});

$("#btn-close").click(function() {
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
    }
});

