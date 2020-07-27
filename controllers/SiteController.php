<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Cases;
use app\models\CasesSearch;
use app\models\ImportFormCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\User;
use app\models\clusterForm;
use app\models\CountCluster;
use app\models\CountSymptom;
use app\models\CountsymptomSearch;
use app\models\Centroid;
use app\models\CentroidSearch;
use app\models\ListCentroid;
use app\models\ListcentroidSearch;
use app\models\Segment;
use app\models\SegmentSearch;
use app\models\ReportCountCluster;
use app\models\ReportCountClusterSearch;
use app\models\ReportCountSymptom;
use app\models\ReportData;
use app\models\ReportListCentroid;
use app\models\ReportCentroid;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionViewReport($id)
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);
      $model = ReportCountCluster::findOne($id);

      $data = $connection->createCommand("SELECT * FROM report_data WHERE date_open >= '$model->start_date' AND date_open <= '$model->end_date'")->queryAll();

      return $this->render('view-report',[
        'data' => $data,
        'date_report' => $model->date_report,
        'start_date' => $model->start_date,
        'end_date' => $model->end_date
      ]);
    }
    public function actionListReport()
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);
      
      $searchModel = new ReportCountClusterSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('report-count-cluster', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
      ]);
    }

    public function actionReport($title)
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      
      $user = User::findOne(Yii::$app->user->id);

      $cek_tgl = $connection->createCommand("SELECT * FROM count_cluster ORDER BY id DESC")->queryOne();
      // die();
      $model_count_cluster = new ReportCountCluster;

      $cek_if_av = $connection->createCommand("SELECT * FROM report_count_cluster WHERE start_date='$cek_tgl[start_date]' AND end_date='$cek_tgl[end_date]' AND jumlah_cluster='$cek_tgl[jumlah_cluster]'")->queryOne();

      $cek_report_cases = $connection->createCommand("SELECT * FROM report_data WHERE DATE(date_open)>='$cek_tgl[start_date]' AND DATE(date_open)<='$cek_tgl[end_date]'")->queryAll();
      
      $cek_cases = $connection->createCommand("SELECT * FROM cases WHERE DATE(date_open)>='$cek_tgl[start_date]' AND DATE(date_open)<='$cek_tgl[end_date]'")->queryAll();
      $cek_count_symptom = $connection->createCommand("SELECT * FROM count_symptom WHERE kmeans_type='$cek_tgl[kmeans_type]'")->queryAll();

      $max_iterasi = $connection->createCommand("SELECT MAX(iterasi) FROM list_centroid")->queryScalar();
      $cek_list_centroid = $connection->createCommand("SELECT * FROM list_centroid WHERE iterasi='$max_iterasi'")->queryAll();

      // $max_centroid = $connection->createCommand("SELECT MAX(cluster) FROM centroid WHERE cluster='$cek_tgl[jumlah_cluster]'")->queryScalar();
      $cek_report_centroid = $connection->createCommand("SELECT * FROM centroid WHERE login='$user->id'")->queryAll();

      if($cek_if_av == NULL):
        $model_count_cluster->login = $cek_tgl['login'];
        $model_count_cluster->kmeans_type = $cek_tgl['kmeans_type'];
        $model_count_cluster->start_date = $cek_tgl['start_date'];
        $model_count_cluster->end_date = $cek_tgl['end_date'];
        $model_count_cluster->jumlah_cluster = $cek_tgl['jumlah_cluster'];
        $model_count_cluster->date_report = date('Y-m-d H:i:s');
        $model_count_cluster->report_by = $user->id;
        $model_count_cluster->save(false);

        if($cek_report_cases == NULL):
          foreach($cek_cases as $cek_case => $ccase):
            $model_report_data = new ReportData;
            $cekAv = $connection->createCommand("SELECT * FROM report_data WHERE date_open='$ccase[date_open]' AND trouble_ticket='$ccase[trouble_ticket]'")->queryOne();
            if($cekAv == NULL):
              $model_report_data->date_open = $ccase['date_open'];
              $model_report_data->trouble_ticket = $ccase['trouble_ticket'];
              $model_report_data->symptomp = $ccase['symptomp'];
              $model_report_data->segment = $ccase['segment'];
              $model_report_data->ncli = $ccase['ncli'];
              $model_report_data->internet_number = $ccase['internet_number'];
              $model_report_data->pstn = $ccase['pstn'];
              $model_report_data->regional =$ccase['regional'];
              $model_report_data->witel = $ccase['witel'];
              $model_report_data->datel = $ccase['datel'];
              $model_report_data->speed = $ccase['speed'];
              $model_report_data->workzone_amcrew = $ccase['workzone_amcrew'];
              $model_report_data->amcrew = $ccase['amcrew'];
              $model_report_data->packet = $ccase['packet'];
              $model_report_data->status = $ccase['status'];
              $model_report_data->date_closed = $ccase['date_closed'];
              $model_report_data->range_day_service = $ccase['range_day_service'];
              $model_report_data->login = $ccase['login'];
              $model_report_data->save(false);
            endif;
          endforeach;

          foreach($cek_report_centroid as $c_centroid => $c_cd):
            $model_report_centroid = new ReportCentroid;
            $model_report_centroid->login = $c_cd['login'];
            $model_report_centroid->kmeans_type = $c_cd['kmeans_type'];
            $model_report_centroid->reg1 = $c_cd['reg1'];
            $model_report_centroid->reg2 = $c_cd['reg2'];
            $model_report_centroid->reg3 = $c_cd['reg3'];
            $model_report_centroid->reg4 = $c_cd['reg4'];
            $model_report_centroid->reg5 = $c_cd['reg5'];
            $model_report_centroid->reg6 = $c_cd['reg6'];
            $model_report_centroid->reg7 = $c_cd['reg7'];
            $model_report_centroid->iterasi = $c_cd['iterasi'];
            $model_report_centroid->cluster = $c_cd['cluster'];
            $model_report_centroid->id_count_symptom = $c_cd['id_count_symptom'];
            $model_report_centroid->date_report = $model_count_cluster->date_report;
            $model_report_centroid->save(false);
          endforeach;

          foreach($cek_count_symptom as $c_counts => $ccs):
            $model_count_symptom = new ReportCountSymptom;
            $model_count_symptom->id = $ccs['id'];
            $model_count_symptom->login = $ccs['login'];
            $model_count_symptom->symptom = $ccs['symptom'];
            $model_count_symptom->kmeans_type = $ccs['kmeans_type'];
            $model_count_symptom->reg1 = $ccs['reg1'];
            $model_count_symptom->reg2 = $ccs['reg2'];
            $model_count_symptom->reg3 = $ccs['reg3'];
            $model_count_symptom->reg4 = $ccs['reg4'];
            $model_count_symptom->reg5 = $ccs['reg5'];
            $model_count_symptom->reg6 = $ccs['reg6'];
            $model_count_symptom->reg7 = $ccs['reg7'];
            $model_count_symptom->date_report = $model_count_cluster->date_report;
            $model_count_symptom->save(false);
          endforeach;

          foreach($cek_list_centroid as $c_listc => $clc):
            $model_count_list_centroid = new ReportListCentroid;
            $model_count_list_centroid->login = $clc['login'];
            $model_count_list_centroid->kmeans_type = $clc['kmeans_type'];
            $model_count_list_centroid->count_symptom = $clc['count_symptom'];
            $model_count_list_centroid->iterasi = $clc['iterasi'];
            $model_count_list_centroid->cluster = $clc['cluster'];
            $model_count_list_centroid->c1 = $clc['c1'];
            $model_count_list_centroid->c2 = $clc['c2'];
            $model_count_list_centroid->c3 = $clc['c3'];
            $model_count_list_centroid->c4 = $clc['c4'];
            $model_count_list_centroid->c5 = $clc['c5'];
            $model_count_list_centroid->date_report = $model_count_cluster->date_report;
            $model_count_list_centroid->save(false);
          endforeach;
        endif;

        Yii::$app->session->setFlash('success', "Success reported");
        return $this->redirect(['analisis-cluster','title'=>$title]);
      else:
        Yii::$app->session->setFlash('error', "Data has been reported");
        return $this->redirect(['analisis-cluster','title'=>$title]);
      endif;
    }

    public function actionShowMore($id, $type)
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $data = $connection->createCommand("SELECT * FROM list_centroid WHERE id='$id' GROUP BY iterasi ORDER BY iterasi DESC")->queryOne();
      $cek_tgl = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$data[kmeans_type]' ORDER BY id DESC")->queryOne();

      return $this->render('/site/show-more',[
        'data' => $data,
        'type' => $type,
        'start_date' => $cek_tgl['start_date'],
        'end_date' => $cek_tgl['end_date'],
      ]);
    }

    public function actionDisplayData($id, $type)
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $data = $connection->createCommand("SELECT * FROM list_centroid WHERE id='$id' GROUP BY iterasi ORDER BY iterasi DESC")->queryOne();

      if($type=='count'){
        $cek_tgl = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$data[kmeans_type]' ORDER BY id DESC")->queryOne();

        // $cek_data = $connection->createCommand("SELECT * FROM list_centroid as a
        // INNER JOIN count_symptom as b ON b.id=a.count_symptom
        // INNER JOIN symptom as c ON c.id=b.symptom
        // WHERE a.kmeans_type='$data[kmeans_type]' AND a.cluster='$data[cluster]' AND b.kmeans_type='$data[kmeans_type]' GROUP BY c.id")->queryAll();

        $cek_data = $connection->createCommand("SELECT *, e.regional as tregional, b.symptom AS tsymptom, MAX(d.iterasi) AS max_iterasi, b.symptom AS b_simptom, a.regional AS a_regional, a.witel AS a_witel, a.datel AS a_datel, a.amcrew AS a_amcrew FROM cases AS a 
          INNER JOIN symptom AS b ON b.id=a.symptomp
          INNER JOIN regional AS e ON e.id=a.regional
          INNER JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$data[kmeans_type]'
          INNER JOIN list_centroid AS d ON d.count_symptom=c.id
          WHERE date(a.date_open)>='$cek_tgl[start_date]' AND date(a.date_open) <= '$cek_tgl[end_date]' AND d.cluster='$data[cluster]' AND d.kmeans_type='$data[kmeans_type]' AND d.iterasi='$data[iterasi]' GROUP BY a.symptomp")->queryAll();

        return $this->render('/site/display-data-count', [
          'cek_data' => $cek_data,
          'data' => $data,
          // 'start_date' => $cek_tgl['start_date'],
          // 'end_date' => $cek_tgl['end_date'],
        ]);

      }elseif($type == 'symptom'){
        $cek_tgl = $connection->createCommand("SELECT * FROM count_cluster WHERE kmeans_type='$data[kmeans_type]' ORDER BY id DESC")->queryOne();
        // if($data['kmeans_type'] == 'teknisi'):
        $cek_data = $connection->createCommand("SELECT *, e.regional as tregional, b.symptom AS tsymptom, MAX(d.iterasi) AS max_iterasi, b.id AS b_simptom, a.regional AS a_regional, a.witel AS a_witel, a.datel AS a_datel, a.amcrew AS a_amcrew FROM cases AS a 
          INNER JOIN symptom AS b ON b.id=a.symptomp
          INNER JOIN regional AS e ON e.id=a.regional
          INNER JOIN count_symptom AS c ON c.symptom=b.id AND c.kmeans_type='$data[kmeans_type]'
          INNER JOIN list_centroid AS d ON d.count_symptom=c.id
          WHERE date(a.date_open)>='$cek_tgl[start_date]' AND date(a.date_open) <= '$cek_tgl[end_date]' AND d.cluster='$data[cluster]' AND d.kmeans_type='$data[kmeans_type]' AND d.iterasi='$data[iterasi]' GROUP BY a.amcrew, b.id")->queryAll();
        // endif;
        return $this->render('/site/display-data-symtomp',[
          'cek_data' => $cek_data,
          'data' => $data,
          'type' => $type,
          'start_date' => $cek_tgl['start_date'],
          'end_date' => $cek_tgl['end_date'],
        ]);
      }
    }

    public function actionTechnisian()
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }

      $user = User::findOne(Yii::$app->user->id);

      $model = new CountCluster();

      if ($model->load(Yii::$app->request->post())){
        $start_date = $_POST['CountCluster']['start_date'];
        $end_date = $_POST['CountCluster']['end_date'];

        $cek_if_login_available = $connection->createCommand("SELECT * FROM count_cluster WHERE login='$user->id' AND kmeans_type='teknisi'")->queryAll();
        if($cek_if_login_available){
          $del_login = $connection->createCommand("UPDATE count_cluster SET `start_date`='$start_date',`end_date`='$end_date',jumlah_cluster='$model->jumlah_cluster' WHERE `login`='$user->id' AND kmeans_type='teknisi'")->execute();
        }else{
          $model->start_date = $start_date;
          $model->end_date = $end_date;
          $model->login = $user->id;
          $model->kmeans_type = 'teknisi';
          $model->save();
        }
                    
        $del_old_list_centroid = $connection->createCommand("DELETE FROM list_centroid WHERE `login`='$user->id' AND kmeans_type='teknisi'")->execute();
        $del_old_centroid = $connection->createCommand("DELETE FROM centroid WHERE `login`='$user->id' AND kmeans_type='teknisi'")->execute();
        $del_old = $connection->createCommand("DELETE FROM count_symptom WHERE login='$user->id' AND kmeans_type='teknisi'")->execute();
        // $del_count_symptom = $connection->createCommand("DELETE FROM count_symptom WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();

        $cek_data = $connection->createCommand("SELECT * FROM cases WHERE date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')")->queryAll();
        $ck_cluster = $connection->createCommand("SELECT 
          a.*,b.*,
          SUM(IF(a.regional='1' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_1,
          SUM(IF(a.regional='2' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_2,
          SUM(IF(a.regional='3' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_3,
          SUM(IF(a.regional='4' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_4,
          SUM(IF(a.regional='5' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_5,
          SUM(IF(a.regional='6' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_6,
          SUM(IF(a.regional='7' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_7
          FROM cases AS a 
          INNER JOIN symptom AS b ON b.id=a.symptomp
          GROUP BY a.symptomp
          ORDER BY a.symptomp ASC")->queryAll();

        if($cek_data){
          foreach($ck_cluster as $row){
            $model2 = new CountSymptom(); //Deklarasi model database count_symptom
            $model2->symptom = $row['symptomp'];
            $model2->kmeans_type = 'teknisi';
            $model2->reg1 = $row['r_1'];
            $model2->reg2 = $row['r_2'];
            $model2->reg3 = $row['r_3'];
            $model2->reg4 = $row['r_4'];
            $model2->reg5 = $row['r_5'];
            $model2->reg6 = $row['r_6'];
            $model2->reg7 = $row['r_7'];
            $model2->login = $user->id;
            $model2->save();
          }
        }else{
          $del_login = $connection->createCommand("DELETE FROM count_cluster WHERE `login`='$user->id' AND kmeans_type='teknisi'")->execute();
        }
      }

      $searchModel = new CountsymptomSearch();
      $dataProvider = $searchModel->searchTeknisi(Yii::$app->request->queryParams);

      $title = 'Teknisi';
      return $this->render('cases-sympthomp', [
        'user' => $user,
        'title' => $title,
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
      ]);
    }

    public function actionViewChart($t)
    {
      $connection = \Yii::$app->db;
      
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);

      return $this->renderAjax('view-chart',[
        't' => $t,
        'user' => $user,
      ]);
    }
    public function actionCeknextCluster()
    {
      $connection = \Yii::$app->db;
      
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);

      $title = $_POST['title'];
      if($_POST['cluster'] != '100'){
        $c_symptom = $connection->createCommand("SELECT * FROM count_symptom WHERE login='$user->id' AND kmeans_type='$title' order by `id` ASC")->queryAll();
        $max_iterasi = $connection->createCommand("SELECT max(iterasi) FROM centroid WHERE login='$user->id' AND kmeans_type='$title'")->queryScalar();
        $centroid = $connection->createCommand("SELECT * FROM centroid WHERE login='$user->id' AND iterasi='$max_iterasi' AND kmeans_type='$title' order by cluster ASC")->queryAll();

        $cek_list_centroid = $connection->createCommand("SELECT * FROM list_centroid WHERE iterasi='$max_iterasi' AND login='$user->id' AND kmeans_type='$title'")->queryAll(); //cek table list centroid jika iterasi sudah sama dengan di tabel centroid

        if($cek_list_centroid == NULL){
          foreach($c_symptom as $c => $row)
          {
            $model = new ListCentroid();
            $model->count_symptom = $row['id'];
            $model->iterasi = $max_iterasi;

            // $modelCentroid = new Centroid();
            foreach($centroid as $cd => $ctr){
              $c1 = NULL; $c2 = NULL; $c3 = NULL; $c4=NULL; $c5 = NULL;
              // print_r($row['reg1']."<br>");
              if($ctr['cluster'] == '1'){
                $c1 = round(sqrt(pow(($row['reg1']-$ctr['reg1']),2) + pow(($row['reg2']-$ctr['reg2']),2) + pow(($row['reg3']-$ctr['reg3']),2) + pow(($row['reg4']-$ctr['reg4']),2) + pow(($row['reg5']-$ctr['reg5']),2) + pow(($row['reg6']-$ctr['reg6']),2) + pow(($row['reg7']-$ctr['reg7']),2)),2);
                $model->c1 = $c1;
                $c1 = NULL;
              }
              
              if($ctr['cluster'] == '2'){
                $c2 = round(sqrt(pow(($row['reg1']-$ctr['reg1']),2) + pow(($row['reg2']-$ctr['reg2']),2) + pow(($row['reg3']-$ctr['reg3']),2) + pow(($row['reg4']-$ctr['reg4']),2) + pow(($row['reg5']-$ctr['reg5']),2) + pow(($row['reg6']-$ctr['reg6']),2) + pow(($row['reg7']-$ctr['reg7']),2)),2);
                $model->c2 = $c2;
                $c2 = NULL;
              }
              
              if($ctr['cluster'] == '3'){
                $c3 = round(sqrt(pow(($row['reg1']-$ctr['reg1']),2) + pow(($row['reg2']-$ctr['reg2']),2) + pow(($row['reg3']-$ctr['reg3']),2) + pow(($row['reg4']-$ctr['reg4']),2) + pow(($row['reg5']-$ctr['reg5']),2) + pow(($row['reg6']-$ctr['reg6']),2) + pow(($row['reg7']-$ctr['reg7']),2)),2);
                $model->c3 = $c3;
                $c3 = NULL;
              }
              
              if($ctr['cluster'] == '4'){
                $c4 = round(sqrt(pow(($row['reg1']-$ctr['reg1']),2) + pow(($row['reg2']-$ctr['reg2']),2) + pow(($row['reg3']-$ctr['reg3']),2) + pow(($row['reg4']-$ctr['reg4']),2) + pow(($row['reg5']-$ctr['reg5']),2) + pow(($row['reg6']-$ctr['reg6']),2) + pow(($row['reg7']-$ctr['reg7']),2)),2);
                $model->c4 = $c4;
                $c4 = NULL;
              }
              
              if($ctr['cluster'] == '5'){
                $c5 = round(sqrt(pow(($row['reg1']-$ctr['reg1']),2) + pow(($row['reg2']-$ctr['reg2']),2) + pow(($row['reg3']-$ctr['reg3']),2) + pow(($row['reg4']-$ctr['reg4']),2) + pow(($row['reg5']-$ctr['reg5']),2) + pow(($row['reg6']-$ctr['reg6']),2) + pow(($row['reg7']-$ctr['reg7']),2)),2);
                $model->c5 = $c5;
                $c5 = NULL;
              }
              
            }//end foreach

            // print_r($model->c1." | ".$model->c2."|<br>");

            $val = array_diff(array($model->c1, $model->c2, $model->c3, $model->c4, $model->c5),array(null));
            sort($val);
            
            $min_val = min($val);
            $val_cluster = NULL;
            if($min_val == $model->c1){
              $val_cluster = '1';
            }elseif($min_val == $model->c2){
              $val_cluster = '2';  
            }elseif($min_val == $model->c3){
              $val_cluster = '3';  
            }elseif($min_val == $model->c4){  
              $val_cluster = '4';  
            }elseif($min_val == $model->c5){
              $val_cluster = '5';  
            }
            $model->login = $user->id;
            $model->kmeans_type = $title;
            $model->cluster = $val_cluster;
            $model->save(false);            
          }// end foreach simpan data list cendroid

          $max_cluster = $connection->createCommand("SELECT max(jumlah_cluster) FROM count_cluster WHERE login='$user->id' AND kmeans_type='$title'")->queryScalar();
          for($i=1 ;$i <= $max_cluster; $i++){
            $modelCentroid = new Centroid();
            $avg_list_centroid = $connection->createCommand("SELECT AVG(b.reg1) as reg1, AVG(b.reg2) as reg2, AVG(b.reg3) as reg3, AVG(b.reg4) as reg4, AVG(b.reg5) as reg5, AVG(b.reg6) as reg6, AVG(b.reg7) as reg7, a.iterasi, a.count_symptom as countSymptom , b.symptom as simptom
            FROM list_centroid as a 
            INNER JOIN count_symptom as b ON a.count_symptom=b.id
            -- LEFT JOIN centroid as c ON b.symptom = c.id_count_symptom
            WHERE a.cluster='$i' AND a.login='$user->id' AND a.iterasi='$max_iterasi' AND a.kmeans_type='$title'")->queryOne();

            $my_simptom = $connection->createCommand("SELECT a.*,b.*,c.* FROM list_centroid as a 
            INNER JOIN count_symptom AS b ON a.count_symptom=b.id
            INNER JOIN centroid AS c ON b.symptom=c.id_count_symptom
            WHERE a.cluster='$i' AND c.iterasi='$max_iterasi' AND c.login='$user->id' AND a.kmeans_type='$title'")->queryOne();

            $modelCentroid->login=$user->id;
            $modelCentroid->kmeans_type=$title;
            $modelCentroid->reg1 = $avg_list_centroid['reg1'];
            $modelCentroid->reg2 = $avg_list_centroid['reg2'];
            $modelCentroid->reg3 = $avg_list_centroid['reg3'];
            $modelCentroid->reg4 = $avg_list_centroid['reg4'];
            $modelCentroid->reg5 = $avg_list_centroid['reg5'];
            $modelCentroid->reg6 = $avg_list_centroid['reg6'];
            $modelCentroid->reg7 = $avg_list_centroid['reg7'];
            $modelCentroid->iterasi = $max_iterasi+1;
            $modelCentroid->cluster = $i;
            $modelCentroid->id_count_symptom = $my_simptom['id_count_symptom'];
            $modelCentroid->save(false);
          }
        }// end if cek data list centroid
      }
    }
    public function actionAnalisisCluster($title)
    {
      $connection = \Yii::$app->db;
      
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);

      $list_centroid = $connection->createCommand("SELECT * FROM centroid WHERE login='$user->id' AND kmeans_type='$title' GROUP BY iterasi ORDER BY iterasi DESC")->queryAll();
      if($list_centroid == NULL):
        Yii::$app->session->setFlash('error', "No data found");
        return $this->redirect(['cases-symthomp']);
      endif;
      $searchModel = new CentroidSearch();
      $dataProvider = $searchModel->searchMax(Yii::$app->request->queryParams,$user->id,$title);

      return $this->render('analisis-cluster',[
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'title' => $title,
        'user' => $user,
        'list_centroid' => $list_centroid
      ]);
    }

    public function actionCekIterasi($title) //controller untuk mengambil nilai dari cluster yang sudah di select dan disimpan di database centroid
    {
      $connection = \Yii::$app->db;
      
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);

      $keys = $_POST['keylist'];
      // $iterasi = '1';
      $cluster = '1';
      if($keys){
        $delOld = $connection->createCommand("DELETE from centroid WHERE login='$user->id' AND kmeans_type='$title'")->execute();
        $delListCentroid = $connection->createCommand("DELETE FROM list_centroid WHERE login='$user->id' AND kmeans_type='$title'")->execute();
        foreach($keys as $k => $key){
            $data = $connection->createCommand("SELECT * FROM count_symptom WHERE id='$key' AND kmeans_type='$title'")->queryOne();
            // print_r($key);
            $model = new Centroid();
            $model->login = $user->id;
            $model->kmeans_type = $data['kmeans_type'];
            $model->id_count_symptom = $data['symptom'];
            $model->reg1 = $data['reg1'];
            $model->reg2 = $data['reg2'];
            $model->reg3 = $data['reg3'];
            $model->reg4 = $data['reg4'];
            $model->reg5 = $data['reg5'];
            $model->reg6 = $data['reg6'];
            $model->reg7 = $data['reg7'];
            $model->iterasi = '1';
            $model->cluster = $cluster; 
            $model->save();
            $iterasi++;
            $cluster++;
        }
        return $this->redirect(['analisis-cluster','title'=>$title]);
      }

      // return $this->render('form-cluster',[
      //     'model' => $model
      // ]);
    }
    public function actionClearCluster($title)
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);
      $del_count_cluster = $connection->createCommand("DELETE FROM count_cluster WHERE `login`='$user->id' AND kmeans_type='$title'")->execute();
      $del_centroid = $connection->createCommand("DELETE FROM centroid WHERE login='$user->id' AND kmeans_type='$title'")->execute();
      $del_list_centroid = $connection->createCommand("DELETE FROM list_centroid WHERE login='$user->id' AND kmeans_type='$title'")->execute();
      $del_count_symptom = $connection->createCommand("DELETE FROM count_symptom WHERE `login`='$user->id' AND kmeans_type='$title'")->execute();
    }

    public function actionCasesSymthomp()
    {
        $connection = \Yii::$app->db;
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->goHome();
        }

        $user = User::findOne(Yii::$app->user->id);

        $model = new CountCluster();

        if ($model->load(Yii::$app->request->post())){
          $start_date = $_POST['CountCluster']['start_date'];
          $end_date = $_POST['CountCluster']['end_date'];

          $cek_if_login_available = $connection->createCommand("SELECT * FROM count_cluster WHERE login='$user->id' AND kmeans_type='symtomp'")->queryAll();
          if($cek_if_login_available){
            $del_login = $connection->createCommand("UPDATE count_cluster SET `start_date`='$start_date',`end_date`='$end_date',jumlah_cluster='$model->jumlah_cluster' WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();
          }else{
            $model->start_date = $start_date;
            $model->end_date = $end_date;
            $model->login = $user->id;
            $model->kmeans_type = 'symtomp';
            $model->save();
          }
                      
          $del_old_list_centroid = $connection->createCommand("DELETE FROM list_centroid WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();
          $del_old_centroid = $connection->createCommand("DELETE FROM centroid WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();
          $del_old = $connection->createCommand("DELETE FROM count_symptom WHERE login='$user->id' AND kmeans_type='symtomp'")->execute();
          // $del_count_symptom = $connection->createCommand("DELETE FROM count_symptom WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();

          $cek_data = $connection->createCommand("SELECT * FROM cases WHERE date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')")->queryAll();
          $ck_cluster = $connection->createCommand("SELECT 
            a.*,b.*,
            SUM(IF(a.regional='1' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_1,
            SUM(IF(a.regional='2' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_2,
            SUM(IF(a.regional='3' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_3,
            SUM(IF(a.regional='4' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_4,
            SUM(IF(a.regional='5' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_5,
            SUM(IF(a.regional='6' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_6,
            SUM(IF(a.regional='7' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_7
            FROM cases AS a 
            INNER JOIN symptom AS b ON b.id=a.symptomp
            GROUP BY a.symptomp
            ORDER BY a.symptomp ASC")->queryAll();

          if($cek_data){
            foreach($ck_cluster as $row){
              $model2 = new CountSymptom(); //Deklarasi model database count_symptom
              $model2->symptom = $row['symptomp'];
              $model2->kmeans_type = 'symtomp';
              $model2->reg1 = $row['r_1'];
              $model2->reg2 = $row['r_2'];
              $model2->reg3 = $row['r_3'];
              $model2->reg4 = $row['r_4'];
              $model2->reg5 = $row['r_5'];
              $model2->reg6 = $row['r_6'];
              $model2->reg7 = $row['r_7'];
              $model2->login = $user->id;
              $model2->save();
            }
          }else{
            $del_login = $connection->createCommand("DELETE FROM count_cluster WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();
          }
        }

        $searchModel = new CountsymptomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $title = 'Simtom';
        return $this->render('cases-sympthomp', [
          'user' => $user,
          'title' => $title,
          'model' => $model,
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex()
    {
        $connection = \Yii::$app->db;
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->goHome();
        }
        $user = User::findOne(Yii::$app->user->id);
        $model = new ImportFormCase();
        $searchModel = new CasesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if($model->load(Yii::$app->request->post())):
            $uploadedFile = \yii\web\UploadedFile::getInstance($model,'file_case');
            $extension =$uploadedFile->extension;
            if($extension=='xlsx'){
                $inputFileType = 'Xlsx';
            }else{
                $inputFileType = 'Xls';
            }
            $sheetname = "Sheet1";
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            
            $reader->setLoadSheetsOnly($sheetname);
            $spreadsheet = $reader->load($uploadedFile->tempName);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            for ($row = 2; $row <= $highestRow; $row++) { //$row = 2 artinya baris kedua yang dibaca dulu(header kolom diskip disesuaikan saja)
                //for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $date_open = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); // Mengambil nilai kolom date_open
                $tiket = $worksheet->getCellByColumnAndRow(2, $row)->getValue(); // Mengambil nilai kolom trouble_ticket
                $symptomp = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); // Mengambil nilai kolom symptomp
                $segment = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); // Mengambil nilai kolom segment
                $ncli = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); // Mengambil nilai kolom ncli
                $inet = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); // Mengambil nilai kolom internet_number
                $pstn = $worksheet->getCellByColumnAndRow(7, $row)->getValue(); // Mengambil nilai kolom pstn
                $regional = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); // Mengambil nilai kolom regional
                $witel = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); // Mengambil nilai kolom witel
                $datel = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); // Mengambil nilai kolom datel
                $speed = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); // Mengambil nilai kolom speed 
                $workzone = $worksheet->getCellByColumnAndRow(12, $row)->getValue(); // Mengambil nilai kolom workzone amvrew
                $amcrew = $worksheet->getCellByColumnAndRow(13, $row)->getValue(); // Mengambil nilai kolom amcrew
                $paket = $worksheet->getCellByColumnAndRow(14, $row)->getValue(); // Mengambil nilai kolom packet
                $status = $worksheet->getCellByColumnAndRow(15, $row)->getValue(); // Mengambil nilai kolom status
                $date_closed = $worksheet->getCellByColumnAndRow(16, $row)->getValue(); // Mengambil nilai kolom date_closed
                $login = $user->id; // Mengambil nilai login user $user
                // print_r($tiket);
                $cek_data = $connection->createCommand("SELECT * FROM cases WHERE trouble_ticket='$tiket'")->queryAll();
                // $cek_data_null = $connection->createCommand("SELECT COUNT(*) FROM cases WHERE id IS NULL")->queryAll();
                // print_r('test');
                if(!$cek_data){
                    // print_r('test');
                    $cek_symtomp = $connection->createCommand("SELECT * FROM symptom WHERE symptom LIKE '%$symptomp%'")->queryOne();
                    $cek_segment = $connection->createCommand("SELECT * FROM segment WHERE segment LIKE '%$segment%'")->queryOne();
                    $cek_witel = $connection->createCommand("SELECT * FROM witel WHERE nama_witel LIKE '%$witel%'")->queryOne();
                    $cek_reg = $connection->createCommand("SELECT * FROM regional WHERE regional LIKE '%$regional%'")->queryOne();

                    if($cek_segment['id'] == NULL){
                      $cek_segment['id'] = '';
                    }
                    if($cek_symtomp['id'] == NULL){
                        $cek_symtomp['id'] = '';
                    }
                    if($cek_witel['id'] == NULL){
                        $cek_witel['id'] = '';
                    }
                    if($cek_reg['id'] == NULL){
                        $cek_reg['id'] = '';
                    }

                    // print_r($cek_reg['id']."-".$cek_symtomp['id']."<br>");
                    $insert_cases = $connection->createCommand("INSERT INTO cases (`date_open`,`trouble_ticket`,`symptomp`,`segment`,`ncli`,`internet_number`,`pstn`,`regional`,`witel`,`datel`,`speed`,`workzone_amcrew`,`amcrew`,`packet`,`status`,`date_closed`,`login`) VALUES ('$date_open','$tiket','$cek_symtomp[id]','$cek_segment[id]','$ncli','$inet','$pstn','$cek_reg[id]','$cek_witel[id]','$datel','$speed','$workzone','$amcrew','$paket','$status','$date_closed','$login')")->execute();
                }
                //naa disini baru isi dengan model ->save()
            }
            // die();
        endif;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }


    public function actionLogin() //Menampilkan halaman login
    {
        $this->layout="login-main";
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
          $level = Yii::$app->user->identity->level;
          if($level == "manager"):
            return $this->redirect(['/manager/index']);
          endif;
            return $this->redirect(['index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
