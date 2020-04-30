<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Centroid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="centroid-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <?= $form->field($model, 'reg1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg4')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg5')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg6')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reg7')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'iterasi')->textInput() ?>

    <?= $form->field($model, 'cluster')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
