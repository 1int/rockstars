<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "tourney_match".
 *
 * @property string $id
 * @property string $tourney_id
 * @property int $round
 * @property string $white
 * @property string $black
 * @property string $href
 * @property float $result
 *
 * @property Tourney $tourney
 */
class Match extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tourney_match';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tourney_id', 'round', 'white', 'black'], 'required'],
            [['tourney_id', 'round'], 'integer'],
            [['white', 'black', 'href'], 'string', 'max' => 255],
            [['tourney_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tourney::className(), 'targetAttribute' => ['tourney_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tourney_id' => 'Tourney ID',
            'round' => 'Round',
            'white' => 'White',
            'black' => 'Black',
            'href' => 'Href'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTourney()
    {
        return $this->hasOne(Tourney::className(), ['id' => 'tourney_id']);
    }
}
