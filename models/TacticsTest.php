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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level_id' => 'Level ID',
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
}
