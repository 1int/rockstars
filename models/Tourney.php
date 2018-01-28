<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tourney".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $team1name
 * @property string $team1logo
 * @property string $team1players
 * @property string $team2name
 * @property string $team2logo
 * @property string $team2players
 * @property string $date
 * @property string $timeControl
 * @property string $url
 * @property int $totalRounds
 * @property bool $isFinished
 * @property string $timeControlFullString
 * @property string $team1PlayersWithLinks
 * @property string $team2PlayersWithLinks
 * @property Match[] $matches
 * @property PlayerScore[] $bestPlayers
 */
class Tourney extends ActiveRecord
{
    /**
     * @var PlayerScore[] $_bestPlayers
     */
    private $_bestPlayers = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tourney';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team1name', 'team2name', 'team1players', 'team2players', 'date', 'slug'], 'required'],
            [['date', 'description', 'time_control'], 'safe'],
        ];
    }

    /**'
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'team1' => 'Team1',
            'team2' => 'Team2',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatches()
    {
        return $this->hasMany(Match::className(), ['tourney_id' => 'id']);
    }

    /**
     * @param $player string
     * @return Match[]
     */
    public function getPlayerMatches($player) {
        return Match::find()->where('tourney_id=:id AND (white=:player OR black=:player)', ['id'=>$this->id, 'player'=>$player])->all();
    }

    /**
     * @return array
     */
    public function getPlayersOfTeam1() {
        return explode(',', $this->team1players);
    }

    /**
     * @return array
     */
    public function getPlayersOfTeam2() {
        return explode(',', $this->team2players);
    }

    public function generateRounds() {
        Match::deleteAll('tourney_id=' . $this->id);

        $players1 = $this->getPlayersOfTeam1();
        $players2 = $this->getPlayersOfTeam2();
        shuffle($players1);
        shuffle($players2);
        $count = count($players2);

        for($round = 1; $round <= $count; $round++) {
            $p1index = -1;
            foreach ($players1 as $p1)
            {
                $p1index++;

                $whiteFirst = rand(0, 1) > 0;
                $p2 = $players2[($p1index + $round - 1) % $count];

                $match = new Match();
                $match->white = $whiteFirst ? $p1 : $p2;
                $match->black = $whiteFirst ? $p2 : $p1;
                $match->round = $round;
                $match->tourney_id = $this->id;
                $match->save();

                //and now the rematch
                $rematch = new Match();
                $rematch->white = $whiteFirst ? $p2 : $p1;
                $rematch->black = $whiteFirst ? $p1 : $p2;
                $rematch->round = $round;
                $rematch->tourney_id = $this->id;
                $rematch->save();
            }
        }
    }

    /**
     * @return string
     */
    function getTimeControl() {
        return $this->time_control;
    }

    /**
     * @param string $slug
     * @return Tourney|null
     */
    static function findBySlug($slug) {
        return Tourney::find()->where('slug=:slug',['slug'=>$slug])->one();
    }

    /**
     * @return string the tourney URL on the site
     */
    public function getUrl() {
        return sprintf('/tourney/%s', $this->slug);
    }

    /**
     * @return int
     */
    public function getTotalRounds() {
        return count($this->getPlayersOfTeam2());
    }

    /**
     * @param int $round round number (1-based)
     * @return Match[]
     */
    public function getMatchesOfRound($round) {
        return Match::find()->where('tourney_id = :tourney_id AND round = :round',
                ['tourney_id'=>$this->id, 'round'=>$round])->all();
    }

    /**
     * @return bool
     */
    public function getIsFinished() {
        return Match::find()->where('tourney_id = :tourney_id AND href IS NULL',
            ['tourney_id'=>$this->id])->count() == 0;
    }

    /**
     * @param int $round round number (1-based)
     * @return bool
     */
    public function isRoundFinished($round) {
        return Match::find()->where('tourney_id = :tourney_id AND href IS NULL AND round=:round',
            ['tourney_id'=>$this->id, 'round'=>$round])->count() == 0;
    }


    /**
     * @return string
     */
    public function getTimeControlFullString() {
        return $this->timeControl . ' blitz'; // someday we may play rapid or classical games
    }


    /**
     * @return string
     */
    public function getTeam1PlayersWithLinks() {
        $ret = array_map(function($e) {
            return sprintf('<a href="https://lichess.org/@/%s" target="_blank">%s</a>', $e, $e);
        }, $this->getPlayersOfTeam1());
        return implode(', ', $ret);
    }

    /**
     * @return string
     */
    public function getTeam2PlayersWithLinks() {
        $ret = array_map(function($e) {
            return sprintf('<a href="https://lichess.org/@/%s" target="_blank">%s</a>', $e, $e);
        }, $this->getPlayersOfTeam2());
        return implode(', ', $ret);
    }

    /**
     * @param int $round round number (1-based)
     * @param string $separator
     * @return string
     * @throws \Exception
     */
    public function getRoundScore($round, $separator = ' : ') {
        return $this->getScoreForMatches(Match::find()->where('tourney_id = :tourney_id AND round = :round',
            ['tourney_id'=>$this->id, 'round'=>$round])->all(), $separator);
    }

    /**
     * @param string $separator
     * @return string
     * @throws \Exception
     */
    public function getTotalScore($separator = ' : ') {
        return $this->getScoreForMatches($this->matches, $separator);
    }

    /**
     * @param Match[] $matches
     * @param string $separator
     * @return string
     * @throws \Exception
     */
    protected function getScoreForMatches($matches, $separator) {
        $players1 = $this->getPlayersOfTeam1();
        $team1score = 0;
        $team2score = 0;

        foreach($matches as $m) {
            switch($m->result) {
                case null:
                    continue;
                case 0.5:
                    $team1score += 0.5;
                    $team2score += 0.5;
                    break;
                case 1:
                    if( array_search($m->white,$players1,false) !== false ) {
                        $team1score++;
                    }
                    else {
                        $team2score++;
                    }
                    break;
                case -1:
                    if( array_search($m->white,$players1,false) !== false ) {
                        $team2score++;
                    }
                    else {
                        $team1score++;
                    }
                    break;
                default:
                    throw new \Exception("Everything's made to be broken");
            }
        }

        return sprintf("%.1f%s%.1f", $team1score, $separator, $team2score);
    }

    /**
     * @return PlayerScore[]
     */
    public function getBestPlayers() {

         if( $this->_bestPlayers != null ) {
             return $this->_bestPlayers;
         }
         $scores = [];
         $players = array_merge($this->getPlayersOfTeam1(), $this->getPlayersOfTeam2());

         foreach($players as $p) {
             $matches = $this->getPlayerMatches($p);

             $res = new PlayerScore($p);
             $res->score = 0;
             foreach($matches as $m) {
                 $res->score += $m->getScoreForPlayer($p);
             }
             $res->scoreString = sprintf("%.2f&nbsp;-&nbsp;%.2f", $res->score, 2*$this->totalRounds - $res->score);
             $scores[] = $res;
         }


         // calculate average opponent points (to resolve equal points situation)
         foreach($scores as &$s) {
             $matches = $this->getPlayerMatches($s->player);
             $wins = 0;
             $totalRating = 0;
             foreach($matches as $m) {
                 if( $m->getScoreForPlayer($s->player) == 1 ) {
                     $oppName = $m->white == $s->player ? $m->black : $m->white;

                     $opp = array_filter($scores,
                         function ($e) use ($oppName) {
                             return $e->player == $oppName;
                         }
                     );
                     $opp = reset($opp);
                     if( !isset($opp) || !isset($opp->score)) {
                         // opponent not found, probably somebody replaced him for the match
                         continue;
                     }
                     $totalRating += $opp->score;
                     $wins++;
                 }
             }

             if( $wins > 0 ) {
                 $s->avgOpponentScore = $totalRating / $wins;
             }
             else {
                 $s->avgOpponentScore = 0;
             }
         }

         uasort( $scores, function($a, $b) {
            /** @var PlayerScore $a, $b */
            if($a->score == $b->score) {
                return $a->avgOpponentScore <= $b->avgOpponentScore;
            }
            else {
                return $a->score <= $b->score;
            }
         });

        $this->_bestPlayers = $scores;
        return $scores;
    }
}

class PlayerScore {
    public $player;
    public $score;
    public $scoreString;
    public $avgOpponentScore;

    function __construct($player) {
        $this->player = $player;
    }
}
