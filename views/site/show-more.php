<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    
    $this->title="Show More";
    $this->registerCssFile('@web/css/morris.css');
    $connection = \Yii::$app->db;

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
    
?>

<div class="index-show-more">
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
    </div>
</div>

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
    }
</script>