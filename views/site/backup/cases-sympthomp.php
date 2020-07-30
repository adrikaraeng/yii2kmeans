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
  <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable" id="report-success" style="position:fixed;right:20px;display:none;">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
  <?php endif; ?>
  <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissable" id="report-error" style="position:fixed;right:20px;display:none;">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
  <?php endif; ?>

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
        // 'filterModel' => $searchModel,
        'filterModel' => null,
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
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='1' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg1,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 1",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"]
            ],
            [
              'attribute' => 'reg2',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='2' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = "<div>".$row['trouble_ticket']."-".$text."</div>";
                endforeach;

                $tag = Html::tag('span', $model->reg2,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 2",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg3',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='3' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg3,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 3",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg4',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='4' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg4,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 4",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg5',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='5' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg5,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 5",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg6',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='6' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg6,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 6",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'reg7',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT * FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional='7' AND c.symptom='$model->symptom'")->queryAll();
                $content = [];
                foreach($data as $d => $row):
                  if($row['range_day_service'] == '0'):
                    $text = "<span><span class='label label-success'>Mudah</span></span>";
                  elseif($row['range_day_service'] == '1' || $row['range_day_service'] == '2'):
                    $text = "<span><span class='label label-warning'>Normal</span></span>";
                  elseif($row['range_day_service'] > '2'):
                    $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  else:
                    $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  endif;
                  $content[] = $row['trouble_ticket']."-".$text."<br>";
                endforeach;

                $tag = Html::tag('span', $model->reg7,[
                  'tabindex' => "0",
                  'id' => 'case-popover',
                  'options' => [
                    'style' => "width:500px;"
                  ],
                  'role' => "button",
                  'data-trigger' => "focus", 
                  'data-html' => "true",
                  'data-container' => "body",
                  'data-toggle' => 'popover',
                  'title' => "List Regional 7",
                  'data-placement' => 'top',
                  'data-content' => $this->render('@app/views/site/data-case',[
                    'content' => $content
                  ]),
                ]);
                return $tag;
              },
              'contentOptions' => ['style'=>"vertical-align:middle;text-align:center;"],
            ],
            [
              'attribute' => 'dominan',
              'format' => 'raw',
              'value' => function($model) use ($connection){
                $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$model->kmeans_type' ORDER BY id DESC")->queryOne();
                $data = $connection->createCommand("SELECT *, count(a.range_day_service) as c_service FROM cases AS a 
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                  WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$model->symptom' AND a.regional IS NOT NULL AND c.symptom='$model->symptom' ORDER BY c_service DESC")->queryOne();
                  
                  // if($data['range_day_service'] == '0'):
                  //   $text = "<span><span class='label label-success'>Mudah</span></span>";
                  // elseif($data['range_day_service'] == '1' || $data['range_day_service'] == '2'):
                  //   $text = "<span><span class='label label-warning'>Normal</span></span>";
                  // elseif($data['range_day_service'] > '2'):
                  //   $text = "<span><span class='label label-danger'>Sulit</span></span>";
                  // else:
                  //   $text = "<span><span class='label label-danger'>On Progress</span></span>";
                  // endif;
                  
                  if($data['range_day_service'] == '0'):
                    $text = "Mudah";
                  elseif($data['range_day_service'] == '1' || $data['range_day_service'] == '2'):
                    $text = "Normal";
                  elseif($data['range_day_service'] > '2'):
                    $text = "Sulit";
                  else:
                    $text = "On Progress";
                  endif;

                return $text;
              },
              // 'filter' => ['Mudah' => 'Mudah', 'Normal' => 'Normal', 'Sulit' => 'Sulit'],
              'filter' => false,
              'headerOptions' => ['id' => 'dominan-case','style'=>"width:100px;text-align:center;"],
              'contentOptions' => ['id' => 'dominan-case-child','style'=>"width:100px;text-align:center;"],
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

  $("th[name='CountsymptomSearch[dominan]'").keyup(function () {
      var value = this.value.toLowerCase().trim();

      $("table tr").each(function (index) {
          if (!index) return;
          $(this).find("td").each(function () {
              var id = $(this).text().toLowerCase().trim();
              var not_found = (id.indexOf(value) == -1);
              $(this).closest('tr').toggle(!not_found);
              console.log('not found');
              return not_found;
          });
      });
  });

  $('#case-popover').popover({
    selector:'[data-toggle=popover]',
    trigger: 'hover',
    html: true,
    content: function () {
        return $(this).parents('.row').first().find('.metaContainer').html();
    }
  });

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