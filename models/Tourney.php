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
 * @property string $team1
 * @property string $team2
 * @property string $date
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
            [['team1', 'team2'], 'string', 'max' => 1028],
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
}
