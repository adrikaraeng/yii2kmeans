<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CountSymptom */

$this->title = Yii::t('app', 'Create Count Symptom');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Count Symptoms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="count-symptom-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
