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
 * @property string $iframe
 * @property string $matchScore
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

    /**
     * @return string the iframe code or empty string if no lichess game id
     */
    public function getIframe() {
        if( $this->href  == '' || $this->href == null ) {
            //check if other matches of the same round already have results
            $others = Match::find()->where('tourney_id = :tourney_id AND round=:round AND href IS NOT NULL',
                ['tourney_id'=>$this->tourney_id, 'round'=>$this->round])->count();

            if( $others > 0 ){
                return "<div class='match-not-played other-matches-are-finished'>?</div>";
            }
            else {
                return "<div class='match-not-played no-matches-are-finished'></div>";
            }
        }
        else if( $this->href == 'cancelled' ) {
            return "<div class='match-not-played other-matches-are-finished'>×</div>";
        }

        return sprintf("<iframe width='%s' height='%s' frameborder=0 src='https://lichess.org/embed/%s?theme=auto&bg=auto'
    ></iframe>", '100%', '100%', $this->href);
    }

    /**
     * @return string
     */
    public function getMatchScore() {
        switch($this->result) {
            case 1: return '[1 - 0]';
            case 0.5: return '[0.5 - 0.5]';
            case -1: return '[0 - 1]';
            default: return '';
        }
    }
}
