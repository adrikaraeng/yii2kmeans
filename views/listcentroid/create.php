<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ListCentroid */

$this->title = Yii::t('app', 'Create List Centroid');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'List Centroids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list-centroid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
