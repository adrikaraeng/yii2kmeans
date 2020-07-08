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
  <div class="detail-title" style="font-weight:bold;font-size:1.2em;text-align:center;">Data Cluster <?=$data['cluster']?></div>
    <table id="tb-analisis-cluster">
        <thead>
            <tr>
                <th>#</th>
                <th>Symptom</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1;foreach($cek_data as $cdata => $cd):?>
                <tr>
                    <td><?=$no++?></td>
                    <td><?=$cd['b_simptom']?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>