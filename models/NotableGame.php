<?php

namespace app\models;

use Yii;
use  \yii\db\ActiveRecord;

/**
 * This is the model class for table "notable_games".
 *
 * @property string $id
 * @property int $player_id
 * @property string $lichess_id
 * @property string $created
 *
 * @property Member $player
 */
class NotableGame extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notable_games';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'lichess_id'], 'required'],
            [['player_id'], 'integer'],
            [['created'], 'safe'],
            [['lichess_id'], 'string', 'max' => 20],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'lichess_id' => 'Lichess ID',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(Member::className(), ['id' => 'player_id']);
    }


    public function getIframe() {
        if( $this->lichess_id  == '' || $this->lichess_id == null ) {
            return "<div class='match-not-played other-matches-are-finished'>×</div>";
        }

        return sprintf("<iframe width='%s' height='%s' frameborder=0 src='https://lichess.org/embed/%s?theme=auto&bg=auto'
    ></iframe>", '345', '243', $this->lichess_id);
    }
}
