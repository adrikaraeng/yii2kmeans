<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->registerCssFile('@web/css/morris.css');
$this->registerJsFile('@web/morrisjs/morris.js');
$connection = \Yii::$app->db;

if($t == 'symtomp'){
  $title = 'Simptom';
}else{
  $title = 'Teknisi';
}
$max_iterasi = $connection->createCommand("SELECT max(iterasi) FROM list_centroid WHERE kmeans_type='$t'")->queryScalar();
$list_count_cluster = $connection->createCommand("SELECT * FROM list_centroid WHERE kmeans_type='$t' AND iterasi='$max_iterasi' GROUP BY cluster ORDER BY cluster ASC")->queryAll();

$cluster2 = [];
$sum_value = [];
$c2 = 0;

$cluster = [];
$cekid = [];
$cekid2 = [];
$count_cluster = [];
$c1 = 0;
foreach($list_count_cluster as $l => $lc):
  $cls = $connection->createCommand("SELECT COUNT(*) FROM list_centroid WHERE login='$user->id' AND kmeans_type='$t' AND iterasi='$max_iterasi' AND cluster='$lc[cluster]'")->queryScalar();
  $cluster[] = "Cluster ".$lc['cluster'];
  $cekid[] = $lc['id'];
  $count_cluster[] = $cls;
  $c1++;
endforeach;

foreach($list_count_cluster as $v => $vl):
  // SUM(IF(sentiment='Positive' AND (review_last_update_date_and_time BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS c_sent_positif
  $list_value_cluster = $connection->createCommand("SELECT a.*,b.*,
  SUM(IF(a.iterasi='$max_iterasi' AND a.cluster='$vl[cluster]' AND b.id=a.count_symptom, (b.reg1 + b.reg2 + b.reg3 + b.reg4 + b.reg5 + b.reg6 + b.reg7),0)) AS jlh_reg
  FROM list_centroid AS a
  INNER JOIN count_symptom AS b ON b.id=a.count_symptom
  WHERE b.id=a.count_symptom AND a.iterasi='$max_iterasi' AND a.cluster='$vl[cluster]'")->queryOne();
  $cluster2[] = "Cluster ".$vl['cluster'];
  $cekid2[] = $vl['id'];
  $sum_value[] = $list_value_cluster['jlh_reg'];
  $c2++;
endforeach;
?>
<div class="index-view-chart">

	<div id="id_chart_cluster" style="position:relative;float:left;">
    <div style="text-align:center;font-weight:bold;">Count By Cluster</div>
    <div id="count_cluster" style="height: 300px;background-color:transparent;"></div>
  </div>
	<div id="id_chart_cluster" style="position:relative;float:left;">
    <div style="text-align:center;font-weight:bold;">Total <?=$title?> By Cluster</div>
    <div id="sum_cluster" style="height: 300px;background-color:transparent;"></div>
  </div>

  <div style="clear:left;"></div>
</div>
<script type="text/javascript">
	var $katColors = ['#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#035bc7', '#7901c9', '#f0910f', '#9c6372', '#656049', '#e91b53', '#d6e41f', '#60c1f0', '#f4d25b', '#c2eb65', '#94bc9a', '#cbba85', '#9ca2b4'];
	$data_count = [
	  <?php for($i=0;$i<$c1;$i++){?>
	    {
	      cekid: '<?=$cekid[$i]?>',
	      cluster: '<?=$cluster[$i]?>',
	      count_cluster: '<?=$count_cluster[$i]?>',
	    },
	  <?php } ?>
  ];
  
	$data_sum = [
	  <?php for($i=0;$i<$c2;$i++){?>
	    {
	      cekid2: '<?=$cekid2[$i]?>',
	      cluster2: '<?=$cluster2[$i]?>',
	      sum_value: '<?=$sum_value[$i]?>',
	    },
	  <?php } ?>
	];
	Morris.Bar({
    element: 'count_cluster',
    data: $data_count,
    xLabelAngle: 30,
    resize: true,
    // barRatio: 0.9,
    parseTime: false,
    gridTextSize : 10,
    xkey: ['cluster'],
    ykeys: ['count_cluster'],
    labels: ['Count Cluster'],
    barColors: function (row, series, type) {
        return $katColors[row.x];
    },
  }).on('click', function (i, row) {  
    displayData(i, row);
  });
  
	Morris.Bar({
    element: 'sum_cluster',
    data: $data_sum,
    xLabelAngle: 30,
    resize: true,
    // barRatio: 0.9,
    parseTime: false,
    gridTextSize : 10,
    xkey: ['cluster2'],
    ykeys: ['sum_value'],
    labels: ['Total'],
    barColors: function (row, series, type) {
        return $katColors[row.x];
    },
	}).on('click', function (i, row) {  
    displayData2(i, row);
  });

  function displayData(i, row){
    // alert (row.count_cluster);
    window.open('display-data?id='+row.cekid+'&type=count','_blank');
  }
  function displayData2(i, row){
    // alert (row.count_cluster);
    window.open('display-data?id='+row.cekid2+'&type=symptom','_blank');
  }
</script>
