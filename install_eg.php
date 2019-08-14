<?php 

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\Package;
use common\models\PackageDetails;
use backend\models\BrandSearch;
use backend\models\Admin;
use common\models\Brand;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseFileHelper;

/**
 * BrandController implements the CRUD actions for User model.
 */
class BrandController extends Controller
{
   
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Brand();
        $model->scenario = "ajax";
        if ($model->load(Yii::$app->request->post()) && $user = $model->addBrand()) { 
            $this->actionCopySite($model->domain, $model->packageId, $user->attributes);  
            Yii::$app->session->setFlash('success', 'Brand has bee created successfully!');
            return $this->redirect(['index']);
        } else { 
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCopySite($site_name, $packageId, $attributes)
    {     
        $servername = str_replace("mysql:host=", "", explode(";", Yii::$app->db->dsn)[0]);
        $username = Yii::$app->db->username;
        $password = Yii::$app->db->password;

        // Create connection
        $conn = mysqli_connect($servername, $username, $password);
        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        // Create database
        $sql = "CREATE DATABASE if not exists ".$site_name;
        if (mysqli_query($conn, $sql)) {
            mysqli_select_db($conn, $site_name);

            $contents = file_get_contents("../../resto_demo/resto.sql");

            // Remove C style and inline comments
            $comment_patterns = array('/\/\*.*(\n)*.*(\*\/)?/', //C comments
                                      '/\s*--.*\n/', //inline comments start with --
                                      '/\s*#.*\n/', //inline comments start with #
                                      );
            $contents = preg_replace($comment_patterns, "\n", $contents);

            //Retrieve sql statements
            $statements = explode(";\n", $contents);
            $statements = preg_replace("/\s/", ' ', $statements);

            foreach ($statements as $query) {
                if (trim($query) != '') {
                        mysqli_query($conn, $query);
                    }
                }
            mysqli_query($conn, "UPDATE `settings` SET `value` = '$packageId' WHERE `settings`.`name` = 'packageId'");
            $password_hash = Admin::getPasswordhashkey($attributes['saved_password']);

            mysqli_query($conn, "INSERT INTO `admin` (`id`, `name`, `email`, `username`, `saved_password`, `password_hash`, `password_reset_token`, `role_id`, `parent_id`, `status`, `created_by`, `created_at`, `updated_at`, `last_login`) VALUES (NULL, '".$attributes['name']."', '".$attributes['email']."', '".$attributes['email']."', '".$attributes['saved_password']."', '".$password_hash."', NULL, '1', '', '1', '".Yii::$app->user->id."', '".time()."', '', '')");
            //Insert Package
            $package = Package::findOne($packageId); 
            mysqli_query($conn, "INSERT INTO `package` (`id`, `name`, `price`, `offer_price`, `status`) VALUES (".$package->id.", '".$package->name."', '".$package->price."', '".$package->offer_price."', '1')");
            foreach ($package->settings as $package_setting) {
                mysqli_query($conn, "INSERT INTO `package_settings` (`id`, `package_id`, `package_details_id`, `quantity`, `unit_price`) VALUES (".$package_setting->id.", '".$package_setting->package_id."', '".$package_setting->package_details_id."', '".$package_setting->quantity."', '".$package_setting->unit_price."')");              

                mysqli_query($conn, "INSERT INTO `brand_package_settings` (`id`, `package_details_id`, `value`, `usage`) VALUES (NULL, '".$package_setting->package_details_id."', '".$package_setting->quantity."', '0')");
            }
            $packageDetails = PackageDetails::find()->where(["status" => 1])->all();
            foreach ($packageDetails as $packageDetail) {
                mysqli_query($conn, "INSERT INTO `package_details` (`id`, `label`, `input_type`, `unit_price`, `status`) VALUES (".$packageDetail->id.", '".$packageDetail->label."', '".$packageDetail->input_type."', '".$packageDetail->unit_price."', '1')");               
            }

        } else {
            echo "Error creating database: " . mysqli_error($conn);
        }
        mysqli_close($conn);
        BaseFileHelper::createDirectory("../../../".$site_name, 0775, true);
        BaseFileHelper::copyDirectory("../../site_demo/", "../../../".$site_name);
        if(is_dir("../../../".$site_name)){
            $fname = "../../../".$site_name."/common/config/main-local.php";
            $fhandle = fopen($fname,"r");
            $content = fread($fhandle,filesize($fname));

            $content = str_replace("dbname=demo", "dbname=".$site_name, $content);

            $fhandle = fopen($fname,"w");
            fwrite($fhandle,$content);
            fclose($fhandle);
        }
        
    }

    public function actionCheckDomain()
    { 
        $post = Yii::$app->request->post();        
        if($post['domain'] != ""){
            return $this->makeDomain($post['domain']);
        }elseif ($post['company'] != "") {
            return $this->makeDomain($post['company']);
        }        
    }

    public function domainAvailability($domain)
    {
        if(User::find()->where(["domain" => $domain])->count() > 0)
            return true;
        else
            return false;
    }

    public function makeDomain($name)
    { 
        if($this->domainAvailability($name)){
            for($i=1; $i<11; $i++){
                if($this->domainAvailability($name.$i) == false){
                    return json_encode(["domain" => $name.$i, "success" => "Available"]);
                }
            }
            return json_encode(["error" => "Sorry, domain already in use.Please try some other name."]);
        }else{
            return json_encode(["success" => "Available", "domain" => $name]);
        } 
    }

   

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
