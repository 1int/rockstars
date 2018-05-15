<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\HttpException;


class InviteForm extends Model
{

    public $name;
    public $username;
    public $password;
    public $password_repeat;
    public $rememberMe;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['name', 'safe'],
            [['password', 'password_repeat', 'username'], 'required'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true) {

        if(!parent::validate($attributeNames, $clearErrors)) {
            return false;
        }

        /** @var Invite $invite */
        $invite = Invite::find()->where('username=:u and used=0', ['u'=>$this->username])->one();
        if( !$invite ) {
            $this->addError('username', 'No invite found for ' . $this->username);
            return false;
        }
        $invite->used = 1;
        $invite->save();
        return true;
    }

    /**
     * @throws HttpException
     * @return bool
     */
    public function registerUser()
    {
        if ($this->validate()) {
            $oldMember = Member::findByUsername($this->username);
            if( $oldMember ) {
                $newMember = $oldMember;
                $pass = trim($this->password);
                if( strlen($pass) < 3 ) {
                    throw new HttpException(400, "That's a shitty password");
                }
                $newMember->password = Member::hashPassword($pass);
            }
            else {
                $newMember = new Member();
                $newMember->username = $this->username;
                $newMember->name = $this->name;
                $newMember->password = Member::hashPassword($this->password);
                $newMember->bio = '';
                $newMember->role = 0;
                $newMember->show_on_graph = 0;
                $newMember->show_in_pairings = 0;
                $newMember->show_on_homepage = 0;
            }

            if (!$newMember->save()) {
                throw new HttpException(500, 'Failed to create user: ' . print_r($newMember->getErrors(), true));
            }
            return Yii::$app->user->login($newMember, 3600*24*30);
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_repeat' => 'Repeat Password',
        ];
    }
}
