<?php
    // use miloschuman\highcharts\Highcharts;
    // use miloschuman\highcharts\HighchartsAsset;

    use yii\helpers\Html;
    use yii\helpers\Url;
    

    // HighchartsAsset::register($this)->withScripts(['modules/exporting', 'modules/drilldown']);
    // HighchartsAsset::register($this)->withScripts(['highcharts-3d','highcharts']);

    $this->title="Show More";
    $this->registerCssFile('@web/css/morris.css');
    $connection = \Yii::$app->db;

    $sql_sv_mudah = $connection->createCommand("SELECT *,a.id AS a_id, c.symptom AS c_symtomp, a.regional AS a_regional, w.nama_witel AS w_witel, a.range_day_service AS rds
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    LEFT JOIN witel AS w ON w.id=a.witel
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='0' GROUP BY a.id
    ")->queryAll();
    $sql_sv_normal = $connection->createCommand("SELECT *,a.id AS a_id, c.symptom AS c_symtomp, a.regional AS a_regional, w.nama_witel AS w_witel, a.range_day_service AS rds
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    LEFT JOIN witel AS w ON w.id=a.witel
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='1' OR e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='2' GROUP BY a.id
    ")->queryAll();
    $sql_sv_sulit = $connection->createCommand("SELECT *,a.id AS a_id, c.symptom AS c_symtomp, a.regional AS a_regional, w.nama_witel AS w_witel, a.range_day_service AS rds
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    LEFT JOIN witel AS w ON w.id=a.witel
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service>'2' GROUP BY a.id
    ")->queryAll();

    $sql_service_mudah = $connection->createCommand("SELECT COUNT(*)
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE  e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='0'
    GROUP BY a.range_day_service
    ")->queryScalar(); 

    $sql_service_normal = $connection->createCommand("SELECT COUNT(*)
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='1' OR e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='2'
    ")->queryScalar(); 

    $sql_service_sulit = $connection->createCommand("SELECT COUNT(*)
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service>'2'
    ")->queryScalar();  

    $sql_service_all = $connection->createCommand("SELECT COUNT(*)
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service IS NOT NULL
    ")->queryScalar();
    
    $sql_data_service = $connection->createCommand("SELECT *,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='0',1,0)) AS c_mudah,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='1' OR e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service='2',1,0)) AS c_normal,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service > '2',1,0)) AS c_sulit,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date' AND a.range_day_service IS NOT NULL,1,0)) AS c_all
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]'
    GROUP BY a.range_day_service
    ")->queryAll();

    $sql_data_regional = $connection->createCommand("SELECT *,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date',1,NULL)) AS c_regional, b.regional AS b_regional
    FROM cases AS a
    INNER JOIN regional AS b ON b.id=a.regional
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]'
    GROUP BY a.regional
    ")->queryAll();

    $sql_data_segment = $connection->createCommand("SELECT *,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date',1,0)) AS c_segment, b.segment AS b_segment
    FROM cases AS a
    INNER JOIN segment AS b ON b.id=a.segment
    INNER JOIN symptom AS c ON c.id=a.symptomp
    INNER JOIN count_symptom AS d ON d.symptom=a.symptomp
    INNER JOIN list_centroid AS e ON e.count_symptom=d.id
    WHERE e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]'
    GROUP BY a.segment
    ")->queryAll();

    $mudah = round(($sql_service_mudah / $sql_service_all)*100,2);
    $normal =  round(($sql_service_normal / $sql_service_all)*100,2);
    $sulit =  round(100-($mudah+$normal),2);
    $all =  $mudah+$normal+$sulit;
    
    $seg = [];
    $count_seg = [];
    $c2 = 0;
    foreach($sql_data_segment as $sg => $sgt):
        $seg[] = $sgt['b_segment'];
        $count_seg[] = $sgt['c_segment'];
        $c2++;
    endforeach;

    $reg = [];
    $count_reg = [];
    $c1 = 0;

    foreach($sql_data_regional as $s_dt_reg => $row):
        $reg[] = "Regional ".$row['b_regional'];
        $count_reg[] = $row['c_regional'];
        $c1++;
    endforeach;

    $serv_s_mudah = [];
    $serv_s_normal = [];
    $serv_s_sulit = [];
    $serv_s_mudah_id = [];
    $serv_s_normal_id = [];
    $serv_s_sulit_id = [];
    $serv_s_mudah_rday = [];
    $serv_s_normal_rday = [];
    $serv_s_sulit_rday = [];
    $tgl_0 = [];
    $bln_0 = [];
    $thn_0 = [];
    $tgl_1 = [];
    $bln_1 = [];
    $thn_1 = [];
    $tgl_2 = [];
    $bln_2 = [];
    $thn_2 = [];
    $tgl_a = [];
    $c_all = 0;
    $c_mudah = 0;
    $c_normal = 0;
    $c_sulit = 0;

    $srv_date_id_mudah = [];

    foreach($sql_sv_mudah as $s_service_m => $s_m):
        // array_push($srv_date_id_mudah, array("x"=> date("Y-m-d", strtotime($s_m['date_open'])), "y"=> $s_m['a_id']));
        // $srv_date_id_mudah[] = join([date("Y-m-d", strtotime($s_m['date_open'])),$s_m['a_id']],',');
        $rds = $s_m['rds'];
        if($s_m['amcrew'] == NULL || $s_m['amcrew'] == ''):
            $s_m['amcrew'] = "Closed by sistem";
        endif;

        $serv_s_mudah[] = $s_m['trouble_ticket'].'['.$s_m['c_symtomp'].']<br>Regional: '.$s_m['a_regional'].'/'.$s_m['w_witel'].'/'.$s_m['datel'].'<br>Handling: '.$s_m['amcrew'];
        $serv_s_mudah_id[] = $s_m['a_id'];
        $serv_s_mudah_rday[] = $s_m['range_day_service'];
        // $tgl_0[] = strtotime($s_m['date_open']);
        $tgl_0[] = date("j", strtotime($s_m['date_open']));
        $month = date("n", strtotime($s_m['date_open']));
        $bln_0[] = $month-1;
        $thn_0[] = date("Y", strtotime($s_m['date_open']));
        $c_mudah++;
    endforeach;

    foreach($sql_sv_normal as $s_service_n => $s_n):
        if($s_n['amcrew'] == NULL || $s_n['amcrew'] == ''):
            $s_n['amcrew'] = "Closed by sistem";
        endif;
        
        $serv_s_mudah[] = $s_n['trouble_ticket'].'['.$s_n['c_symtomp'].']<br>Regional: '.$s_n['a_regional'].'/'.$s_n['w_witel'].'/'.$s_n['datel'].'<br>Handling: '.$s_n['amcrew'];
        // $serv_s_normal[] = $s_n['trouble_ticket'];
        $serv_s_normal_id[] = $s_n['a_id'];
        $serv_s_normal_rday[] = $s_n['range_day_service'];
        // $tgl[] = date("Y-m-d", strtotime($s_n['date_open']));
        // $tgl_1[] = strtotime($s_n['date_open']);
        $tgl_1[] = date("j", strtotime($s_n['date_open']));
        $month1 = date("n", strtotime($s_n['date_open']));
        $bln_1[] = $month1-1;
        $thn_1[] = date("Y", strtotime($s_n['date_open']));
        // echo $s_n['a_id']."<br>";
        $c_normal++;
    endforeach;
    // echo "------------------------------------------";
    foreach($sql_sv_sulit as $s_service_s => $s_s):
        if($s_s['amcrew'] == NULL || $s_s['amcrew'] == ''):
            $s_s['amcrew'] = "Closed by sistem";
        endif;
        $serv_s_mudah[] = $s_s['trouble_ticket'].'['.$s_s['c_symtomp'].']<br>Regional: '.$s_s['a_regional'].'/'.$s_s['w_witel'].'/'.$s_s['datel'].'<br>Handling: '.$s_s['amcrew'];
        // $serv_s_sulit[] = $s_s['trouble_ticket'];
        $serv_s_sulit_id[] = $s_s['a_id'];
        $serv_s_sulit_rday[] = $s_s['range_day_service'];
        // $tgl[] = date("Y-m-d", strtotime($s_s['date_open']));
        // $tgl_2[] = strtotime($s_s['date_open']);
        $tgl_2[] = date("j", strtotime($s_s['date_open']));
        $month2 = date("n", strtotime($s_s['date_open']));
        $bln_2[] = $month2-1;
        $thn_2[] = date("Y", strtotime($s_s['date_open']));
        // echo $s_s['a_id']."<br>";
        $c_sulit++;
    endforeach;
?>
<div class="index-show-more">
    <div id="sv-mudah" style="display:none;"></div>
    <div id="service-mudah" style="display:none;"><?=$mudah?></div>
    <div id="service-normal" style="display:none;"><?=$normal?></div>
    <div id="service-sulit" style="display:none;"><?=$sulit?></div>
    <div id="service-mudah-val" style="display:none;"><?=$sql_service_mudah?></div>
    <div id="service-normal-val" style="display:none;"><?=$sql_service_normal?></div>
    <div id="service-sulit-val" style="display:none;"><?=$sql_service_sulit?></div>

    <div style="text-align:center;text-transform:uppercase;font-weight:bold;">Cluster <?=$data['cluster']?></div>
    <div style="text-align:right;position:fixed;right:30px;">
    <?= Html::a("<span class='btn btn-danger'><i class='fa fa-arrow-circle-left'></i> Back</span>", Url::toRoute(['site/display-data',
            'id' => $data['id'],
            'type' => $type
        ]), [
            'title' => Yii::t('app', 'Back to list cluster by symptom'),
        ]);
    ?>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="col-lg-12" style="border-bottom:0.5px solid #cfcdcd;">
                <div style="text-align:center;font-weight:bold;">Symptom by Regional</div>
                <div id="reg0" style="height: 200px;background-color:transparent;"></div>
            </div>
            <div class="col-lg-12">
                <div style="text-align:center;font-weight:bold;">Service Status</div>
                <div id="serv0" style="height: 300px;background-color:transparent;"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="col-lg-12" style="border-left:0.5px solid #cfcdcd;">
                <div style="text-align:center;font-weight:bold;">By Segment</div>
                <div id="seg0" style="height: 500px;background-color:transparent;"></div>
            </div>
        </div>
        <br>
        <div class="col-lg-12" style="border-top:0.5px solid #cfcdcd;">
            <div style="text-align:center;font-weight:bold;">Service Status Detail</div>
            <div id="chartContainer" style="max-height:200px;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$data_sv_mudah = [
	  <?php for($i=0;$i<$c_mudah;$i++){?>
	    {
	      x: <?=$tgl_0[$i]?>,
	      y: <?=$serv_s_mudah_id[$i]?>
	    },
	  <?php } ?>
    ];
	$data_sv_normal = [
	  <?php for($k=0;$k<$c_normal;$k++){?>
	    {
	      x: <?=$tgl_1[$k]?>,
	      y: <?=$serv_s_normal_id[$k]?>,
	    },
	  <?php } ?>
    ];
	$data_sv_sulit = [
	  <?php for($l=0;$l<$c_sulit;$l++){?>
	    {
	      x: <?=$tgl_2[$l]?>,
	      y: <?=$serv_s_sulit_id[$l]?>,
	    },
	  <?php } ?>
    ];
    // console.log($data_sv_mudah);
    // console.log($data_sv_mudah.join(','));
    // console.log($data_sv_mudah);
    // console.log($data_sv_normal);
    // console.log($data_sv_sulit);
    // console.log($('#serv_s_normal').html());

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
    
    console.log($data_seg);
    
	$data_sv = [
	  <?php for($j=0;$j<$c2;$j++){?>
	    {
	        y: <?=$count_seg[$j]?>,
	        label: '<?=$seg[$j]?>',
	    },
	  <?php } ?>
    ];

    var katColors = ['#f0910f', '#9c6372', '#656049', '#e91b53', '#035bc7', '#60c1f0', '#f4d25b', '#c2eb65', '#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#7901c9', '#d6e41f', '#94bc9a', '#cbba85', '#9ca2b4'];

    new Morris.Bar({
        element: 'reg0',
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

    window.onload = function() {
        
        var mudah = $('#service-mudah').text();
        var normal = $('#service-normal').text();
        var sulit = $('#service-sulit').text();

        var vmudah = $('#service-mudah-val').text();
        var vnormal = $('#service-normal-val').text();
        var vsulit = $('#service-sulit-val').text();

        // console.log(vmudah);
        var service_chart = new CanvasJS.Chart("serv0", {
            animationEnabled: true,
            theme: "light2",
            backgroundColor: "transparent",
            title: {
                text: ""
            },
            legend:{
                fontSize: 11,
                horizontalAlign: "right",
                verticalAlign: "center",
                fontColor: "#7f7f7f",
                fontFamily: "Roboto"
            },
            data: [{
                type: "pie",
                showInLegend: true,
                legendText: "{label}",
                yValueFormatString: "##0.##'%'",
                indexLabel: "{label} {y}",
                // indexLabelPlacement: "inside",
                indexLabelFontStyle: "bold",
                dataPoints: [
                    { y:mudah, label:"Mudah("+vmudah+")|", color:'green'},
                    { y:normal, label:"Normal("+vnormal+")|", color:'#e79c01'},
                    { y:sulit, label:"Sulit("+vsulit+")|", color:'red'}
                ]
            }]
        });

        service_chart.render();

        var chart = new CanvasJS.Chart("seg0", {
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

        var chart_scatter = new CanvasJS.Chart("chartContainer", {
            // animationEnabled: true,
            height: 500,
            title:{
                text: ""
            },
            legend: {
                horizontalAlign: "left", // "center" , "right"
                verticalAlign: "center",  // "top" , "bottom"
                fontSize: 15
            },
            axisX:{
                // title: "timeline",
                // valueFormatString: "#,##0.##",
                // labelFormatter: function(e){
                //     return "x: " + e.value;  
                // },
                valueFormatString: "DD-MMM",
                gridThickness: 1,
                labelFontSize: 12,
                interval:1,
                intervalType: "day"
            },
            axisY:{
                labelFormatter: function ( e ) {
                    return e.value;  
                },
                labelFontSize: 12,
                titleFontSize: 15,
                title: "Queue tickets",
                interval:50
            },
            data: [{
                type: "scatter",
                toolTipContent: "<span style=\"color:#0cb859 \"><b>{m}</b></span><br/><b>",
                name: "Mudah",
                markerColor: "#0cb859",
                // axisYType: "secondary",
                showInLegend: true,
                indexLabel: false,
                // legendText: "{y}",
                xValueType: "dateTime",
                dataPoints: [
                <?php for($i=0;$i<$c_mudah;$i++){?>
                    {
                        x: new Date(<?=$thn_0[$i]?>, <?=$bln_0[$i]?>, <?=$tgl_0[$i]?>),
                        y: <?=$serv_s_mudah_id[$i]?>,
                        m: "<?=$serv_s_mudah[$i]?>",
                    },
                <?php }?>
                ]
            },
            {
                type: "scatter",
                toolTipContent: "<span style=\"color:#dcb414 \"><b>{m}</b></span><br/><b>",
                name: "Normal",
                showInLegend: true, 
                markerColor: "#dcb414",
                xValueType: "dateTime",
                // toolTipContent: "<span style=\"color:#C0504E \"><b>{x}</b></span><br/><b> Load:</b> {y} TPS<br/><b> Response Time:</b></span> {y} ms",
                dataPoints: [  
                    <?php for($j=0;$j<$c_normal;$j++){?>
                        {
                            x: new Date(<?=$thn_1[$j]?>, <?=$bln_1[$j]?>, <?=$tgl_1[$j]?>),
                            y: <?=$serv_s_normal_id[$j]?>,
                            m: "<?=$serv_s_mudah[$j]?>",
                        },
                    <?php }?>
                ]
            },
            {
                type: "scatter",
                name: "Sulit",
                markerColor: "#bf0409",
                showInLegend: true, 
                xValueType: "dateTime",
                toolTipContent: "<span style=\"color:#bf0409 \"><b>{m}</b>",
                dataPoints: [
                    <?php for($k=0;$k<$c_sulit;$k++){?>
                        {
                            x: new Date(<?=$thn_2[$k]?>, <?=$bln_2[$k]?>, <?=$tgl_2[$k]?>),
                            y: <?=$serv_s_sulit_id[$k]?>,
                            m: "<?=$serv_s_mudah[$k]?>",
                        },
                    <?php }?>
                ]
            }]
        });
        chart_scatter.render();
    }
</script>