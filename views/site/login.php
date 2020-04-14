<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login first';
?>
<div class="site-login">
  <div id="login-center">
    <div class="title-login">SCINS</div>
    <div class="l-title-login">(Sistem Clusterring IndiHome with K-Means)</div>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
      //   'layout' => 'horizontal',
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true,'placeholder'=>'Username'])->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder'=>'Password'])->label(false) ?>

        <div class="form-group" style="text-align:center;">
          <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>
