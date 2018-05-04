<?php
    /**
     * Crafted by Pavel Lint 04/05/2018
     * Mail to: pavel@1int.org
     */

    $this->title = 'Rockstars! — Test answers';
    $this->params['breadcrumbs'][] = 'Test answers';
?>
    <form method="post" action="/marina">
        <div class="form-group">
            <label for="admin-password">Test number:</label>
            <input id="test_id" name="test_id" class="form-control" type="text"/>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
        </div>
        <style>
            table#at tr td {
                padding: 5px 10px 5px 10px;
            }
        </style>
        <table id="at">
                <thead>
                    <tr>
                        <td>Number</td>
                        <td>Answer</td>
                        <td>Points</td>
                        <td>...</td>
                    </tr>
                </thead>
                <tbody>
                <?php for( $i = 0; $i < 12; $i++) { ?>
                    <tr>
                        <td># <?=($i+1)?></td>
                        <td><input name="answers[]" class="form-control" type="text" style="width: 80px" placeholder="Nf3"/></td>
                        <td><input name="points[]" class="form-control" type="text" style="width: 80px" placeholder="3"/></td>
                        <td><input name="dot<?=$i?>" type="checkbox" class="form-check-input"/></td>
                        <td></td>
                    </tr>
                <?php }?>
                </tbody>
        </table>
        <div class="form-group">
            <input type="submit" class="btn btn-submit"></input>
        </div>
    </form>

