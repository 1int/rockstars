<?php

/* @var $this yii\web\View */

$this->title = 'Rockstars! — Ratings';
//use dosamigos\chartjs\ChartJs;
use miloschuman\highcharts\Highstock;

/** @var array $data */
/** @var array $labels */
/** @var int $rating */
/** @var int $dr rating change */
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div id="rating-holder">
                Team rating: <span id="the-rating"><?=$rating?></span>
                <?php if( $dr >= 0) { ?>
                <span class="rating-up">
                    +<?=$dr?>
                </span>
                <?php } else { ?>
                <span class="rating-down">
                    <?=$dr?>
                </span>
                <?php } ?>
            </div>
            <hr/>

            <?= Highstock::widget([
                'options' => [
                    'title' => ['text' => 'Rating history'],
                    'yAxis' => [
                        'title' => ['text' => 'Rating']
                    ],
                    'series' => [
                        ['name' => 'Blitz', 'data' => $data],
                    ]
                ]
            ]);?>
        </div>
    </div>
</div>
