<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportData */

$this->title = Yii::t('app', 'Create Report Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Report Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
