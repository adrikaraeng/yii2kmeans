<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
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
          SUM(IF(a.regional='1' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_1,
          SUM(IF(a.regional='2' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_2,
          SUM(IF(a.regional='3' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_3,
          SUM(IF(a.regional='4' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_4,
          SUM(IF(a.regional='5' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_5,
          SUM(IF(a.regional='6' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_6,
          SUM(IF(a.regional='7' AND a.amcrew <> '' AND (a.date_open BETWEEN '$start_date' AND LAST_DAY('$end_date')) ,1,0)) AS r_7
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
                $ncli = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); // Mengambil nilai kolom ncli
                $inet = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); // Mengambil nilai kolom internet_number
                $pstn = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); // Mengambil nilai kolom pstn
                $regional = $worksheet->getCellByColumnAndRow(7, $row)->getValue(); // Mengambil nilai kolom regional
                $witel = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); // Mengambil nilai kolom witel
                $datel = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); // Mengambil nilai kolom datel
                $speed = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); // Mengambil nilai kolom speed 
                $workzone = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); // Mengambil nilai kolom workzone amvrew
                $amcrew = $worksheet->getCellByColumnAndRow(12, $row)->getValue(); // Mengambil nilai kolom amcrew
                $paket = $worksheet->getCellByColumnAndRow(13, $row)->getValue(); // Mengambil nilai kolom packet
                $status = $worksheet->getCellByColumnAndRow(14, $row)->getValue(); // Mengambil nilai kolom status
                $date_closed = $worksheet->getCellByColumnAndRow(15, $row)->getValue(); // Mengambil nilai kolom date_closed
                $login = $user->id; // Mengambil nilai login user $user
                // print_r($tiket);
                $cek_data = $connection->createCommand("SELECT * FROM cases WHERE trouble_ticket='$tiket'")->queryAll();
                // $cek_data_null = $connection->createCommand("SELECT COUNT(*) FROM cases WHERE id IS NULL")->queryAll();
                // print_r('test');
                if(!$cek_data){
                    print_r('test');
                    $cek_symtomp = $connection->createCommand("SELECT * FROM symptom WHERE symptom LIKE '%$symptomp%'")->queryOne();
                    $cek_witel = $connection->createCommand("SELECT * FROM witel WHERE nama_witel LIKE '%$witel%'")->queryOne();
                    $cek_reg = $connection->createCommand("SELECT * FROM regional WHERE regional LIKE '%$regional%'")->queryOne();

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
                    $insert_cases = $connection->createCommand("INSERT INTO cases (`date_open`,`trouble_ticket`,`symptomp`,`ncli`,`internet_number`,`pstn`,`regional`,`witel`,`datel`,`speed`,`workzone_amcrew`,`amcrew`,`packet`,`status`,`date_closed`,`login`) VALUES ('$date_open','$tiket','$cek_symtomp[id]','$ncli','$inet','$pstn','$cek_reg[id]','$cek_witel[id]','$datel','$speed','$workzone','$amcrew','$paket','$status','$date_closed','$login')")->execute();
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
