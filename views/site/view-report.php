<?php
  use yii\helpers\Html;
  use yii\helpers\Url;
  $this->title = Yii::t('app', 'View Data');

  $connection = \Yii::$app->db;
  
  $max_iterasi = $connection->createCommand("SELECT max(iterasi) FROM report_list_centroid WHERE date_report='$date_report'")->queryScalar();
  $list_count_cluster = $connection->createCommand("SELECT * FROM report_list_centroid WHERE iterasi='$max_iterasi' AND date_report='$date_report' GROUP BY cluster ORDER BY cluster ASC")->queryAll();

  $cluster2 = [];
  $cekid2 = [];
  $sum_value = [];
  $c_reg = 0;
  
  foreach($list_count_cluster as $v => $vl):
    // SUM(IF(sentiment='Positive' AND (review_last_update_date_and_time BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS c_sent_positif
    $list_value_cluster = $connection->createCommand("SELECT a.*,b.*,
    SUM(IF(a.iterasi='$max_iterasi' AND a.cluster='$vl[cluster]' AND b.id=a.count_symptom, (b.reg1 + b.reg2 + b.reg3 + b.reg4 + b.reg5 + b.reg6 + b.reg7),0)) AS jlh_reg
    FROM report_list_centroid AS a
    INNER JOIN report_count_symptom AS b ON b.id=a.count_symptom
    WHERE b.id=a.count_symptom AND a.iterasi='$max_iterasi' AND a.cluster='$vl[cluster]'")->queryOne();

    $cluster2[] = "Cluster ".$vl['cluster'];
    $cekid2[] = $vl['id'];
    $sum_value[] = $list_value_cluster['jlh_reg'];
    // echo $list_value_cluster['jlh_reg']."<br>";
    $c_reg++;
  endforeach;
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

<div class="data-table">
    <div class="input-container" style="text-align:center;float:left;max-width:400px;width:100%;">
      <i class="fa fa-search icon"></i>
      <input type="text" name="search" id="search" class="input-field" style="width:300px;" placeholder="Search data">
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
            <th style="width:250px;">Symptom Problem [Segment]</th>
            <th>Amcrew</th>
            <th>Status Service</th>
        </tr>
        <?php $nom=1;foreach($data as $cdata => $cd):?>
        <?php
            $witel = $connection->createCommand("SELECT * FROM witel WHERE id='$cd[witel]'")->queryOne();   
            $simtom = $connection->createCommand("SELECT * FROM symptom WHERE id='$cd[symptomp]'")->queryOne();
            $segment = $connection->createCommand("SELECT * FROM segment WHERE id='$cd[segment]'")->queryOne();
        ?>
        <tr id="data-search">
            <td><?=$nom++?></td>
            <td><?=$cd['trouble_ticket']?></td>
            <td><?=$cd['internet_number']?></td>
            <td><?=$cd['speed']?></td>
            <td><?=$cd['packet']?></td>
            <td><?=$cd['regional']?></td>
            <td><?=$witel['nama_witel']?></td>
            <td><?=$cd['datel']?></td>
            <td><?=$simtom['symptom']?> [<?=$segment['segment']?>]</td>
            <td><?=$cd['amcrew']?></td>
            <td style="text-align:center;"><?php
                if($cd['range_day_service'] == "0"):
                    echo "<span class='label label-success'>Mudah</span>";
                elseif($cd['range_day_service'] == "1" || $cd['range_day_service'] == '2'):
                    echo "<span class='label label-warning'>Normal</span>";
                elseif($cd['range_day_service'] > 2):
                    echo "<span class='label label-danger'>Sulit</span>";
                endif;
            ?></td>
        </tr>
        <?php endforeach;?>
      </table>
    </div>
</div>
<div class="data-chart">
  <div class="row">
    <div class="col-lg-6">
      <div class="col-lg-12" id="id_chart_cluster" style="width:555px;position:relative;border:0.5px solid #9b9b9b;padding:5px;"> 
          <div style="text-align:center;font-weight:bold;">Total Symptom By Cluster</div>
          <div id="sum_cluster" style="height: 500px;background-color:transparent;"></div>
      </div>
    </div>

    <?php $id='1'; foreach($list_count_cluster as $lcount => $lcc):?>
    <?php 
      $sql_data_regional = $connection->createCommand("SELECT *,
      SUM(IF(e.iterasi='$lcc[iterasi]' AND e.cluster='$lcc[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date',1,NULL)) AS c_regional, b.regional AS b_regional
      FROM report_data AS a
      INNER JOIN regional AS b ON b.id=a.regional
      INNER JOIN symptom AS c ON c.id=a.symptomp
      INNER JOIN report_count_symptom AS d ON d.symptom=a.symptomp
      INNER JOIN report_list_centroid AS e ON e.count_symptom=d.id
      WHERE e.iterasi='$lcc[iterasi]' AND e.cluster='$lcc[cluster]'
      GROUP BY a.regional
      ")->queryAll();

      $sql_data_segment = $connection->createCommand("SELECT *,
      SUM(IF(e.iterasi='$lcc[iterasi]' AND e.cluster='$lcc[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date',1,0)) AS c_segment, b.segment AS b_segment
      FROM report_data AS a
      INNER JOIN segment AS b ON b.id=a.segment
      INNER JOIN symptom AS c ON c.id=a.symptomp
      INNER JOIN report_count_symptom AS d ON d.symptom=a.symptomp
      INNER JOIN report_list_centroid AS e ON e.count_symptom=d.id
      WHERE e.iterasi='$lcc[iterasi]' AND e.cluster='$lcc[cluster]'
      GROUP BY a.segment
      ")->queryAll();

      $reg = [];
      $count_reg = [];
      $c1 = 0;

      foreach($sql_data_regional as $s_dt_reg => $row):
        $reg[] = "Regional ".$row['b_regional'];
        $count_reg[] = $row['c_regional'];
        $c1++;
      endforeach;
      
      $seg = [];
      $count_seg = [];
      $c2 = 0;
      foreach($sql_data_segment as $sg => $sgt):
        $seg[] = $sgt['b_segment'];
        $count_seg[] = $sgt['c_segment'];
        $c2++;
      endforeach;
    ?>
    
    <div class="col-lg-6">
      <div class="col-lg-12" style="border-bottom:0.5px solid #cfcdcd;border:0.5px solid #9b9b9b;padding:5px;">
          <div style="text-align:center;font-weight:bold;">Symptom by Regional cluster <?=$lcc['cluster']?></div>
          <div id="reg0<?=$id?>" style="height:500px;background-color:transparent;"></div>
      </div>
    </div>
    <div class="col-lg-6">
        <div class="col-lg-12" style="border-bottom:0.5px solid #cfcdcd;border:0.5px solid #9b9b9b;padding:5px;">
          <div style="text-align:center;font-weight:bold;">By Segment Cluster <?=$lcc['cluster']?></div>
          <div id="seg0<?=$id?>" style="height:500px;background-color:transparent;"></div>
        </div>
    </div>
    <div style="clear:left;"></div>
    
    <script type="text/javascript">
      $data_reg = [
        <?php for($i=0;$i<$c1;$i++){?>
          {
            reg: '<?=$reg[$i]?>',
            count_reg: '<?=$count_reg[$i]?>',
          },
        <?php } ?>
      ];
    
      $data_seg = [
        <?php for($j=0;$j<$c2;$j++){?>
          {
              y: <?=$count_seg[$j]?>,
              label: '<?=$seg[$j]?>',
          },
        <?php } ?>
      ];
      
      var katColors = ['#f0910f', '#9c6372', '#656049', '#e91b53', '#035bc7', '#60c1f0', '#f4d25b', '#c2eb65', '#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#7901c9', '#d6e41f', '#94bc9a', '#cbba85', '#9ca2b4'];

        new Morris.Bar({
            element: 'reg0<?=$id?>',
            data: $data_reg,
            xLabelAngle: 30,
            resize: true,
            // barRatio: 0.9,
            parseTime: false,
            gridTextSize : 10,
            xkey: ['reg'],
            ykeys: ['count_reg'],
            labels: ['Total'],
            barColors: function (row, series, type) {
                return katColors[row.x];
            },
        });

        var chart = new CanvasJS.Chart("seg0<?=$id?>", {
            animationEnabled: true,
            height: 500,
            // backgroundColor: "transparent",
            title: {
                text: ""
            },
            axisX:{
                interval: 1
            },
            axisY2:{
                interlacedColor: "rgba(1,77,101,.2)",
                gridColor: "rgba(1,77,101,.1)",
                // title: "Number of Companies"
            },
            data: [{
                type: "bar",
                // name: "companies",
                axisYType: "secondary",
                color: "#014D65",
                dataPoints: $data_seg
            }]
        });
        chart.render();
    </script>
    
    <?php $id++;endforeach;?>
  </div>
</div>

<script type="text/javascript">

  var $katColors = ['#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#035bc7', '#7901c9', '#f0910f', '#9c6372', '#656049', '#e91b53', '#d6e41f', '#60c1f0', '#f4d25b', '#c2eb65', '#94bc9a', '#cbba85', '#9ca2b4'];
  
	$data_sum = [
	  <?php for($i=0;$i<$c_reg;$i++){?>
	    {
	      cekid2: '<?=$cekid2[$i]?>',
	      cluster2: '<?=$cluster2[$i]?>',
	      sum_value: '<?=$sum_value[$i]?>',
	    },
	  <?php } ?>
	];
  
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
	});
</script>
                
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