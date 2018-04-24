<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tactics_positions".
 *
 * @property string $id
 * @property string $test_id
 * @property int $points
 * @property int $dotdotdot
 * @property string $answer
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_id' => 'Test ID',
            'points' => 'Points',
            'dotdotdot' => 'Dotdotdot',
            'answer' => 'Answer',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(TacticsTest::className(), ['id' => 'test_id']);
    }
}
