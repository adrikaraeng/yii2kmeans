<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Cases');
// $this->params['breadcrumbs'][] = $this->title;
$connection=\Yii::$app->db;

$cek_date_close = $connection->createCommand("SELECT * FROM cases WHERE range_day_service IS NULL OR range_day_service = ''")->queryAll();
$del_null_date = $connection->createCommand("DELETE FROM cases WHERE date_open IS NULL OR date_closed IS NULL OR date_open='' OR date_closed=''")->execute();

foreach($cek_date_close as $c_dc => $row):
    $date_old = date_create(date('Y-m-d', strtotime($row['date_open'])));
    $date_new = date_create(date('Y-m-d', strtotime($row['date_closed'])));
    $diff = date_diff($date_old, $date_new);

    $update_row = $connection->createCommand("UPDATE cases SET range_day_service='$diff->d' WHERE range_day_service IS NULL AND id='$row[id]'")->execute();
endforeach;

?>
<div class="cases-index">
    <div style="border: 0.5px solid #000;padding:6px;">
        <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>
        <div style="float:left;">
            <?= $form->field($model,'file_case')->fileInput() ?>
        </div>
        <div class="form-group" style="clear:left;">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'date_open',
            'trouble_ticket',
            // 'symptomp',
            [
                'attribute' => 'symptomp',
                'format' => 'raw',
                'value' => function($model){
                    if($model->symptomp != NULL && $model->symptomp != ''):
                        return $model->symptomp0->symptom;
                    else:
                        return '';
                    endif;
                }
            ],
            [
                'attribute' => 'segment',
                'format' => 'raw',
                'value' => function($model){
                    if($model->segment != NULL && $model->segment != ''):
                        return $model->segment0->segment;
                    else:
                        return '';
                    endif;
                }
            ],
            // 'ncli',
            'internet_number',
            [
                'attribute' => 'regional',
                'contentOptions' => ['style'=>"text-align:center;"],
                'format' => 'html',
                'value' => function($model){
                    return "[ <b>".$model->regional."</b> ]<br>".$model->witel0->nama_witel;
                }
            ],
            //'datel',
            'speed',
            //'workzone_amcrew',
            'amcrew',
            //'packet',
            // 'status',
            'date_closed',
            //'login',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>