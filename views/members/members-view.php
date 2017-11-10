<?php

use app\models\Member;
use app\assets\TextillateAsset;

/** @var $this yii\web\View */
/** @var Member[] $members */

$this->title = 'Rockstars! — Members';
$i = 0;
?>
<div class="site-index">
    <div class="body-content">
        <div class="rockstars-animated">
            <h1 id="rockstars-animated" data-in-effect="flip">Rockstars!</h1>
        </div>
        <div id="members" style="display: none">
            <div class="row">
                <?php foreach($members as $member) { ?>
                <div class="member col-lg-4" style="display: none">
                    <h2><?=$member->name?></h2>
                    <img src="<?=$member->avatar?>" class="avatar" alt="<?=$member->name?>"/>
                    <p class="member-text"><?=$member->bio?></p>
                    <p><a class="btn btn-default" href="<?=$member->link?>" target="_blank">@<?=$member->username?></a></p>
                </div>
                <?php
                $i++;
                if( $i % 3 == 0 ) {
                    print "\n</div>\n<div class='row'>\n";
                }
                } ?>
            </div>
        </div>
    </div>
</div>


<?php
    $asset = TextillateAsset::register($this);
    $asset->publish(Yii::$app->assetManager);
    ob_start();
?>
    $h1 = $("#rockstars-animated");
    $h1.textillate( {
        initialDelay: 200,
        in: { effect: 'flip' },
        out: { effect: 'tada', shuffle: false, delay: 20}
    });

    $h1.on('inAnimationEnd.tlt', function () {
        $h1.textillate('out');
    });

    $h1.on('outAnimationEnd.tlt', function () {
        $h1.fadeOut(200, function() {
            $("#members").show();
            $("div.member").each(function(index) {
                $(this).delay(index*200).fadeIn();
            });
        });
    });

<?php
    $this->registerJs(ob_get_clean());
?>



