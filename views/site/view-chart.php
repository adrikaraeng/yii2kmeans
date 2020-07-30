<?php

use yii\helpers\Html;
use yii\helpers\Url;

// $this->registerCssFile('@web/css/morris.css');
// $this->registerJsFile('@web/morrisjs/morris.js');
$connection = \Yii::$app->db;

if($t == 'symtomp'){
  $title = 'Simptom';
}else{
  $title = 'Teknisi';
}

$max_iterasi = $connection->createCommand("SELECT max(iterasi) FROM list_centroid WHERE kmeans_type='$t'")->queryScalar();
$list_count_cluster = $connection->createCommand("SELECT * FROM list_centroid WHERE kmeans_type='$t' AND iterasi='$max_iterasi' GROUP BY cluster ORDER BY cluster ASC")->queryAll();

$date_selected = $connection->createCommand("SELECT * FROM count_cluster ORDER BY id DESC")->queryOne();
$get_mudah = $connection->createCommand("SELECT *,b.symptom AS b_symtomp, c.regional AS c_regional, d.nama_witel AS d_witel FROM cases AS a 
LEFT JOIN symptom AS b ON b.id=a.symptomp 
LEFT JOIN regional AS c ON c.id=a.regional 
LEFT JOIN witel AS d ON d.id=a.witel 
WHERE DATE(date_open)>='$date_selected[start_date]' AND DATE(date_open) <= '$date_selected[end_date]' AND range_day_service='0'")->queryAll();
$get_normal = $connection->createCommand("SELECT *,b.symptom AS b_symtomp, c.regional AS c_regional, d.nama_witel AS d_witel FROM cases AS a 
LEFT JOIN symptom AS b ON b.id=a.symptomp 
LEFT JOIN regional AS c ON c.id=a.regional 
LEFT JOIN witel AS d ON d.id=a.witel 
WHERE DATE(date_open)>='$date_selected[start_date]' AND DATE(date_open) <= '$date_selected[end_date]' AND range_day_service='1' OR DATE(date_open)>='$date_selected[start_date]' AND DATE(date_open) <= '$date_selected[end_date]' AND range_day_service='2'")->queryAll();
$get_sulit = $connection->createCommand("SELECT *,b.symptom AS b_symtomp, c.regional AS c_regional, d.nama_witel AS d_witel FROM cases AS a 
LEFT JOIN symptom AS b ON b.id=a.symptomp 
LEFT JOIN regional AS c ON c.id=a.regional 
LEFT JOIN witel AS d ON d.id=a.witel 
WHERE DATE(date_open)>='$date_selected[start_date]' AND DATE(date_open) <= '$date_selected[end_date]' AND range_day_service>'2'")->queryAll();

$serv_mudahx = [];
$serv_mudahy = [];
$title_mudah = [];

$serv_normalx = [];
$serv_normaly = [];
$title_normal = [];

$serv_sulitx = [];
$serv_sulity = [];
$title_sulit = [];

$c_mudah = 0;
$c_normal = 0;
$c_sulit = 0;

foreach($get_mudah as $g_mudah => $gm):
  $serv_mudahx[] = $gm['val_uniqx'];
  $serv_mudahy[] = $gm['val_uniqy'];
  $title_mudah[] = $gm['trouble_ticket'].'['.$gm['b_symtomp'].']<br>Regional: '.$gm['c_regional'].'/'.$gm['d_witel'].'/'.$gm['datel'].'<br>Handling: '.$gm['amcrew'];
  $c_mudah++;
endforeach;

foreach($get_normal as $g_normal => $gn):
  $serv_normalx[] = $gn['val_uniqx'];
  $serv_normaly[] = $gn['val_uniqy'];
  $title_normal[] = $gn['trouble_ticket'].'['.$gn['b_symtomp'].']<br>Regional: '.$gn['c_regional'].'/'.$gn['d_witel'].'/'.$gn['datel'].'<br>Handling: '.$gn['amcrew'];
  $c_normal++;
endforeach;
foreach($get_sulit as $g_sulit => $gs):
  $serv_sulitx[] = $gs['val_uniqx'];
  $serv_sulity[] = $gs['val_uniqy'];
  $title_sulit[] = $gs['trouble_ticket'].'['.$gs['b_symtomp'].']<br>Regional: '.$gs['c_regional'].'/'.$gs['d_witel'].'/'.$gs['datel'].'<br>Handling: '.$gs['amcrew'];
  $c_sulit++;
endforeach;

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
  <div id="chart_scatter_poin">
    <div style="text-align:center;font-weight:bold;">Count by Services</div>
    <div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>
  </div>
</div>

<script src="<?=Yii::getAlias('@web/canvasjs-2.3.1/canvasjs.min.js')?>"></script>
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

  

var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	zoomEnabled: true,
	title:{
		text: ""
	},
	axisX: {
    title:"",
    tickLength: 0,
    // lineThickness: 0,
		// minimum: 790,
    // maximum: 2260
    labelFormatter: function(){
      return " ";
    }
	},
	axisY:{
		title: "",
    tickLength: 0,
    // lineThickness: 0,
    // valueFormatString: "$#,##0k"
    labelFormatter: function(){
      return " ";
    }
	},
  legend: {
      horizontalAlign: "left", // "center" , "right"
      verticalAlign: "center",  // "top" , "bottom"
      fontSize: 15
  },
	data: [{
    type: "scatter",
    name: "Mudah",
    markerColor: "#0cb859",
    showInLegend: true, 
    toolTipContent: "<span style=\"color:#0cb859 \"><b>{m}</b></span>",
		dataPoints: [
      <?php for($i=0;$i<$c_mudah;$i++){?>
      {
        x: <?=$serv_mudahx[$i]?>,
        y: <?=$serv_mudahy[$i]?>,
        m: "<?=$title_mudah[$i]?>",
      },
      <?php }?>
    ]
  },
  {
    type: "scatter",
    name: "Normal",
    markerColor: "#dcb414",
    showInLegend: true, 
    toolTipContent: "<span style=\"color:#dcb414 \"><b>{m}</b></span>",
		dataPoints: [
      <?php for($i=0;$i<$c_normal;$i++){?>
      {
        x: <?=$serv_normalx[$i]?>,
        y: <?=$serv_normaly[$i]?>,
        m: "<?=$title_normal[$i]?>",
      },
      <?php }?>
    ]
  },{
    type: "scatter",
    name: "Sulit",
    markerColor: "#bf0409",
    showInLegend: true, 
    toolTipContent: "<span style=\"color:#bf0409 \"><b>{m}</b></span>",
		dataPoints: [
      <?php for($i=0;$i<$c_sulit;$i++){?>
      {
        x: <?=$serv_sulitx[$i]?>,
        y: <?=$serv_sulity[$i]?>,
        m: "<?=$title_sulit[$i]?>",
      },
      <?php }?>
    ]
  },
  ]
});
chart.render();
</script>