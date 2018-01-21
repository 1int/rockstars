<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
use \DateTime;
use \DateInterval;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property string $name
 * @property string $start
 * @property string $time
 * @property int $repeatsInDays
 * @property string $image
 * @property DateTime $startDate
 * @property DateTime $date
 * @property string $startTime
 * @property DateTime $startDateTime
 * @property string $dayOfWeek
 * @property int $dateOfMonth
 * @property string $month
 * @property bool $active
 * @property int $skipNext
 * @property string $pairings
 * @property bool $hasPairings
 *
 * @property Member $master
 */
class Event extends ActiveRecord
{
    /**
     * @var DateTime $date
     */
    private $date;

    /**
     * @var string $pairs
     */
    private $pairs = '';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start', 'time'], 'required'],
            [['start', 'time', 'has_pairings'], 'safe'],
            [['repeatsInDays'], 'integer'],
            [['name', 'image'], 'string', 'max' => 128],
            [['master'], 'exist', 'skipOnError' => true, 'targetClass' => Member::className(), 'targetAttribute' => ['master_id' => 'id']],
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
            'start' => 'Start',
            'time' => 'Time',
            'repeatsInDays' => 'Repeats In Days',
            'master' => 'Master',
            'image' => 'Image',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaster()
    {
        return $this->hasOne(Member::className(), ['id' => 'master_id']);
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        $ret = new DateTime();
        $timestamp = strtotime($this->start);
        $ret->setTimestamp($timestamp);
        return $ret;
    }

    /**
     * @return DateTime
     */
    public function getStartDateTime() {
        $ret = new DateTime();
        $timestamp = strtotime($this->start . ' ' . $this->time);
        $ret->setTimestamp($timestamp);
        return $ret;
    }

    /**
     * @return string
     */
    public function getDayOfWeek() {
        return $this->date->format('l');
    }

    /**
     * @return int
     */
    public function getDateOfMonth() {
        return intval($this->date->format('d'));
    }

    /**
     * @return string
     */
    public function getMonth() {
        return $this->date->format('M');
    }

    /**
     * @return string
     */
    public function getStartTime() {
        return $this->date->format('H:i');
    }

    public function afterFind() {
        parent::afterFind();
        $this->date = $this->getStartDateTime();
    }

    public function setTimestamp($t) {
        $this->date = new DateTime();
        $this->date->setTimestamp($t);
    }

    public function setDateToNextTime() {
        $this->date->add(new DateInterval(sprintf("P%dD", $this->repeatsInDays)));
    }

    /**
     * @return int
     */
    public function getTimestamp() {
        return $this->date->getTimestamp();
    }

    /**
     * @return DateTime
     */
    public function getDate() {
       return $this->date;
    }

    /**
     * @param Event $otherEvent
     */
    public function assignDate($otherEvent) {
        $this->date = $otherEvent->date;
    }

    protected function queryPairings() {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT pairs FROM pairings WHERE event_id = :event_id AND date=:date",
            ['event_id'=>$this->id, 'date'=>$this->date->format('Y-m-d')]);

        $this->pairs = $command->queryAll();
        if($this->pairs) {
            $this->pairs = $this->pairs[0]['pairs'];
        }
    }

    protected function createPairings() {
        $members = Member::find()->where('show_in_pairings=1')->all();
        $members = array_map(function($e){return $e->id;}, $members);
        shuffle($members);
        if( count($members) % 2 == 0 ) {
            $ret = implode(',', $members);
        }
        else {
            $ret = implode(',', $members) . ',0';
        }

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand(sprintf("INSERT INTO pairings (event_id, date, pairs) VALUES (%d, '%s', '%s')",
                                                            $this->id, $this->date->format('Y-m-d'), $ret));
        $command->execute();
        $this->pairs = $ret;
    }

    public function getPairings() {
        if( !$this->pairs ) {
            $this->queryPairings();
            if( !$this->pairs ) {
                $this->createPairings();
            }
        }
        if( !$this->pairs ) {
            return '';
        }

        $ids = explode(',', $this->pairs);
        $i = 0;
        $ret = '';
        foreach($ids as $id) {
            if( $id == 0 ) {
                $ret .= '???';
            }
            else {
                $ret .= Member::findOne($id)->usernameWithLink;
            }

            if($i < count($ids) - 1) {
                if ($i % 2 == 1) {
                    $ret .= ', ';
                }
                else {
                    $ret .= '-';
                }
            }
            $i++;
        }


        return $ret;
    }

    public function getHasPairings() {
        return $this->has_pairings;
    }
}
