<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
use RockstarsApp;

/**
 * This is the model class for table "tactics_answers".
 *
 * @property string $test_id
 * @property int $player_id
 * @property int $position_id
 * @property string $answer
 * @property string $clearAnswer
 * @property string $timestamp
 *
 * @property Member $player
 * @property TacticsPosition $position
 * @property TacticsTest $test
 */
class TacticsAnswer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tactics_answers';
    }

    public static function primaryKey() {
        return array('position_id', 'player_id');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'player_id', 'answer'], 'required'],
            [['test_id', 'player_id'], 'integer'],
            [['timestamp'], 'safe'],
            [['answer'], 'string', 'max' => 10],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['player_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => TacticsTest::className(), 'targetAttribute' => ['test_id' => 'id']],
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
            'answer' => 'Answer',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Member::className(), ['id' => 'player_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(TacticsPosition::className(), ['id' => 'position_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(TacticsTest::className(), ['id' => 'test_id']);
    }

    public function getClearAnswer() {
        return RockstarsApp::ClearTacticsAnswer($this->answer);
    }
}
