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
    public function actionSelectCluster()
    {
        $val = $_POST['check_list'];
        foreach($val as $v => $l){
            print_r($l."<br>");
        }

        // return $this->render('form-cluster',[
        //     'model' => $model
        // ]);
    }
    public function actionClearCluster()
    {
      $connection = \Yii::$app->db;
      if (Yii::$app->user->isGuest) {
          Yii::$app->user->logout();
          return $this->goHome();
      }
      $user = User::findOne(Yii::$app->user->id);
      $del_count_cluster = $connection->createCommand("DELETE FROM count_cluster WHERE `login`='$user->id'")->execute();
      $del_count_symptom = $connection->createCommand("DELETE FROM count_symptom WHERE `login`='$user->id'")->execute();
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

          $cek_if_login_available = $connection->createCommand("SELECT * FROM count_cluster WHERE login='$user->id'")->queryAll();
          if($cek_if_login_available){
            $del_login = $connection->createCommand("UPDATE count_cluster SET `start_date`='$start_date',`end_date`='$end_date',jumlah_cluster='$model->jumlah_cluster' WHERE `login`='$user->id'")->execute();
          }else{
            $model->start_date = $start_date;
            $model->end_date = $end_date;
            $model->login = $user->id;
            $model->save();
          }
                      
          $del_old = $connection->createCommand("DELETE FROM count_symptom WHERE `login`='$user->id' AND kmeans_type='symtomp'")->execute();
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
            $del_login = $connection->createCommand("DELETE FROM count_cluster WHERE `login`='$user->id'")->execute();
          }
        }

        $searchModel = new CountsymptomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('cases-sympthomp', [
          'user' => $user,
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
