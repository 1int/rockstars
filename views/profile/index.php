<?php
    /**
     * Crafted by Pavel Lint 01/05/2018
     * Mail to: pavel@1int.org
     */


    /* @var $this yii\web\View */

    $this->title = 'Rockstars! — Ratings';
    use miloschuman\highcharts\Highcharts;

    /** @var array $scores */
    /** @var array $labels */

?>

    <div id="tactics-chart" >
        <?= Highcharts::widget([
        'options' => [
            'legend'=> ['enabled' => false],
            'chart' => [
                'height' => 150,
                'width' => 400,
            ],
            'title' => false,
            'yAxis' => [
                'max'   => 25,
                'min' => 0,
                'Max' => 25,
                'labels' => ['enabled'=>false],
                'title' => false,
                'tickInterval' => 5,
            ],
            'xAxis' => [
               'labels' => ['enabled'=>false],
                'categories' => $labels,
            ],
            'series' => [
                ['name' => 'Result', 'data' => $scores, 'labels'=>$labels ],
            ],
            'plotOptions' =>
                ['series'=>
                 ['marker' => ['enabled'=>false]]
                ],

        ]
        ]);?>
    </div>