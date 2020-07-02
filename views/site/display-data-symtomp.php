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
    <div class="input-container" style="text-align:center;">
      <i class="fa fa-search icon"></i>
      <input type="text" name="search" id="search" class="input-field" placeholder="Search data" style="max-width:400px;width:100%;">
    </div>

    <div class="table-reponsive">
      <table id="tb-analisis-cluster">
        <tr>
            <th>#</th>
            <th>Tiket</th>
            <th>Internet</th>
            <th>Speed</th>
            <th>Packet</th>
            <th>Regional</th>
            <th>Witel</th>
            <th>Datel</th>
            <th>Symptom Problem</th>
            <th>Amcrew</th>
        </tr>
        <?php $no=1;foreach($cek_data as $cdata => $cd):?>
            <?php
                if($cd['witel'] != '' OR $cd['witel'] != NULL):
                    $data = $connection->createCommand("SELECT * FROM witel WHERE id='$cd[witel]'")->queryOne();
                    $witel = $data['nama_witel'];
                else:
                    $witel = "-";
                endif;    

                if($cd['amcrew'] != NULL || $cd['amcrew'] != ''):
                  $c_amcrew = $connection->createCommand("SELECT COUNT(a.amcrew) FROM cases AS a
                  LEFT JOIN symptom AS b ON b.id=a.symptomp
                  LEFT JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$cd[kmeans_type]'
                  LEFT JOIN list_centroid AS d ON d.count_symptom=c.id AND d.iterasi='$cd[iterasi]'
                  WHERE date(a.date_open)>='$start_date' AND date(a.date_open) <= '$end_date' AND a.amcrew='$cd[amcrew]' AND d.cluster='$cd[cluster]' AND d.kmeans_type='$cd[kmeans_type]' AND a.amcrew <> '' GROUP BY b.id")->queryScalar();

                  $data_simptomp = $connection->createCommand("SELECT *, b.symptom AS nama_symtomp FROM cases AS a
                  INNER JOIN symptom AS b ON b.id=a.symptomp
                  INNER JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$cd[kmeans_type]'
                  INNER JOIN list_centroid AS d ON d.iterasi='$cd[iterasi]'
                  WHERE a.amcrew <> '' AND date(a.date_open)>='$start_date' AND date(a.date_open) <= '$end_date' AND a.amcrew='$cd[amcrew]' AND d.cluster='$cd[cluster]' AND d.kmeans_type='$cd[kmeans_type]'
                  GROUP BY b.id")->queryAll();
                else:
                  $c_amcrew = '';
                endif;

            ?>
            <tr id="data-search">
                <td><?=$no++?></td>
                <td><?=$cd['trouble_ticket']?></td>
                <td><?=$cd['internet_number']?></td>
                <td><?=$cd['speed']?></td>
                <td><?=$cd['packet']?></td>
                <td><?=$cd['tregional']?></td>
                <td><?=$witel?></td>
                <td><?=$cd['datel']?></td>
                <td>
                  <ul type="1">
                    <?php
                      if($data_simptomp){
                        foreach($data_simptomp as $ds => $s):
                    ?>
                          <li><?=$s['nama_symtomp']?></li>
                    <?php 
                      endforeach;
                      }
                    ?>
                  </ul>
                </td>
                <td><?=$cd['amcrew']?></td>
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