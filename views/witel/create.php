<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Witel */

$this->title = Yii::t('app', 'Create Witel');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Witels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="witel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
