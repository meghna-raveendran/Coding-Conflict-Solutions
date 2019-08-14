<?php
ob_flush();
session_start();
define('ADMIN_SESSION', 'admin_userlog');
//DB details
$dbHost = 'localhost';
$dbUsername = 'user';
$dbPassword = 'sdf!@#';
$dbName = 'db';

//Create connection and select DB
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Unable to connect database: " . $db->connect_error);
}
if(!isset($_SESSION[ADMIN_SESSION]))
{
    header('Location:import_login.php');
} 

if(isset($_POST['importSubmit'])){
    $countC=0;
    $sellerArr=explode('-',$_POST['seller_id']);
    $seller_id=$sellerArr[1];
    $sellerName=$sellerArr[0];
    if (!file_exists($DOCUMENT_ROOT.'catalog/'.$sellerName)) {
        mkdir($DOCUMENT_ROOT.'catalog/'.$sellerName, 0777, true);
    }
    //validate whether uploaded file is a csv file
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            $DOCUMENT_ROOT=$_SERVER['DOCUMENT_ROOT'].'/image/';
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            //skip first line
            fgetcsv($csvFile);
            //parse data from csv file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE)
            {
                $product_code=$line[0];
                if($product_code!='' && $product_code!==0)
                {
                    $product_model=$sellerName.'-'.$product_code;
                    $name=$line[1];
                    $image_title=str_replace(" ","-",$name).rand(1000,10000);
                    $image_title=str_replace([':', '\\', '/', '*',"'",'"',"&"], '', $image_title);
                    $category=$line[2];
                    $cat_arr=explode('>', $line[3]);
                    $price=$line[5];
                    $weight=$line[6];
                    $description=$line[7];
                    $color_arr=explode('>', $line[8]);
                    $size_arr=explode('>', $line[9]);
                    $date=date('Y-m-d');
                    $pic0='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-1.jpg'; 
                    
                    if($line[21]!=''){
                        $manufacturer=$line[21];
                        $reso_manu = mysqli_query($db,"SELECT manufacturer_id FROM  oc_manufacturer WHERE name = '$manufacturer' ");
                        $ro_manufacturer = mysqli_fetch_assoc($reso_manu);
                        $manufacturer_id=$ro_manufacturer['manufacturer_id'];
                    }
                    else 
                        $manufacturer_id=0;
                    if($line[22]!='')
                        $quantity=$line[22];
                    else 
                        $quantity=1;
                    //check whether product already exists in database with same product_code
                    $prevQuery = "SELECT product_id FROM oc_product WHERE product_code = '".$product_code."'";
                    $prevResult = $db->query($prevQuery);
                    if($prevResult->num_rows > 0){
                        //update product data
                        $db->query("UPDATE oc_product SET price = '".$price."', weight = '".$weight."',image='".$pic0."',manufacturer_id='".$manufacturer_id."' WHERE product_code = '".$product_code."'");  
                    }else{
                        //insert product data into database
                        $db->query("INSERT INTO oc_product (product_code, model,image,price,manufacturer_id, weight,quantity,stock_status_id,date_available,weight_class_id,length_class_id,sort_order,status,date_added) VALUES ('".$product_code."','".$product_model."','".$pic0."','".$price."','".$manufacturer_id."','".$weight."','".$quantity."',6,'".$date."',1,1,1,1,'".$date."')");  
                    }
                    
                    //get product id from database with product_code
                    $prevQuery1 = "SELECT product_id FROM oc_product WHERE product_code = '".$product_code."'";
                    $prevResult1 = mysqli_query($db, $prevQuery1);
                    if($prevResult1->num_rows > 0){
                    $row = mysqli_fetch_assoc($prevResult1);
                    $product_id=$row['product_id'];}

                    //check whether product_description already exists in database with same product_id
                    $prevQuery2 = "SELECT oc_product_id FROM product_description WHERE product_id = '".$product_id."'";
                    $prevResult2 = $db->query($prevQuery2);
                    if($line[24]!='')
                        $meta_description=$line[24];
                    else
                        $meta_description='';
                    if($prevResult2->num_rows > 0){
                        //update product_description data
                        $db->query('UPDATE oc_product_description SET name = "'.$name.'", description = "'.$description.'", meta_description = "'.$meta_description.'" WHERE product_id = "'.$product_id.'"');  
                    }else{
                        //insert product_description data into database
                        $db->query('INSERT INTO oc_product_description (product_id,language_id, name, description, meta_title, meta_description) VALUES ("'.$product_id.'","1","'.$name.'","'.$description.'","'.$name.'","'.$meta_description.'")');
                        
                        $db->query("INSERT INTO oc_product_to_store (product_id, store_id) VALUES ('".$product_id."','0')");
                        $db->query("INSERT INTO oc_product_to_store (product_id, store_id) VALUES ('".$product_id."','2')");
                        $db->query("INSERT INTO oc_product_to_store (product_id, store_id) VALUES ('".$product_id."','3')");
                        $db->query("INSERT INTO oc_product_to_store (product_id, store_id) VALUES ('".$product_id."','4')");
                        $db->query("INSERT INTO oc_product_to_store (product_id, store_id) VALUES ('".$product_id."','6')");

                        $db->query("INSERT INTO oc_product_to_seller (product_id, seller_id) VALUES ('".$product_id."','".$seller_id."')");
                        
                        

                    }
                        
                    //check whether url alias exists in database with same product_id
                    $query1='product_id='.$product_id;
                    $prevQuery0 = "SELECT url_alias_id FROM oc_url_alias WHERE query = '".$query1."'";
                    $prevResult0 = $db->query($prevQuery0);
                    $keyw=str_replace(" ","-",$name);
                    $keyword = preg_replace('/[^A-Za-z0-9\-]/', '', $keyw).'-'.$product_model;
                    if($prevResult0->num_rows > 0){
                        //update url alias data
                        $db->query("UPDATE oc_url_alias SET keyword = '".$keyword."' WHERE query = '".$query1."'");  
                    }else{
                        //insert url alias data into database
                        $db->query("INSERT INTO oc_url_alias (query, keyword) VALUES ('".$query1."','".$keyword."')");  
                    }
                    
                    //delete from  product_to_category already exists in database with same product_id
                    $db->query("DELETE FROM oc_product_to_category WHERE product_id = '$product_id' AND category_id = '$category'");
                    //insert product_to_category data into database
                    $db->query("INSERT INTO oc_product_to_category (product_id, category_id) VALUES ('".$product_id."','$category')");
                    foreach ($cat_arr as $key => $value) {
                        //delete from  product_to_category already exists in database with same product_id
                        $db->query("DELETE FROM oc_product_to_category WHERE product_id = '$product_id' AND category_id = '$value'");

                        $db->query("INSERT INTO oc_product_to_category (product_id, category_id) VALUES ('".$product_id."','".$value."')");
                    }
                    if($line[23]!=''){
                        $special_price=$line[23];
                        //delete from  oc_product_special already exists in database with same product_id
                        $db->query("DELETE FROM oc_product_special WHERE product_id = '$product_id'");
                        //insert oc_product_special data into database
                        $db->query("INSERT INTO oc_product_special (product_id, customer_group_id,price) VALUES ('".$product_id."','1','".$special_price."')");
                    }
                    if($line[8]!='')  {  
                        $option_id=19; //id of option color
                        $db->query("DELETE FROM oc_product_option WHERE product_id = '$product_id' AND option_id = '$option_id'");
                        $db->query("INSERT INTO oc_product_option (product_id, option_id,required) VALUES ('$product_id','$option_id','1')");
                        $prevQuery2 = "SELECT product_option_id FROM oc_product_option order by product_option_id desc limit 1";
                        $prevResult2 = mysqli_query($db, $prevQuery2);
                        $row2 = mysqli_fetch_assoc($prevResult2);
                        $product_option_id=$row2['product_option_id'];
                        foreach ($color_arr as $key => $value1) {
                            $prc=explode('=', $value1);
                            if($prc[1]!=''){
                                $pric=$prc[1].'.000';
                                $opt_name=$prc[0];
                                $sqlo = "SELECT option_value_id FROM  oc_option_value_description WHERE name = '$opt_name' ";
                                $reso = mysqli_query($db, $sqlo);
                                $ro = mysqli_fetch_assoc($reso);
                                $option_value_id=$ro['option_value_id'];
                            }
                            else{
                                $pric='0.000';
                                $opt_name=$value1;
                                $sqlo = "SELECT option_value_id FROM  oc_option_value_description WHERE name = '$opt_name' ";
                                $reso = mysqli_query($db, $sqlo);
                                $ro = mysqli_fetch_assoc($reso);
                                $option_value_id=$ro['option_value_id'];
                            }
                            $db->query("DELETE FROM oc_product_option_value WHERE product_id = '$product_id' AND option_id = '$option_id' AND option_value_id = '$option_value_id'");
                            $db->query("INSERT INTO oc_product_option_value (product_option_id, product_id, option_id,option_value_id,quantity,subtract,price,price_prefix,points_prefix,weight_prefix) VALUES ('$product_option_id','$product_id','$option_id','$option_value_id','1','1','$pric','+','+','+')");
                        }
                    }

                    if($line[9]!='')  {  
                        $option_id=20; //id of option size
                        $db->query("DELETE FROM oc_product_option WHERE product_id = '$product_id' AND option_id = '$option_id'");
                        $db->query("INSERT INTO oc_product_option (product_id, option_id,required) VALUES ('$product_id','$option_id','1')");
                        $prevQuery2 = "SELECT product_option_id FROM oc_product_option order by product_option_id desc limit 1";
                        $prevResult2 = mysqli_query($db, $prevQuery2);
                        $row2 = mysqli_fetch_assoc($prevResult2);
                        $product_option_id=$row2['product_option_id'];
                        foreach ($size_arr as $key => $value2) {
                            $opt_name1=$value2;
                            $sqlo1 = "SELECT option_value_id FROM  oc_option_value_description WHERE name = '$opt_name1' ";
                            $reso1 = mysqli_query($db, $sqlo1);
                            $ro1 = mysqli_fetch_assoc($reso1);
                            $option_value_id1=$ro1['option_value_id'];
                            $db->query("DELETE FROM oc_product_option_value WHERE product_id = '$product_id' AND option_id = '$option_id' AND option_value_id = '$option_value_id1'");
                            $db->query("INSERT INTO oc_product_option_value (product_option_id, product_id, option_id,option_value_id,quantity,subtract,price_prefix,points_prefix,weight_prefix) VALUES ('$product_option_id','$product_id','$option_id','$option_value_id1','1','1','+','+','+')");
                        }
                    }

                    //delete from  product_image already exists in database with same product_id
                    $db->query("DELETE FROM oc_product_image WHERE product_id = '".$product_id."'");
                    if (!file_exists($DOCUMENT_ROOT.'catalog/'.$sellerName.'/'.$category)) {
                        mkdir($DOCUMENT_ROOT.'catalog/'.$sellerName.'/'.$category, 0777, true);
                    }
                    if($line[11]!='')  {     
                        $pic1='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-1.jpg';        
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic1."')");
                        $image1 = file_get_contents($line[11]);
                        if (!file_exists($DOCUMENT_ROOT.$pic1)) 
                            file_put_contents($DOCUMENT_ROOT.$pic1, $image1); //Where to save the image on your server
                    }
                    
                    if($line[12]!='')  {
                        $pic2='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-2.jpg';
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic2."')");
                        $image2 = file_get_contents($line[12]);
                        if (!file_exists($DOCUMENT_ROOT.$pic2)) 
                            file_put_contents($DOCUMENT_ROOT.$pic2, $image2); //Where to save the image on your server
                    }
                    if($line[13]!='')  {
                        $pic3='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-3.jpg';
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic3."')");
                        $image3 = file_get_contents($line[13]);
                        if (!file_exists($DOCUMENT_ROOT.$pic3)) 
                            file_put_contents($DOCUMENT_ROOT.$pic3, $image3); //Where to save the image on your server
                    }
                    if($line[14]!='')  {       
                        $pic4='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-4.jpg';       
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic4."')");
                        $image4 = file_get_contents($line[14]);
                        if (!file_exists($DOCUMENT_ROOT.$pic4)) 
                            file_put_contents($DOCUMENT_ROOT.$pic4, $image4); //Where to save the image on your server
                    }
                    if($line[15]!='')  { 
                        $pic5='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-5.jpg';         
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic5."')");
                        $image5 = file_get_contents($line[15]);
                        if (!file_exists($DOCUMENT_ROOT.$pic5)) 
                            file_put_contents($DOCUMENT_ROOT.$pic5, $image5); //Where to save the image on your server
                    }
                    if($line[16]!=''){
                        $pic6='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-6.jpg';
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic6."')");
                        $image6 = file_get_contents($line[16]);
                        if (!file_exists($DOCUMENT_ROOT.$pic6)) 
                            file_put_contents($DOCUMENT_ROOT.$pic6, $image6); //Where to save the image on your server
                    }
                    if($line[17]!=''){
                        $pic7='catalog/'.$sellerName.'/'.$category.'/'.$image_title.'-7.jpg';
                        $db->query("INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$pic7."')");
                        $image7 = file_get_contents($line[17]);
                        if (!file_exists($DOCUMENT_ROOT.$pic7)) 
                            file_put_contents($DOCUMENT_ROOT.$pic7, $image7); //Where to save the image on your server
                    }
                    $countC=$countC+1;
                }
                
            }
            
            //close opened csv file
            fclose($csvFile);
            
        }
        if($countC != 0){
            $qstring = 'succ';
        }
        else{
            $qstring = 'err';
        }
    }
    else{
        $qstring = 'invalid_file';
    }
}


