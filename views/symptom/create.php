<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Symptom */

$this->title = Yii::t('app', 'Create Symptom');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Symptoms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="symptom-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
