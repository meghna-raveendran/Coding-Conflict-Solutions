<?php


$offerToEdit = $offerToEdit[Tags::RESULT][0];
$content = json_decode($offerToEdit['content'],JSON_OBJECT_AS_ARRAY);


$adminAccess = "";
if($offerToEdit['user_id']!=$this->adminbrowser->getData("id_user") && $this->adminbrowser->getData("typeAuth")=="admin"){
    $adminAccess = "disabled";
}

$offertypes =  $offertypes['offertypes'];

$levels = $selected_offers;

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Message Error -->
            <div class="col-sm-12">
                <?php $this->load->view("backend/include/messages");?>
            </div>

        </div>

        <div class="row">


            <div class="col-xs-12">
                <form id="form">
                    <div class="box box-solid">
                        <div class="box-header">

                            <div class="box-title">
                                <b><?=Translate::sprint("Edit offer","")?></b>
                            </div>

                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <?php
                                $timeOffer = strtotime($offerToEdit['date_end']);
                                $currentTime = time();
                            ?>


                            <?php if($timeOffer<$currentTime and ($offerToEdit['date_end']!=$offerToEdit['date_start'])){ ?>
                            <div class="callout callout-danger">
                                <h4><?=Translate::sprint("Offer is expired","")?></h4>

                                <p><?=Translate::sprint("That offer will  expired in","")?> <?=date("Y-m-d",strtotime($offerToEdit['date_end']))?> </p>
                            </div>
                            <?php } ?>


                            <div class="col-sm-4">

                                <div class="form-group">
                                    <label><?=Translate::sprint("Store","")?></label>
                                    <select <?=$adminAccess?> class="form-control select2 selectStore" style="width: 100%;">
                                        <option selected="selected" value="0"><?=Translate::sprint("Select store","")?></option>
                                        <?php

                                        if(isset($myStores[Tags::RESULT])){
                                            foreach ($myStores[Tags::RESULT] as $st){
                                                if($st['id_store']==$offerToEdit['store_id']){
                                                    echo '<option adr="'.$st['address'].'" 
                                                    lat="'.$st['latitude'].'" lng="'.$st['longitude'].'" 
                                                    value="'.$st['id_store'].'" selected>'.$st['name'].'</option>';
                                                }else{
                                                    echo '<option adr="'.$st['address'].'" 
                                                    lat="'.$st['latitude'].'" lng="'.$st['longitude'].'" 
                                                    value="'.$st['id_store'].'">'.$st['name'].'</option>';
                                                }

                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?=Translate::sprint("Offer_type","")?></label>
                                    <select id="offertype-level1" name="level1" class="form-control selectCat select2">
                                    <?php if(!empty($offertypes)){ ?>
                                    <option>Select Offertype</option>
                                    <?php foreach($offertypes AS $offertype){ 
                                    if($offertype->id_offertype == $levels['level1'] ){
                                    ?>
                                    <option selected value="<?=$offertype->id_offertype?>"><?=$offertype->name?></option>
                                        <?php }  else{
                                    ?>
                                  <option value="<?=$offertype->id_offertype?>"><?=$offertype->name?></option>

                                    <?php } } ?>
                                    <?php } ?>

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?=Translate::sprint("Name","")?></label>
                                    <input <?=$adminAccess?> type="text" class="form-control" id="name" placeholder="black friday offer" value="<?=$offerToEdit['name']?>">
                                </div>
                            </div>

                            <?php

                            $price = $content['price'];
                            $percent = $content['percent'];


                            if(!is_array($content['currency']))
                                $currency = json_decode($content['currency'],JSON_OBJECT_AS_ARRAY);
                            else
                                $currency = $content['currency'];

                            ?>

                            <div class="col-sm-4">

                                <div class="form-group">
                                    <div class="row">

                                        <div class="col-sm-6  no-margin">

                                            <label><?=Translate::sprint("Offer price")?></label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                      <input <?=$adminAccess?> name="poffer" type="radio" id="price" <?php if($price>0) echo "checked";?>>
                                                    </span>
                                                    <input <?=$adminAccess?> type="number" class="form-control" id="priceInput" placeholder="<?=Translate::sprint("Enter price of your offer")?>" value="<?=$price?>" <?php if($price>0 || $price<0) echo "checked";?>>
                                                </div>
                                        </div>

                                        <div class="col-sm-6" style="padding-left: 0px;">
                                            <?php

                                            $currencies =  $currencies = json_decode(CURRENCIES,JSON_OBJECT_AS_ARRAY);
                                            $default_currency = $this->offers_bundle->getDefaultCurrency();

                                            ?>
                                            <div class="form-group">
                                                <label><?=Translate::sprint("Select offer currency")?></label>
                                                <select <?=$adminAccess?> id="selectCurrency" class="form-control select2 selectCurrency" style="width: 100%;">
                                                    <option selected="selected" value="0"> <?=Translate::sprint("Select")?></option>
                                                    <?php
                                                    $def_currency = $this->offers_bundle->getDefaultCurrency();
                                                    foreach ($currencies as $key => $value){

                                                        if($key==$currency['code'])
                                                            echo '<option selected="selected" value="'.$key.'">'.$value['name'].' ('.$value['code'].')</option>';
                                                        else
                                                            echo '<option value="'.$key.'">'.$value['name'].' ('.$value['code'].')</option>';

                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label><?=Translate::sprint("Offer percent","")?> </label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                        <input <?=$adminAccess?> name="poffer" type="radio" id="percent" <?php if($percent>0 || $percent<0) echo "checked";?>>
                                                </span>
                                                <input <?=$adminAccess?> type="number" class="form-control" id="percentInput" <?php if($percent==0)echo "disabled";?>value="<?php if($percent>0 || $percent<0)echo $percent;?>" placeholder="Exemple : -50 %">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> <?=Translate::sprint("Date Begin","")?>  </label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="mdi mdi-calendar"></i>
                                                        </div>
                                                        <input disabled  class="form-control" data-provide="datepicker" placeholder="YYYY-MM-DD" type="text" name="date_b" id="date_b" value="<?=date("Y-m-d",strtotime($offerToEdit['date_start']))?>"/>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">  <label><?=Translate::sprint("Date End","")?> </label>

                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="mdi mdi-calendar"></i>
                                                        </div>

                                                        <?php

                                                        $date_end = "";
                                                            if($offerToEdit['date_end']!="")
                                                                $date_end = date("Y-m-d",strtotime($offerToEdit['date_end']));

                                                        ?>
                                                        <input <?=$adminAccess?> class="form-control" data-provide="datepicker" type="text" placeholder="YYYY-MM-DD" name="date_e" id="date_e" value="<?=$date_end?>"/>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-4">
                                <div class="form-group required">

                                    <?php

                                    $images = $offerToEdit['image'];

                                    if(!empty($images))
                                        $images = array($images);

                                    ?>

                                    <label for="name"><i class="mdi mdi-paperclip"></i>&nbsp;&nbsp;<?=Translate::sprint("Image","")?> <sup>*</sup></label>
                                    <label class="msg-error-form image-data"></label>
                                    <input <?=$adminAccess?> type="file" name="addimage" id="fileupload"><br>
                                    <div class="clear"></div>
                                    <div id="progress" class="hidden">
                                        <div class="percent" style="width: 0%"></div>
                                    </div>
                                    <div class="clear"></div>


                                    <div id="image-previews">

                                        <?php if(!empty($images)){ ?>

                                            <?php foreach ($images as $value){ ?>

                                                <?php

                                                $item = "item_".$value['name'];
                                                $idata = $value['name'];
                                                //$imagesData = _openDir($value);
                                                $imagesData = $value;

                                                ?>


                                                <div class="image-uploaded <?=$item?>">
                                                    <a id="image-preview">
                                                        <img src="<?=$imagesData['200_200']['url']?>" alt="">
                                                    </a>

                                                    <div class="clear"></div>
                                                    <a href="#" data="<?=$idata?>" id="delete"><i class="fa fa-trash"></i>&nbsp;&nbsp;
                                                        <?=Translate::sprint("Delete","")?></a></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>



                                </div>
                            </div>


                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label><?=Translate::sprint("Description","")?></label>
                                    <textarea <?=$adminAccess?> class="form-control" rows="3" id="editable-textarea"
                                                                placeholder="<?=Translate::sprint("Enter")?> ..."><?=$content['description']?></textarea>
                                </div>
                            </div>


                            <?php if($adminAccess=="")  {?>

                                <div class="form-group col-sm-12">
                                    <button type="button" class="btn  btn-primary" id="btnEdit" > <span class="glyphicon glyphicon-check"></span>
                                        <?=Translate::sprint("Edit","")?> </button>
                                    <button type="button" class="btn  btn-default" id="btnEdit" onclick="redirectToAddNew()"> <span class="mdi mdi-sale "></span>
                                        <?=Translate::sprint("Create new","")?> </button>
                                </div>

                            <?php } ?>



                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </form>
            </div>


            <?php

//            $data['list'] = $list;
//            $data['pagination'] = $pagination;
//            $this->load->view("backend/offers/offers",$data);

            ?>



            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>


<!-- DataTables -->
<script src="<?=  base_url("template/backend/plugins/datatables/jquery.dataTables.min.js")?>"></script>
<script src="<?=  base_url("template/backend/plugins/datatables/dataTables.bootstrap.min.js")?>"></script>
<!-- SlimScroll -->
<script src="<?=  base_url("template/backend/plugins/slimScroll/jquery.slimscroll.min.js")?>"></script>
<!-- FastClick -->
<script src="<?=  base_url("template/backend/plugins/fastclick/fastclick.js")?>"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="<?=  base_url("template/backend/plugins/uploader/js/jquery.iframe-transport.js")?>"></script>
<script src="<?=  base_url("template/backend/plugins/uploader/js/jquery.ui.widget.js")?>"></script>
<script src="<?=  base_url("template/backend/plugins/uploader/js/jquery.fileupload.js")?>"></script>

<script>

    <?php

    $token = $this->adminbrowser->setToken("SUPIMAGES-4555");

    ?>

    Uploader(true);

    var fileUploaded = {};
    <?php
    if(!empty($images)){

        foreach ($images as $value){

            $key = $value['name'];

            $item = "item_".$key;
            $data = $value;

            echo "fileUploaded[$key]=$key ;";

        }
    }
    ?>


    $(".image-uploaded #delete").on('click',function(){
        var nameDir = $(this).attr("data");
        delete fileUploaded[nameDir];
        $(".image-uploaded.item_"+nameDir).remove();
        return false;
    });

    
    function Uploader(singleFile){

        $('#fileupload').fileupload({
            url: "<?=site_url("1.0/upload/image")?>",
            sequentialUploads: true,
            loadImageFileTypes:/^image\/(gif|jpeg|png|jpg)$/,
            loadImageMaxFileSize: 10000,
            singleFileUploads: singleFile,

            formData     : {
                'token'     : "<?=$token?>",
                'ID'        : "<?=sha1($token)?>"
            },
            dataType: 'json',
            done: function (e, data) {


                var results = data._response.result.results;
                $("#progress").addClass("hidden");
                $("#progress .percent").animate({"width":"0%"});
                $(".image-uploaded").removeClass("hidden");

                if(singleFile==true){
                    fileUploaded = {};
                    $("#image-previews").html(results.html);
                }else
                    $("#image-previews").append(results.html);

                fileUploaded[results.image] = results.image;
                //$("#image-data").val(results.image_data);

                $(".image-uploaded #delete").on('click',function(){
                    var nameDir = $(this).attr("data");
                    delete fileUploaded[nameDir];
                    $(".image-uploaded.item_"+nameDir).remove();
                    return false;
                });

            },
            fail:function (e, data) {

                $("#progress").addClass("hidden");
                $("#progress .percent").animate({"width":"0%"});


            },
            progressall: function (e, data) {

                var progress = parseInt(data.loaded / data.total * 100, 10);

                $("#progress").removeClass("hidden");
                $("#progress .percent").animate({"width":progress+"%"},"linear");

            },
            progress: function (e, data) {

                var progress = parseInt(data.loaded / data.total * 100, 10);

            },
            start: function (e) {

                $("#fileupload").removeClass("input-error");
                $(".image-data").text("");

            }
        });



    }


</script>

<!-- page script -->
<script src="<?=  base_url("/template/backend/plugins/jQuery/jQuery-2.1.4.min.js")?>"></script>
<script src="<?=  base_url("template/backend/plugins/datepicker/bootstrap-datepicker.js")?>"></script>
<script src="<?=  base_url("template/backend/plugins/select2/select2.full.min.js")?>"></script>

<?php if($adminAccess=="")  {?>
<script>

    $('.selectCurrency').select2();

    var store_id = <?=$offerToEdit['store_id']?>;

    $.fn.datepicker.defaults.format = "yyyy-mm-dd";
    $('.datepicker').datepicker({
        startDate: '-3d'
    });

    <?php
    $token = $this->adminbrowser->setToken("SU774aQ55");
    ?>
    //Loading offer types 
    var levels = {};
    <?php foreach($levels as $level=>$oftype){ ?>
    levels['<?=$level?>'] = '<?=$oftype?>';
    <?php } ?>
    $.each(levels, function(k, v) {
        $("#offertype-"+k).val(v);
        loadlevels(k);
    });
    
    function loadlevels(levelk){
        
        var idlevel = "offertype-"+levelk;
        var level = parseInt(idlevel.replace("offertype-level", ""));
        var oval = $("#"+idlevel).val();
        var offertype_name =$("#"+idlevel).find('option:selected').text() 
        if(oval){
            $.ajaxSetup({async: false}); 
            $.ajax({
            url:"<?=  base_url("/ajax/getOffertypesbyParent")?>",
            data:{parent_id: oval, level: level, offertype_name: offertype_name},
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                for(var i=level+1; i < 6; i++){
                    if($("#offertype-level"+i).length){
                        $("#offertype-level"+i).closest(".form-group").remove();
                    }
                }
                $(data).insertAfter($("#"+idlevel).closest(".form-group"));
            }
        });
        $.ajaxSetup({async: true});

        }
        
    }
    
    $(document).on('change', "[id*='offertype-level']", function(){
        loadlevels($(this).attr("id").replace("offertype-",""));
        /*var idlevel = $(this).attr("id");
        var level = parseInt($(this).attr("id").replace("offertype-level", ""));
        if($(this).val()){
            $.ajaxSetup({async: false}); 
            $.ajax({
            url:"<?=  base_url("/ajax/getOffertypesbyParent")?>",
            data:{parent_id: $(this).val(), level: level, offertype_name: $(this).find('option:selected').text()},
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                for(var i=level+1; i < 6; i++){
                    if($("#offertype-level"+i).length){
                        $("#offertype-level"+i).closest(".form-group").remove();
                    }
                }
                $(data).insertAfter($("#"+idlevel).closest(".form-group")); 
            }
        });
        $.ajaxSetup({async: true});

        }*/
        
      });

    $("#btnEdit").on('click',function(){
        var description = $("#form #editable-textarea").val();
        var name = $("#form #name").val();
        var price = 0;
        var percent = 0;

        if($("#form #price").prop('disabled', !this.checked) && $("#form #priceInput").val().length!=0){
            price = $("#form #priceInput").val();
        }

        if($("#form #percent").prop('disabled', !this.checked)  && $("#form #percentInput").val().length!=0){
            percent = $("#form #percentInput").val();
        }
        var offertype = $("#form #offertype").val();

        var date_e = $("#form #date_e").val();

        var currency = $("#form #selectCurrency").val();

        var dataSet0 = {
            "token":"<?=$token?>",
            "store_id":store_id,
            "name":name,
            "image":fileUploaded,
            "description":description,
            "price":price,
            "date_end":date_e,
            "offer_id":<?=$offerToEdit['id_offer']?>,
            "percent":percent,
            "currency":currency,
            "offertype" : offertype
        };



        $.ajax({
            url:"<?=  base_url("/ajax/editOffer")?>",
            data:dataSet0,
            dataType: 'json',
            type: 'POST',
            beforeSend: function (xhr) {

                $("#btnCreate").attr("disabled",true);

            },error: function (request, status, error) {
                alert(request.responseText);
                $("#btnCreate").attr("disabled",false);
                console.log(request)
            },
            success: function (data, textStatus, jqXHR) {

                $("#btnCreate").attr("disabled",false);
                if(data.success===1){
                    document.location.href = "<?=admin_url("offers")?>";
                }else if(data.success===0){
                    var errorMsg = "";
                    for(var key in data.errors){
                        errorMsg = errorMsg+data.errors[key]+"\n";
                    }
                    if(errorMsg!==""){
                        alert(errorMsg);
                    }
                }
            }
        });

        return false;

    });





    $('.selectStore').select2();
    $('.selectStore').on('select2:select', function (e) {
        // Do something
        var data = e.params.data;
        var id = data.id;
        store_id = id;

        if(id>0){
            store_id = id;
        }else {
            store_id = 0;
        }

    });


    $("input[name=poffer]").on('change',function () {

        var checked  = $(this).attr("id");
        if(checked=="price"){
            $("#"+checked+"Input").attr("disabled",false);
            $("#percentInput").attr("disabled",true);
            $("#selectCurrency").attr("disabled",false);
        }else {
            $("#"+checked+"Input").attr("disabled",false);
            $("#priceInput").attr("disabled",true);
            $("#selectCurrency").attr("disabled",true);
        }

    });


    <?php

        if($price>0){

            echo '$("#priceInput").attr("disabled",false);
            $("#percentInput").attr("disabled",true);
            $("#selectCurrency").attr("disabled",false);';
        }else{
            echo '$("#percentInput").attr("disabled",false);
            $("#priceInput").attr("disabled",true);
            $("#selectCurrency").attr("disabled",true);';
        }

    ?>

    function redirectToAddNew() {
        document.location.href="<?=admin_url("offers/add")?>";
    }

</script>
<?php } ?>



