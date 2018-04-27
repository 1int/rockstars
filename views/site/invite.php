<?php
    /**
     * Crafted by Pavel Lint 27/04/2018
     * Mail to: pavel@1int.org
     */


    /* @var $this yii\web\View */
    /* @var $form yii\bootstrap\ActiveForm */
    /* @var $model app\models\LoginForm */

    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title = 'Rockstars! invitation';
    $this->params['breadcrumbs'][] = 'Invite';
    ?>
    <div class="invite-form">
        <h1>You have been invited to join Rockstars!</h1>

        <p>Congratulations! Please fill in the form to continue.</p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'default',
            'fieldConfig' => ['template' => "{label}\n{input}\n{hint}"]

        ]); ?>

        <?= $form->errorSummary($model) ?>
        <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'username')->textInput(['enabled'=>false, 'value'=>Yii::$app->request->get('uname'), 'disabled'=>true]) ?>
        <?= $form->field($model, 'username')->hiddenInput(['value'=>Yii::$app->request->get('uname')]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'password_repeat')->passwordInput() ?>


        <div class="form-group">
            <?= Html::submitButton('Register', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
