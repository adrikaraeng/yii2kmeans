<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReportCountCluster */

$this->title = Yii::t('app', 'Create Report Count Cluster');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Report Count Clusters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-count-cluster-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
