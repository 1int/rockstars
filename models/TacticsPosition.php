<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
use RockstarsApp;

/**
 * This is the model class for table "tactics_positions".
 *
 * @property string $id
 * @property string $test_id
 * @property int $points
 * @property int $dotdotdot
 * @property string $answer
 * @property string $prettyAnswer
 *
 * @property TacticsTest $test
 */
class TacticsPosition extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tactics_positions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'points', 'dotdotdot', 'answer'], 'required'],
            [['test_id', 'points', 'dotdotdot'], 'integer'],
            [['answer'], 'string', 'max' => 10],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => TacticsTest::className(), 'targetAttribute' => ['test_id' => 'id']],
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
     * @param $uid
     * @return TacticsAnswer|null
     */
    public function answerForPlayer($uid) {
        return TacticsAnswer::find()->where('player_id=:uid AND position_id=:id', ['uid'=>$uid, 'id'=>$this->id])->one();
    }

    /**
     * @param TacticsAnswer|null $answer
     * @return bool
     */
    public function isAnswerCorrect($answer) {
        if( !$answer ) {
            return false;
        }
        return RockstarsApp::ClearTacticsAnswer($answer->answer) == RockstarsApp::ClearTacticsAnswer($this->answer);
    }

    public function getPrettyAnswer() {
        return strlen($this->answer) == 2 ? strtolower($this->answer) :
                ucfirst(strtolower($this->answer));
    }
}
