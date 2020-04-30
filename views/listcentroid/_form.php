<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ListCentroid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="list-centroid-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'count_symptom')->textInput() ?>

    <?= $form->field($model, 'iterasi')->textInput() ?>

    <?= $form->field($model, 'cluster')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
