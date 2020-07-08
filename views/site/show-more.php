<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    
    $this->title="Show More";
    $this->registerCssFile('@web/css/morris.css');
    $connection = \Yii::$app->db;

    $sql_data_regional = $connection->createCommand("SELECT *,
    SUM(IF(e.iterasi='$data[iterasi]' AND e.cluster='$data[cluster]' AND date(a.date_open)>='$start_date' AND date(a.date_open)<='$end_date',1,0)) AS c_regional, b.regional AS b_regional
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

    $seg = [];
    $count_seg = [];
    $c2 = 0;
    foreach($sql_data_segment as $sgt):
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
            <div style="text-align:center;font-weight:bold;">Symptom by Regional</div>
            <div id="reg0" style="height: 300px;background-color:transparent;"></div>
        </div>
        <div class="col-lg-6">
            <div style="text-align:center;font-weight:bold;">By Segment</div>
            <div id="seg0" style="height: 300px;background-color:transparent;"></div>
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

    var katColors = ['#f0910f', '#9c6372', '#656049', '#e91b53', '#ca001f', '#02c89b', '#ca00a7', '#fca570', '#61c505', '#035bc7', '#7901c9', '#d6e41f', '#60c1f0', '#f4d25b', '#c2eb65', '#94bc9a', '#cbba85', '#9ca2b4'];

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