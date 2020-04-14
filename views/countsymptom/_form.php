<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CountSymptom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="count-symptom-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <?= $form->field($model, 'symptom')->textInput() ?>

    <?= $form->field($model, 'kmeans_type')->dropDownList([ 'symtomp' => 'Symtomp', 'teknisi' => 'Teknisi', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'reg1')->textInput() ?>

    <?= $form->field($model, 'reg2')->textInput() ?>

    <?= $form->field($model, 'reg3')->textInput() ?>

    <?= $form->field($model, 'reg4')->textInput() ?>

    <?= $form->field($model, 'reg5')->textInput() ?>

    <?= $form->field($model, 'reg6')->textInput() ?>

    <?= $form->field($model, 'reg7')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
