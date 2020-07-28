<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReportCountClusterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Report');
?>
<p>Welcome back <b><?=$user->nama_lengkap?></b></p><p>status : <b><?=$user->level?></b></p>
<div class="report-count-cluster-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // [
            //     'attribute' => 'login',
            //     'value' => function($model){
            //         return $model->login0->username;
            //     }
            // ],
            // 'kmeans_type',
            'start_date',
            'end_date',
            'jumlah_cluster',
            'date_report',
            // 'report_by',
            [
                'attribute' => 'report_by',
                'value' => function($model){
                    return $model->reportBy->nama_lengkap;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'options' => ['style'=>'width:5%;clear:both;'],
                'template' => '{view}&nbsp&nbsp',
                'buttons' => [
                    'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-book"></span>', $url, [
                                'title' => Yii::t('app', 'View Report'),
                                'id' => 'view-report',
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view'):
                        return Url::toRoute(['view-report', 'id' => $model->id]);
                    endif;
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
