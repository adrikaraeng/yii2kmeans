<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Centroid */

$this->title = Yii::t('app', 'Create Centroid');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Centroids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="centroid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
