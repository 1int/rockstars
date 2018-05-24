<?php
    /**
     * Crafted by Pavel Lint 24/05/2018
     * Mail to: pavel@1int.org
     */
?>

    <h2>Send sms to the team</h2>
    <form action="" method="POST" id="frm-add-game">
            <div class="form-group">
                <label for="smstext">SMS text:</label>
                <textarea rows="7" id="smstext" name="smstext" class="form-control" required="required"></textarea>
            </div>
        <div>
            <hr/>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <button type="submit" class="btn btn-primary">Send</button>
        </div>
    </form>