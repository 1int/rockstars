<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "invite".
 *
 * @property string $id
 * @property int $invited_by
 * @property bool $used
 * @property string $username
 * @property string $expires
 * @property string $timestamp
 * @property Member invitedBy
 */
class Invite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['expires', 'timestamp'], 'safe'],
            [['username'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'expires' => 'Expires',
            'timestamp' => 'Timestamp',
        ];
    }

    public function getIvnitedBy()
    {
        return $this->hasOne(Member::className(), ['id' => 'invited_by']);
    }
}
