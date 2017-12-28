<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tourney".
 *
 * @property string $id
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
 *
 * @property Match[] $matches
 */
class Tourney extends ActiveRecord
{
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
            [['name', 'description', 'team1', 'team2', 'date'], 'required'],
            [['description'], 'string'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['team1players', 'team2players', 'team1logo', 'team2logo', 'team1name', 'team2name'], 'string', 'max' => 1028],
        ];
    }

    /**
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

    function getTimeControl() {
        return $this->time_control;
    }

}