if(!empty($qstring)){
    switch($qstring){
        case 'succ':
            $statusMsgClass = 'alert-success';
            $statusMsg = 'Product data has been inserted successfully.';
            break;
        case 'err':
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Some problem occurred, please try again.';
            break;
        case 'invalid_file':
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Please upload a valid CSV file.';
            break;
        default:
            $statusMsgClass = '';
            $statusMsg = '';
    }
}
?>
<title>test | Import</title>
<link href="http://www.test.com/image/catalog/favi.png" rel="icon" />
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="container">
    <?php if(!empty($statusMsg)){
        echo '<div class="alert '.$statusMsgClass.'">'.$statusMsg.'</div>';
    } ?>
    
    
    <div class="panel panel-default" style="margin-top:100px;">
        <div class="panel-heading"> 
            <span style="float:right">Welcome, <?php echo $_SESSION['loginUser'];?> | 
                <a href="import_login.php?userAction=logout">Logout</a>
            </span>
            Products list 
            <a href="javascript:void(0);" onclick="$('#importFrm').slideToggle();">Import Products</a>
        </div>
        <div class="panel-body">    
              <form action="" method="post" enctype="multipart/form-data" id="importFrm">
                <div class="row">
                 <div class="col-xs-4">
                  <select name="seller_id" class="form-control" required>
                    <option value="">Select</option>
                    <option value="test-2198">test Store</option>
                    <option value="tesr-2796">test1 Store</option>
                    
                  </select>
                 </div>
                 <div class="col-xs-4">
                   <input type="file" class="form-control" name="file" required/>
                 </div>
                 <div class="col-xs-4">
                  <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                 </div>
                </div>
                   
            </form>
           
            <table class="table table-bordered">
                <thead>
                    <tr>
                      <th>Id</th>
                      <th>Code</th>
                      <th>Price</th>
                      <th>Weight</th>
                      <th>Name</th>
                      <th>Images</th>
                    </tr>
                </thead>
                <tbody>
                <?php
    
    $query1 = $db->query("SELECT product_id FROM oc_product WHERE product_code!=''");
            $productsc=$query1->num_rows;
                $limit=10;
                $pages=ceil($productsc/$limit);
