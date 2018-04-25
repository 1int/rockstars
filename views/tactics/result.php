<?php
    use app\models\TacticsTest;
    use app\models\TacticsLevel;
    /**
     * Crafted by Pavel Lint 25/04/2018
     * Mail to: pavel@1int.org
     *
     * @var TacticsLevel $level;
     * @var TacticsTest $test;
     */

    $this->title = 'Rockstars  — Tactics: ' . $level->name . ' #' . $test->id;
    //$this->params['breadcrumbs'][] = 'Tactics Tests';
    $this->params['breadcrumbs'][] = ['label'=>'Tactics Tests', 'url'=>'/tactics'];
    $this->params['breadcrumbs'][] = ['label'=> $level->name, 'url'=>'/tactics/' . $level->slug];
    $this->params['breadcrumbs'][] = 'Test ' . $test->number . ' results';

?>
<div id="score-content">
    <div class="col-lg-9">
        <h2 class="score-title">Your score</h2>
        <span id="your-result">21</span>
        <div id="tactics-comment">You are doing O.K.</div>
    </div>


    <div class="col-lg-3">
    <table id="tactics-best" style="text-align: left">
        <caption>Best results</caption>
        <tbody>
            <tr><td><a href="https://lichess.org/@/saksham" target="_blank">saksham</a></td><td>5.0&nbsp;-&nbsp;1.0</td></tr>
            <tr><td><a href="https://lichess.org/@/Tatiana91" target="_blank">Tatiana91</a></td><td>4.5&nbsp;-&nbsp;1.5</td></tr>
            <tr><td><a href="https://lichess.org/@/DavidCecxladzee" target="_blank">DavidCecxladzee</a></td><td>4.0&nbsp;-&nbsp;2.0</td></tr>
            <tr><td><a href="https://lichess.org/@/kayisi" target="_blank">kayisi</a></td><td>2.5&nbsp;-&nbsp;3.5</td></tr>
            <tr><td><a href="https://lichess.org/@/GlebKl" target="_blank">GlebKl</a></td><td>2.0&nbsp;-&nbsp;4.0</td></tr>
        </tbody>
    </table>
    </div>
</div>