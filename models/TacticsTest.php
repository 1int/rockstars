<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tactics_tests".
 *
 * @property int $id
 * @property string $level_id
 * @property int $number
 * @property bool $published
 * @property bool $allowGuest
 *
 * @property TacticsPosition[] $tacticsPositions
 * @property TacticsTestResult[] $tacticsResults
 * @property TacticsLevel $level
 */
class TacticsTest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tactics_tests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level_id'], 'required'],
            [['level_id'], 'integer'],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => TacticsLevel::className(), 'targetAttribute' => ['level_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTacticsPositions()
    {
        return $this->hasMany(TacticsPosition::className(), ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTacticsResults()
    {
        return $this->hasMany(TacticsTestResult::className(), ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(TacticsLevel::className(), ['id' => 'level_id']);
    }

    public function getNumber() {
        return $this->id;
    }

    public function clearAnswer($a) {
        $ret = preg_replace( '/[+!\. ]*/', '' , $a);
        $ret = preg_replace( '/^1/', '' , $ret);
        return strtolower($ret);
    }

    public function finish($userId, $refresh = false) {
        /** @var TacticsTestResult $result */
        $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:player_id',
            ['player_id'=>$userId, 'test_id'=>$this->id])->one();

        if( !$result->start || ($result->finish && !$refresh) ) {
            // not started or already finished, something is wrong
            return;
        }

        if( !$refresh ) {
            $result->finish = time();
        }
        $answers = TacticsAnswer::find()->where('player_id=:player_id AND test_id=:test_id',
            ['player_id'=>$userId, 'test_id'=>$this->id])->all();

        $score = 0;

        /** @var TacticsAnswer $answer  */
        foreach($answers as $answer) {
            if( $answer->clearAnswer == strtolower($answer->position->answer) ) {
                $score += $answer->position->points;
            }
        }
        $result->score = $score;
        $result->save();
    }

    /**
     * @param string $userId
     * @return bool
     */
    public function isFinishedBy($userId) {
        $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:uid',
                ['test_id'=>$this->id, 'uid'=>$userId])->one();
        if( !$result ) {
            return false;
        }
        /** @var TacticsTestResult $result */
        if( $result->finish ) {
            return true;
        }

        if( $result->timePassed() ) {
            $this->finish($userId);
            return true;
        }

        return false;
    }

    public function isInProgressFor($userId) {
        $result = TacticsTestResult::find()->where('test_id=:test_id AND player_id=:uid',
            ['test_id'=>$this->id, 'uid'=>$userId])->one();
        if( !$result ) {
            return false;
        }
        /** @var TacticsTestResult $result */
        if( $result->finish ) {
            return false;
        }

        if( $result->timePassed() ) {
            $this->finish($userId);
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isFinishedByCurrentUser() {
        if( Yii::$app->user->isGuest ) {
            return false;
        }
        return $this->isFinishedBy(Yii::$app->user->identity->getId());
    }

    /**
     * @param string $uid
     * @return int
     */
    public function scoreFor($uid) {
        $result = TacticsTestResult::find()->where('player_id = :uid AND test_id=:id',
            ['uid'=>$uid, 'id'=>$this->id])->one();
        if( !$result ) {
            return 0;
        }
        /** @var  TacticsTestResult $result */
        return $result->score;
    }

}
