<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

$this->title = Yii::t('app', 'K-means - '.$title);

if($title == 'Simtom'){
  $kmeans_type = 'symtomp';
}else{
  $kmeans_type = 'teknisi';
}

$connection = \Yii::$app->db;
$headTable = $connection->createCommand("SELECT * FROM cases GROUP BY regional ORDER BY regional ASC")->queryAll();
$count_headTable = $connection->createCommand("SELECT COUNT(*) FROM cases GROUP BY regional")->queryScalar();

$start_date = '2019-06-01';
$end_date = '2019-10-30';
$bodyTable = $connection->createCommand("SELECT 
a.*,b.*,
SUM(IF(a.regional='1' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_1,
SUM(IF(a.regional='2' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_2,
SUM(IF(a.regional='3' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_3,
SUM(IF(a.regional='4' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_4,
SUM(IF(a.regional='5' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_5,
SUM(IF(a.regional='6' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_6,
SUM(IF(a.regional='7' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_7
FROM cases AS a INNER JOIN symptom AS b ON b.id=a.symptomp GROUP BY a.symptomp ORDER BY a.symptomp ASC")->queryAll();
?>

<div id="form-cluster-awal">
  <?php $form = ActiveForm::begin([
    'id'=>'date-range',
    // 'options'=>['enctype'=>'multipart/form-data']
  ]); ?>

  <?= $form->field($model, 'jumlah_cluster')->dropDownList(['2' => '2', '3' => '3', '4' => '4', '5' => '5'], [
    'prompt' => 'Jumlah Cluster',
    'style'=>"width:200px;float:left;margin-right:5px;margin-bottom:5px;"
  ])->label(false) ?>

  <div style="float:left;width:200px;">
    <?= $form->field($model, 'start_date')->widget(
        DatePicker::className(), [
            //'value' => date('Y-m-d'),
            'options' => ['placeholder' => '*) Start Date'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true
            ]
        ]
    )->label(false)?>
  </div>
  <span style="float:left;font-weight:bold;padding-left:5px;padding-right:5px;">to</span>
  <div style="float:left;width:200px;margin-right:5px;">
    <?= $form->field($model, 'end_date')->widget(
        DatePicker::className(), [
            //'value' => date('Y-m-d'),
            'options' => ['placeholder' => '*) End Date'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true
            ]
        ]
    )->label(false)?>
  </div>
  <div class="form-group" style="float:left;">
      <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
      <?= Html::button(Yii::t('app', 'Clear'), [
          // 'value' => Url::to('clear-cluster'),
          'class' => 'btn btn-danger',
          'id' => 'modalButton',
          'onclick' => "
            if(confirm('Clear Cluster ?')){
              $.ajax({
                url:'".Url::to(['clear-cluster','title'=>$kmeans_type])."',
                type: 'post',
                success: function(){
                  $.pjax.reload({container:'#pjx-cluster-grid'});
                }
              });
            }
          "
          ]);
      ?>
  </div>
  <?php ActiveForm::end(); ?>
</div>

<?php
  $cek_cluster = $connection->createCommand("SELECT * FROM count_cluster WHERE `login`='$user->id' AND kmeans_type='$kmeans_type' ORDER BY id DESC")->queryOne();
?>
<div class="case-sympthomp-index" style="margin-top:40px;">
  <?php Pjax::begin(['id' => 'pjx-cluster-grid', 'enablePushState' => false]); ?>
  <?php if($cek_cluster){ ?>
    <div style="position:fixed;top:70px;right:10px;padding:2px;background-color:#fff;border-radius:3px;">
      <span style="text-align:right;">Select <b><?=$cek_cluster['jumlah_cluster']?></b> Centroid from <b><?=date('d-m-Y',strtotime($cek_cluster['start_date']))?></b> to <b><?=date('d-m-Y',strtotime($cek_cluster['end_date']))?></b></span>
    </div>

    <span id="id-total-first-centroid" style="display:none;"><?=$cek_cluster['jumlah_cluster']?></span><!-- Centroid Awal -->
  <?php } ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'sympthomp-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
              'class' => 'yii\grid\SerialColumn',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'symptom',
              'contentOptions' => ['style'=>"max-width:400px;vertical-align:middle;"],
              'format' => 'raw',
              'value' => function($model){
                return $model->symptom0->symptom;
              }
            ],
            [
              'attribute' => 'reg1',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg2',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg3',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg4',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg5',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg6',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg7',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'class' => 'yii\grid\CheckboxColumn',
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
<div id="button-check-cluster">
  <?= Html::button(Yii::t('app', 'Next Centroid'), [
      // 'value' => Url::to('clear-cluster'),
      'class' => 'btn btn-primary',
      'id' => 'next-button',
      'style' => "display:none;",
      'onclick' => "
        var keys = $('#sympthomp-grid').yiiGridView('getSelectedRows');
        $.ajax({
          url: '".Url::to(['cek-iterasi','title'=>$kmeans_type])."',
          type: 'post',
          data: {keylist:keys},
          success: function(result){
            console.log(result);
          }
        });
      "
      ]);
  ?>
</div>
<?php
$script = <<< JS

  var submit = $('#next-button').hide();
  $('input[name="selection[]"]').change(function() {
    var jumlahCluster = $('#id-total-first-centroid').text();
    var numberOfChecked = $('input:checkbox:checked').length;
    if(jumlahCluster == numberOfChecked){
      $('#next-button').show();
    }else{
      $('#next-button').hide();
    }
  });

JS;
$this->registerJS($script);
?>