<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

$connection = \Yii::$app->db;
$this->title = Yii::t('app', 'Detail data');
?>
<style>
  #tb-analisis-cluster {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    font-size: 0.84em;
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
  .input-container{
    display: flex;
    width: 100%;
    margin-bottom: 15px;
  }
  .icon {
    padding: 10px;
    background: dodgerblue;
    color: white;
    min-width: 50px;
    text-align: center;
  }
  .input-field:focus {
    border: 2px solid dodgerblue;
  }
</style>
<div class="detail-data-list">
  <div class="detail-title" style="font-weight:bold;font-size:1.2em;text-align:center;">Detail Data Cluster <?=$data['cluster']?></div>
    <div class="input-container" style="text-align:center;float:left;max-width:400px;width:100%;">
      <i class="fa fa-search icon"></i>
      <input type="text" name="search" id="search" class="input-field" style="width:300px;" placeholder="Search data">
    </div>
    <div style="clear:right;text-align:right;">
      <?= Html::a("<span class='btn btn-primary'>Show more analytics</span>", Url::toRoute(['site/show-more',
          'id' => $data['id'],
          'type' => $type
        ]), [
          'title' => Yii::t('app', 'Show more analytics'),
        ]);
      ?>
    </div>
    <div class="table-reponsive">
      <table id="tb-analisis-cluster">
        <tr>
            <th>#</th>
            <th style="width:110px;">Tiket</th>
            <th>Internet</th>
            <th>Speed</th>
            <th>Packet</th>
            <th>Regional</th>
            <th>Witel</th>
            <th>Datel</th>
            <th>Jlh</th>
            <th style="width:250px;">Symptom Problem [Segment]</th>
            <th>Amcrew</th>
            <th>Status Service</th>
        </tr>
        <?php $nom=1;foreach($cek_data as $cdata => $cd):?>
            <?php
                if($cd['witel'] != '' OR $cd['witel'] != NULL):
                    $data = $connection->createCommand("SELECT * FROM witel WHERE id='$cd[witel]'")->queryOne();
                    $witel = $data['nama_witel'];
                else:
                    $witel = "-";
                endif;    

                // if($cd['amcrew'] != NULL || $cd['amcrew'] != ''):
                  $c_amcrew = $connection->createCommand("SELECT COUNT(*) FROM cases AS a
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$cd[kmeans_type]'
                  INNER JOIN list_centroid AS d ON d.count_symptom=c.id AND d.iterasi='$cd[iterasi]'
                  WHERE date(a.date_open)>='$start_date' AND date(a.date_open) <= '$end_date' AND a.amcrew='$cd[amcrew]' AND d.cluster='$cd[cluster]' AND a.symptomp='$cd[symptomp]' AND d.kmeans_type='$cd[kmeans_type]'")->queryScalar();

                  // $data_simptomp = $connection->createCommand("SELECT *, b.symptom AS nama_symtomp FROM cases AS a
                  // INNER JOIN symptom AS b ON b.id=a.symptomp
                  // INNER JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$cd[kmeans_type]'
                  // INNER JOIN list_centroid AS d ON d.iterasi='$cd[iterasi]'
                  // WHERE date(a.date_open)>='$start_date' AND date(a.date_open) <= '$end_date' AND a.amcrew='$cd[amcrew]' AND d.cluster='$cd[cluster]' AND d.kmeans_type='$cd[kmeans_type]' AND a.symptomp=c.symptom
                  // GROUP BY a.amcrew, b.id")->queryAll();
                  
                  $data_simptomp = $connection->createCommand("SELECT *, b.symptom AS nama_symtomp, a.regional AS d_regional, e.segment AS e_segment, f.nama_witel AS f_witel FROM cases AS a
                  LEFT JOIN witel AS f ON f.id=a.witel
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  LEFT JOIN segment AS e ON e.id=a.segment
                  INNER JOIN count_symptom AS c ON c.symptom=b.id
                  INNER JOIN list_centroid AS d ON d.count_symptom=c.id AND d.iterasi='$cd[iterasi]'
                  WHERE date(a.date_open)>='$start_date' AND date(a.date_open) <= '$end_date' AND a.amcrew='$cd[amcrew]' AND d.cluster='$cd[cluster]' AND c.symptom='$cd[b_simptom]'")->queryAll();
                // else:
                //   $c_amcrew = '';
                // endif;

            ?>
            <tr id="data-search">
                <td><?=$nom++?></td>
                <td>
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                          <div><?="<b>".$s['trouble_ticket']."</b>"?></div>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                        <?php if($s['internet_number'] == NULL || $s['internet_number'] == ''):?>
                          <div>-</div>
                        <?php else:?>
                          <div><?=$s['internet_number']?></div>
                        <?php endif;?>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                  <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                      <?php if($s['speed'] == NULL || $s['speed'] == ''):?>
                        <div>-</div>
                      <?php else:?>
                          <div><?=$s['speed']?></div>
                      <?php endif;?>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                  <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                      <?php if($s['packet'] == NULL || $s['packet'] == ''):?>
                        <div><?="<b>-</b>"?></div>
                      <?php else:?>
                          <div><?="<b>".$s['packet']."</b>"?></div>
                      <?php endif;?>
                    <?php
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                        <?php if($s['d_regional'] == NULL || $s['d_regional'] == ''):?>
                          <div><?="<b>-</b>"?></div>
                        <?php else:?>
                          <div><?="<b>".$s['d_regional']."</b>"?></div>
                        <?php endif;?>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                    <?php
                      $no = 1;
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                        <?php if($s['f_witel'] == NULL || $s['f_witel'] == ''):?>
                          <div><?=$no.". <b>-</b>"?></div>
                        <?php else:?>
                          <div><?=$no.". <b>".$s['f_witel']."</b>"?></div>
                        <?php endif;?>
                    <?php 
                      $no++;
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                    <?php
                      $no = 1;
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                      <?php if($s['datel'] == NULL || $s['datel'] == ''):?>
                        <div><?=$no.". <b>-</b>"?></div>
                      <?php else:?>
                          <div><?=$no.". <b>".$s['datel']."</b>"?></div>
                      <?php endif;?>
                    <?php 
                      $no++;
                      endforeach;
                      }
                    ?>
                </td>
                <td><?=$c_amcrew?></td>
                <td>
                  <ul type="1">
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                          <li><?=$s['nama_symtomp']." <b>[".$s['e_segment']."]</b>"?></li>
                    <?php 
                      endforeach;
                      }
                    ?>
                  </ul>
                </td>
                <td>
                  <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                      <?php if($s['amcrew'] == NULL || $s['amcrew'] == ''):?>
                        <div>-</div>
                      <?php else:?>
                          <div><?=$s['amcrew']?></div>
                      <?php endif;?>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
                <td>
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                        <?php if($s['range_day_service'] == NULL || $s['range_day_service'] == ''):?>
                          <div>-</div>
                        <?php elseif($s['range_day_service'] == '0'):?>
                          <div><span class="label label-success">Mudah</span></div>
                        <?php elseif($s['range_day_service'] == '1' || $s['range_day_service'] == '2'):?>
                          <div><span class="label label-warning">Normal</span></div>
                        <?php elseif($s['range_day_service'] > '2'):?>
                          <div><span class="label label-danger">Sulit</span></div>
                        <?php endif;?>
                    <?php 
                      endforeach;
                      }
                    ?>
                </td>
            </tr>
        <?php endforeach;?>
      </table>
    </div>
  </div>
</div>
<?php
$this->registerJs("
  $('#search').keyup(function(){
    search_table($(this).val());
  });

  function search_table(value){
    $('#tb-analisis-cluster tr#data-search').each(function(){
      var found = 'false';
      $(this).each(function(){
        if($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0)
        {
          found = 'true';
        }
        if(found == 'true'){
          $(this).show();
        }else{
          $(this).hide();
        }
      });
    });
  }
");
?>