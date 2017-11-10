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
 * @property string $startTime
 * @property DateTime $startDateTime
 * @property string $dayOfWeek
 * @property int $dateOfMonth
 * @property string $month
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
            [['name', 'start', 'time', 'master'], 'required'],
            [['start', 'time'], 'safe'],
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
}
