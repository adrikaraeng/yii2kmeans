<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CasesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date_open') ?>

    <?= $form->field($model, 'trouble_ticket') ?>

    <?= $form->field($model, 'symptomp') ?>

    <?= $form->field($model, 'ncli') ?>

    <?php // echo $form->field($model, 'internet_number') ?>

    <?php // echo $form->field($model, 'pstn') ?>

    <?php // echo $form->field($model, 'regional') ?>

    <?php // echo $form->field($model, 'witel') ?>

    <?php // echo $form->field($model, 'datel') ?>

    <?php // echo $form->field($model, 'speed') ?>

    <?php // echo $form->field($model, 'workzone_amcrew') ?>

    <?php // echo $form->field($model, 'amcrew') ?>

    <?php // echo $form->field($model, 'packet') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'date_closed') ?>

    <?php // echo $form->field($model, 'login') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
