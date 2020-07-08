<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Segment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="segment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'segment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
