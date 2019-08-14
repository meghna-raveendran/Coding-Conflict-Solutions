<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<style>
    .hide {
    display: none!important;
    }
    #generate{
    cursor: pointer;
    }
    
</style>
<?php 


$write_stat = false;
$category_id = "";
$new_file = "";
if(isset($_POST['generate'])){
    $category_id =  $_POST['category_id'];
    $collection = isset($_POST['level1'])?$_POST['level1']:'';
    $type = isset($_POST['level2'])?$_POST['level2']:'';
    $tag = isset($_POST['level3'])?$_POST['level3']:'';
}
$servername = "localhost";
$username = "serv";
$password = "sd!@#";
$dbname = "db";
ini_set("display_errors", "1");
error_reporting(E_ALL);
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if($category_id){
    
$dir = __DIR__ .'/csvs/';
foreach(glob($dir.'*.*') as $v){
    unlink($v);
}
    
file_put_contents(__DIR__ ."/file.csv","");

$res = $conn->query("SELECT p.model, p.price as actualprice, p.image, p.quantity, pd.description, pd.meta_title, pd.meta_description, concat(cu.firstname, cu.lastname) as sellername, p.product_id, IF((p.upc = '') , p.product_id, p.upc) as ucode, pd.name, 
        (SELECT AVG(rating) AS total FROM oc_review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, 
        (SELECT price FROM oc_product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '1' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, 
        (SELECT price FROM oc_product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '1' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special 
        FROM oc_product_to_category p2c 
        LEFT JOIN oc_product p ON (p2c.product_id = p.product_id) 
        LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id) 
        LEFT JOIN oc_product_to_store p2s ON (p.product_id = p2s.product_id) 
        LEFT JOIN oc_product_to_seller s on s.product_id = p2c.product_id
        LEFT JOIN oc_customer cu on cu.customer_id = s.seller_id
        WHERE pd.language_id = '1' AND p.status = '1' AND p.date_available <= NOW() 
        AND p2s.store_id = '0' AND p2c.category_id = '$category_id' 
        GROUP BY ucode ORDER BY p.sort_order ASC, LCASE(pd.name) ASC");

$all_handle = [];

$fin[] = ['Handle', 'Title', 'Body (HTML)', 'Vendor', 'Type', 'Tags', 'Published', 'Option1 Name', 'Option1 Value', 'Variant SKU', 'Variant Inventory Tracker', 'Variant Inventory Qty', 'Variant Inventory Policy', 'Variant Fulfillment Service', 'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode', 
'Image Src', 'Image Position', 'Image Alt Text', 'Gift Card', 'SEO Title', 'SEO Description', 'Variant Image', 'Variant Weight Unit', 'Cost per item', 'Collection'];

while($row = $res->fetch_assoc()){ 

    $handle = $row['model'];
    $model_ary = explode("-", $row['model']);
    $row['description'] = html_entity_decode($row['description']);
    $counter = 0;
    $prod_imgs = $conn->query("select image from oc_product_image where product_id = ".$row['product_id']);
    $sizeOption = getOption($row['product_id'], $conn); //print_r($sizeOption);exit;
    
    $price = ($row['discount'] > $row['actualprice'])?$row['discount']:$row['actualprice'];
    $discount = ($row['discount'] > $row['actualprice'])?$row['actualprice']:''; 
    
    if(count($sizeOption)){ 
        foreach ($sizeOption as $options){
            if($options['option_id'] == 11){
                foreach($options['product_option_value'] as $optval){
                    $expression = $price.$optval['price_prefix'].$optval['price'];
                    eval( '$newPrice = (' . $expression . ');' );
                    if($counter == 0){
                            $fin[] =[$handle, $row['name'], $row['description'], $row['sellername'], $type, $tag, 'TRUE', 'Size', $optval['name'], '', 'shopify', $row['quantity'], 'deny', 'manual', $newPrice, $discount, 'TRUE', 'FALSE', '', 
       ('http://site.com/image/'.$row['image']), 1, '', 'FALSE', $row['meta_title'], $row['meta_description'], ('http://site/image/'.$row['image']), 'kg', $row['actualprice'], $collection  ];
                            
                            if($prod_imgs->num_rows){
                               while($img_row = $prod_imgs->fetch_assoc()){
                                   $fin[] = [$handle, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
       ('http://site.com/image/'.$img_row['image']), '', '', '', '', '', '', '', '', ''  ];
                               }
                            }
                            
                           $all_handle[] = $handle;
                       
                    }else{
                       $fin[] = [$handle, '', '', '', $type, $tag, '', 'Size', $optval['name'], '', 'shopify', $optval['quantity'], 'deny', 'manual', $newPrice, $discount, 'TRUE', 'FALSE', '', 
           ('http://site.com/image/'.$row['image']), '', '', 'FALSE', '', '', ('http://site.com/image/'.$row['image']), '', '', ''  ]; 
                    }
                    $counter++;
                }
            }
        }
    }
    
    if($counter == 0){
       
           $fin[] =[$handle, $row['name'], $row['description'], $row['sellername'], $type, $tag, 'TRUE', '', '', '', 'shopify', $row['quantity'], 'deny', 'manual', $price, $discount, 'TRUE', 'FALSE', '', 
           ('http://site.com/image/'.$row['image']), 1, '', 'FALSE', $row['meta_title'], $row['meta_description'], ('http://site.com/image/'.$row['image']), 'kg', $row['actualprice'], $collection  ];
           
           $prod_imgs = $conn->query("select image from oc_product_image where product_id = ".$row['product_id']);
           
           if($prod_imgs->num_rows){
               
               while($img_row = $prod_imgs->fetch_assoc()){
                   $fin[] = [$handle, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
       ('http://site.com/image/'.$img_row['image']), '', '', '', '', '', '', '', '', ''  ];
                   
               }
           }
            $all_handle[] = $handle;
       
    }
}


$fp = fopen(__DIR__ .'/file.csv', 'w');

foreach ($fin as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

$new_file = str_replace("/", "Or", $tag).".csv";
copy(__DIR__ .'/file.csv', __DIR__ ."/csvs/".$new_file);

$write_stat = true;

}

function getOption($product_id, $conn){
    $product_options = [];
    $product_option_data = array();

	$product_option_query = $conn->query("SELECT * FROM oc_product_option po LEFT JOIN `oc_option` o ON (po.option_id = o.option_id) LEFT JOIN oc_option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '1' ORDER BY o.sort_order");
    
    while($prow = $product_option_query->fetch_assoc()){
        $product_options[] = $prow;
    }
    
    foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $conn->query("SELECT * FROM oc_product_option_value pov LEFT JOIN oc_option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN oc_option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '1' ORDER BY ov.sort_order");

			while($pvrow = $product_option_value_query->fetch_assoc()){
                $product_option_values[] = $pvrow;
            }
			
			foreach ($product_option_values as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}
		return $product_option_data;
}

$file = 'http://site.com/csvs/'.$new_file;

if(isset($_POST['parent_id'])){
    
    $level = isset($_POST['level'])?$_POST['level']:'';
    
    $newlevel = ($level == "level1")?"level2":'';
    
    $elemId = ($level == 'level2')?'last_cat':'cat-level-';
    
    $cat_query = $conn->query("SELECT c.category_id, c.parent_id, d.name FROM `oc_category` c left join oc_category_description d on d.category_id = c.category_id WHERE c.`parent_id` = ".trim($_POST['parent_id']));
    
    echo (($elemId != 'last_cat')?'<label>Select Sub Category: </label>':'Select Child Category').'<select id="'.$elemId.$level.'" class="'.$newlevel.'" name="'.(($elemId == "last_cat")?"category_id":"cat").'"><option value="">Select</option>';
    while($childs = $cat_query->fetch_assoc()){
        echo '<option value="'.$childs['category_id'].'">'.$childs['name'].'</option>';
    }
    echo '</select>';
    exit;
}

$main_cat_query = $conn->query("SELECT c.category_id, c.parent_id, d.name FROM `oc_category` c left join oc_category_description d on d.category_id = c.category_id WHERE c.`parent_id` = 0");

?>

<p><b><u>Generate Shopify CSV for a Category and Download!</u></b></b></p>
<form method="post">
<label>Select A Category: </label>
<select id="cat-level" class="level1">
    <option value="">Select</option>
    <?php
    while($maincats = $main_cat_query->fetch_assoc()){ 
        echo '<option value="'.$maincats['category_id'].'">'.$maincats['name'].'</option>';
     } ?>
</select>

<div id="level1"></div>

<div id="level2"></div>

<div style="padding-top:10px;" id="gen-btn">
    <button class="hide" id="generate" name="generate" type="submit">Generate CSV</button>
</div>


</form>

<a class="<?=($write_stat == true)?'':'hide'?>" id="download-link" href="<?=$file?>">Download</a>

<script type="text/javascript">

$(document).on('change', "[id*='cat-level']", function(){
        var idlevel = $(this).attr("class");
       
        if($(this).val()){
            $.ajaxSetup({async: false}); 
            $.ajax({
            url:"http://www.site.com/export.php",
            data:{parent_id: $(this).val(), level: idlevel},
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                $("#"+idlevel).html($(data));
                selval = $("."+idlevel+" option:selected").text();
                (idlevel == "level1")?$("#level2").html(""):'';
                $("#gen-btn").append("<input type='hidden' name='"+idlevel+"' value='"+selval+"' >");
                //$(data).insertAfter($("#level1")); 
                
            }
        });
        $.ajaxSetup({async: true});

        }
        
      });
   $(document).on('change', "[id='last_catlevel2']", function(){
       $("#generate").removeClass("hide");
       $("#gen-btn").append("<input type='hidden' name='level3' value='"+$("#last_catlevel2 option:selected").text()+"' >");
   }); 
    
</script>


