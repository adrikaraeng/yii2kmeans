<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReportCountCluster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-count-cluster-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <?= $form->field($model, 'kmeans_type')->dropDownList([ 'symtomp' => 'Symtomp', 'teknisi' => 'Teknisi', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'end_date')->textInput() ?>

    <?= $form->field($model, 'jumlah_cluster')->textInput() ?>

    <?= $form->field($model, 'date_report')->textInput() ?>

    <?= $form->field($model, 'report_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
