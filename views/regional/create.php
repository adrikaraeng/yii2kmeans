<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Regional */

$this->title = Yii::t('app', 'Create Regional');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Regionals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regional-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
