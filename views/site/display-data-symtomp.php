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
</style>
<div class="detail-data-list">
  <div class="detail-title" style="font-weight:bold;font-size:1.2em;text-align:center;">Detail Data Cluster <?=$data['cluster']?></div>
    <table id="tb-analisis-cluster">
        <thead>
            <tr>
                <th>#</th>
                <th>Tiket</th>
                <th>Internet</th>
                <th>PSTN</th>
                <th>Speed</th>
                <th>Packet</th>
                <th>Regional</th>
                <th>Witel</th>
                <th>Datel</th>
                <th>Symptom Problem</th>
                <th>Amcrew</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1;foreach($cek_data as $cdata => $cd):?>
                <?php
                    if($cd['witel'] != '' OR $cd['witel'] != NULL):
                        $data = $connection->createCommand("SELECT * FROM witel WHERE id='$cd[witel]'")->queryOne();
                        $witel = $data['nama_witel'];
                    else:
                        $witel = "-";
                    endif;    
                ?>
                <tr>
                    <td><?=$no++?></td>
                    <td><?=$cd['trouble_ticket']?></td>
                    <td><?=$cd['internet_number']?></td>
                    <td><?=$cd['pstn']?></td>
                    <td><?=$cd['speed']?></td>
                    <td><?=$cd['packet']?></td>
                    <td><?=$cd['tregional']?></td>
                    <td><?=$witel?></td>
                    <td><?=$cd['datel']?></td>
                    <td><?=$cd['tsymptom']?></td>
                    <td><?=$cd['amcrew']?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>