if(isset($_REQUEST["page"]) && $_REQUEST["page"]>1) {
    $page=$_REQUEST["page"];
    $start=($page-1)*$limit;
}
else {
        $start=0;
        $page=1;
}?>
                <?php
                
                    //get records from database
                    $query = $db->query("SELECT p.*,pd.* FROM oc_product p LEFT JOIN oc_product_description pd ON pd.product_id=p.product_id LEFT JOIN oc_product_to_seller
 ps ON ps.product_id=p.product_id WHERE p.product_code!='' ORDER BY p.product_id DESC LIMIT $start,$limit");
                    if($query->num_rows > 0){ 
                        while($row = $query->fetch_assoc()){ ?>
                    <tr>
                      <td><?php echo $row['product_id']; ?></td>
                      <td><?php echo $row['product_code']; ?></td>
                      <td><?php echo $row['price']; ?></td>
                      <td><?php echo $row['weight']; ?></td>
                      <td><?php echo $row['name']; ?></td>
                      <td><?php $query1 = $db->query("SELECT * FROM oc_product_image WHERE product_id='".$row['product_id']."' LIMIT 0,1");
                        while($row1 = $query1->fetch_assoc()){ ?>
                        <img src="image/<?php echo $row1['image']; ?>" style="width:50px">
                        <?php }?>
                      </td>
                    </tr>
                    <?php } }else{ ?>
                    <tr><td colspan="5">No product(s) found.....</td></tr>
                    <?php } ?>
                </tbody>
            </table>

            <ul class="pagination">
            <?php
                if(( @$page-1)>0){ 
                    if($page>5){?>
                    <li><a href="import.php?page=1" > First </a></l>
                <?php }?>
                <li><a href="import.php?page=<?php echo @$page-1;?>" aria-label="Prevoius"> <span aria-hidden="true">« Prev</span> </a></l>
            <?php }
            $j=0;
            if($page>3)
                $start=$page-2;
            else
                $start=1;
            for($i=$start;$i<=$pages;$i++){
                if($j==5) break;?>              
                <li <?php if($i==@$page){?> class="active" <?php }?>><a href="import.php?page=<?php echo $i;?>"><?php echo $i;?></a></l>
                <?php 
                $j++;
            }?>
            <?php if($page<$pages){?>
                <li><a href="import.php?page=<?php echo $page+1;?>" aria-label="Next"> <span aria-hidden="true">Next »</span> </a></l>
                <?php if($pages>5){?>
                    <li><a href="import.php?page=<?php echo $pages;?>"> Last - <?php echo $pages;?> </a></l>
                <?php }
            }?>
            <li><a><?php echo $productsc.' products';?></a></l>
        </ul>
        
        </div>
    </div>
</div>

