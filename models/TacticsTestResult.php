<?php

namespace app\models;

use Yii;
use  \yii\db\ActiveRecord;

/**
 * This is the model class for table "tactics_results".
 *
 * @property string $test_id
 * @property int $player_id
 * @property string $start
 * @property string $finish
 * @property int $score
 *
 * @property TacticsTest $test
 * @property Member $player
 */
class TacticsTestResult extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tactics_results';
    }

    public static function primaryKey() {
        return array('test_id', 'player_id');
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'player_id', 'score'], 'required'],
            [['test_id', 'player_id', 'score'], 'integer'],
            [['start', 'finish'], 'safe'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => TacticsTest::className(), 'targetAttribute' => ['test_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'test_id' => 'Test ID',
            'player_id' => 'Player ID',
            'start' => 'Start',
            'finish' => 'Finish',
            'score' => 'Score',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(TacticsTest::className(), ['id' => 'test_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Member::className(), ['id' => 'player_id']);
    }

    /**
     * @return bool
     */
    public function timePassed() {
        return time() - doubleval($this->start) > 60 * $this->test->level->time;
    }
}
