<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

$connection = \Yii::$app->db;
$this->title = Yii::t('app', 'Analisis Cluster');
// $this->params['breadcrumbs'][] = $this->title;
?>
<style>
  #tb-analisis-cluster {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }

  #tb-analisis-cluster td, #tb-analisis-cluster th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #tb-analisis-cluster tr:nth-child(even){background-color: #f2f2f2;}

  #tb-analisis-cluster tr:hover {background-color: #ddd;}

  #tb-analisis-cluster th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
  }
</style>


<?php

    Modal::begin([
        'header' => '<h4 style="color:#000;"><b>'.Yii::t('app',"Chart").'</b></h4>',
        // 'class' => 'user-lg',
        'id' => 'modal',
        // 'options' => ['style' => 'width: 1500px;'],
        'clientOptions' => ['backdrop' => false],
        'size' => 'user-lg',
    ]);

    echo "<div id='modalContent'></div>";

    Modal::end();
  ?>

<div class="centroid-index">
  <div id="next-centroid">
  </div>
  <div class="first-centroid">

    <?php Pjax::begin(['id' => 'pjx-analisis-cluster', 'enablePushState' => false]); ?>

    <?php
      $max_cluster = $connection->createCommand("SELECT max(iterasi) FROM list_centroid WHERE login='$user->id' AND kmeans_type='$title'")->queryScalar();
      $cek_new_cluster = $connection->createCommand("SELECT * FROM list_centroid WHERE login='$user->id' AND iterasi > '1' AND iterasi='$max_cluster' AND kmeans_type='$title'")->queryAll();
      $min_1_cluster = $max_cluster-1;
      $last = 1;
      $new = 1;
      if($cek_new_cluster){
        foreach($cek_new_cluster as $c => $clc){
          $cek_min_1_cluster = $connection->createCommand("SELECT * FROM list_centroid WHERE login='$user->id' AND count_symptom='$clc[count_symptom]' AND kmeans_type='$clc[kmeans_type]' AND iterasi='$min_1_cluster' AND cluster='$clc[cluster]'")->queryOne();
          if($cek_min_1_cluster){
            $new++;
          }
          $last++;
        }
        // echo $new.'--'.$last."<br>";
        if($last == $new){
          $completed = "100";
        ?>
          <div class="alert alert-success alert-dismissable">Iterasi <?=$max_cluster?> dan Iterasi <?=$min_1_cluster?> memiliki pola cluster yang sama, maka proses K-Means selesai.</div>
        <?php  
        }else{
          $completed = "0";
        ?>
          <div class="alert alert-warning alert-dismissable">Iterasi <?=$max_cluster?> dan Iterasi <?=$min_1_cluster?> memiliki pola cluster yang beda, lanjutkan algoritma...</div>
        <?php
        }
      }
    ?>
    <div id="max-cluster" style="display:none;"><?=$max_cluster?></div>
    <div id="cek-completed" style="display:none;"><?=$completed?></div>
    <div id="title-kmeans" style="display:none;"><?=$title?></div>

      <?php foreach($list_centroid as $c => $row):?>  
        <?php
          $cek_listcentroid = $connection->createCommand("SELECT * FROM list_centroid WHERE login='$row[login]' AND iterasi='$row[iterasi]' AND kmeans_type='$title'")->queryAll();
          
          $in_centroid = $connection->createCommand("SELECT a.*,b.*,c.*, b.kmeans_type AS b_kmeans_type, b.symptom AS b_symptom FROM list_centroid AS a
          INNER JOIN count_symptom AS b ON a.count_symptom=b.id
          INNER JOIN symptom AS c ON b.symptom=c.id
          WHERE a.login='$row[login]' AND a.iterasi='$row[iterasi]' AND a.kmeans_type='$title'
          ORDER BY a.id ASC")->queryAll();
        ?>
        <?php if($cek_listcentroid){?>
        <div style="padding-bottom:20px;">
          

        <?php
            // $iterasi_1 = 1;
            $iterasi_2 = $row['iterasi']+1;
            $int_centroid_ck = $connection->createCommand("SELECT * FROM centroid WHERE login='$row[login]' AND iterasi='$iterasi_2' AND kmeans_type='$title'")->queryAll();
          ?>
          <div style="font-weight:bold;text-align:center;">Centroid <?=$row['iterasi']?></div>
          <div class="">
            <table id="tb-analisis-cluster">
              <thead>
                <tr>
                  <th>Cluster</th>
                  <th>Regional 1</th>
                  <th>Regional 2</th>
                  <th>Regional 3</th>
                  <th>Regional 4</th>
                  <th>Regional 5</th>
                  <th>Regional 6</th>
                  <th>Regional 7</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($int_centroid_ck as $id => $d):?>
                <tr>
                  <td><?=$d['cluster']?></td>
                  <td><?=$d['reg1']?></td>
                  <td><?=$d['reg2']?></td>
                  <td><?=$d['reg3']?></td>
                  <td><?=$d['reg4']?></td>
                  <td><?=$d['reg5']?></td>
                  <td><?=$d['reg6']?></td>
                  <td><?=$d['reg7']?></td>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div>

          <div style="font-weight:bold;text-align:center;margin-top:10px;">Iterasi <?=$row['iterasi']?></div>
          <table id="tb-analisis-cluster">
            <thead>
              <tr>
                <th>No</th>
                <th style="width:300px;text-align:center;">Symptom</th>
                <th style="text-align:center;">Regional 1</th>
                <th style="text-align:center;">Regional 2</th>
                <th style="text-align:center;">Regional 3</th>
                <th style="text-align:center;">Regional 4</th>
                <th style="text-align:center;">Regional 5</th>
                <th style="text-align:center;">Regional 6</th>
                <th style="text-align:center;">Regional 7</th>
                <th style="text-align:center;">Label</th>
                <th style="text-align:center;">Cluster</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1;foreach($in_centroid as $i => $ic):?>
                <tr style="border:0.5px solid #000;">
                  <td><?=$no++?></td>
                  <td style="vertical-align:middle;"><?=$ic['symptom']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg1']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg2']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg3']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg4']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg5']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg6']?></td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['reg7']?></td>
                  <td style="text-align:center;vertical-align:middle;">
                    <?php
                      $date = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$ic[b_kmeans_type]' ORDER BY id DESC")->queryOne();
                      $data = $connection->createCommand("SELECT *, count(a.range_day_service) as c_service FROM cases AS a 
                        INNER JOIN symptom AS b ON b.id=a.symptomp
                        INNER JOIN count_symptom AS c ON c.symptom=b.id
                        INNER JOIN count_cluster AS d ON d.kmeans_type=c.kmeans_type
                        WHERE date(a.date_open)>='$date[start_date]' AND date(a.date_open)<='$date[end_date]' AND a.symptomp='$ic[b_symptom]' AND a.regional IS NOT NULL AND c.symptom='$ic[b_symptom]' ORDER BY c_service DESC")->queryOne();
                        
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
                    ?>
                    <?=$text?>
                  </td>
                  <td style="text-align:center;vertical-align:middle;"><?=$ic['cluster']?></td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          </div>
        <?php } ?> <!-- END IF Cek jika iterasi sudah ada -->
      <?php endforeach;?>
    <?php Pjax::end(); ?>

    <div style="font-weight:bold;text-align:center;">Centroid Awal</div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => false,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'cluster',
            // [
            //   'attribute' => 'id_count_symptom',
            //   'format' => 'raw',
            //   'value' => "symptom.symptom",
            // ],
            'reg1',
            'reg2',
            'reg3',
            'reg4',
            'reg5',
            'reg6',
            'reg7',
            //'iterasi',
            //'cluster',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
  </div>
  <?php Pjax::begin(['id' => 'pjx-analisis-cluster-btn', 'enablePushState' => false]); ?>
  <?php if($completed != '100'){?>
  <div id="btn-next-centroid">
    <?= Html::button(Yii::t('app', 'Next Centroid'), [
      // 'value' => Url::to('clear-cluster'),
      'class' => 'btn btn-primary',
      'id' => 'next-button',
      // 'style' => "display:none;",
      'onclick' => "
          var completed = $('#cek-completed').text();
          var title = $('#title-kmeans').text();
          $.ajax({
          url: 'ceknext-cluster',
          type: 'post',
          data: 'cluster='+completed+'&title='+title,
          success: function(result){
              console.log(result);
              if(completed == '100'){
                $('#btn-next-centroid').hide();
              }else{
                $('#btn-next-centroid').show();
              }
              $.pjax.reload({container:'#pjx-analisis-cluster', async:false});
              $.pjax.reload({container:'#pjx-analisis-cluster-btn', async:false});
          }
          });
      "
      ]);
    }
    ?>
    <?php if($completed == '100'){ ?>
      <div>
        <?= Html::button(Yii::t('app', 'View Chart'), [
          'value' => Url::to(['view-chart','t'=>$title]),
          'class' => 'btn btn-danger',
          'style' => "position:fixed; right: 50px; top:100px;",
          'id' => 'modalButton',
          'onclick' => "$('#modal').modal('show').find('#modalContent').load($(this).attr('value'));"])?>
      </div>
    <?php  } ?>
  </div>
  <?php Pjax::end(); ?>
</div>