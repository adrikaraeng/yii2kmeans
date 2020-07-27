<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReportData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date_open')->textInput() ?>

    <?= $form->field($model, 'trouble_ticket')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'symptomp')->textInput() ?>

    <?= $form->field($model, 'segment')->textInput() ?>

    <?= $form->field($model, 'ncli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'internet_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pstn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'regional')->textInput() ?>

    <?= $form->field($model, 'witel')->textInput() ?>

    <?= $form->field($model, 'datel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'speed')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'workzone_amcrew')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amcrew')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'packet')->dropDownList([ '1P' => '1P', '2P' => '2P', '3P' => '3P', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'CLOSED' => 'CLOSED', 'ON PROGRESS' => 'ON PROGRESS', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'date_closed')->textInput() ?>

    <?= $form->field($model, 'range_day_service')->textInput() ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
