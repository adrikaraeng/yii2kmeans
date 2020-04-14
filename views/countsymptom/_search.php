<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CountsymptomSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="count-symptom-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'login') ?>

    <?= $form->field($model, 'symptom') ?>

    <?= $form->field($model, 'kmeans_type') ?>

    <?= $form->field($model, 'reg1') ?>

    <?php // echo $form->field($model, 'reg2') ?>

    <?php // echo $form->field($model, 'reg3') ?>

    <?php // echo $form->field($model, 'reg4') ?>

    <?php // echo $form->field($model, 'reg5') ?>

    <?php // echo $form->field($model, 'reg6') ?>

    <?php // echo $form->field($model, 'reg7') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
