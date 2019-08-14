<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" /> 
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
<style>
.container { margin:150px auto;}
</style>
</head>

<body>
<div class="container">
<?php foreach ($orders as $order) { ?>
<h1><?php echo $text_invoice; ?> #<?php echo $order['order_id']; ?></h1>
<table class="table table-bordered">
    <tr>
          <td class="text-right">GSTIN No: </td>
          <td id="gstin_num" class="text-right">123456789123456789</td>
    </tr>  
</table>
<table class="table table-bordered" id="prod-table">
    <thead>
        <tr>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b>HSNC (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</b></td>
          <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
          <td class="text-right"><b><?php echo $column_price; ?></b>(<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right"><b>Subtotal</b>(<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right">Disc (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right">Taxable Value (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right cgst-head">CGST (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right sgst-head">SGST (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="text-right igst-head">IGST (<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">)</td>
          <td class="rate-head cgst-tax-head">CGST Tax Rate (%)</td>
          <td class="rate-head sgst-tax-head">SGST Tax Rate (%)</td>
          <td class="rate-head igst-tax-head">IGST Tax Rate (%)</td>
          <td class="text-right"><b>Total Amount</b></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order['product'] as $product) { ?>
        <tr>
          <td><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td><?php echo $product['hsnc']; ?></td>
          <td class="text-right prod-qty"><?php echo $product['quantity']; ?></td>
          <td data-price="<?php echo substr(str_replace(',', '', $product['price']), 3); ?>" class="text-right actual-price"><?php echo $product['actual_price']; ?></td>
          <td class="text-right subtotal"><?php echo $product['sub_total']; ?></td>
          <td class="text-right discount">0</td>
          <td class="text-right taxable_value"></td>
          <td class="text-right cgst_value"></td>
          <td class="text-right sgst_value"></td>
          <td class="text-right igst_value"></td>
          <td class="text-right cgst_tax_rate"><?php echo $product['cgst_rate']; ?></td>
          <td class="text-right sgst_tax_rate"><?php echo $product['sgst_rate']; ?></td>
          <td class="text-right igst_tax_rate"><?php echo $product['igst_rate']; ?></td>
          <td class="text-right total_value"><?php echo substr($product['total'], 3); ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($order['voucher'] as $voucher) { ?>
        <tr>
          <td><?php echo $voucher['description']; ?></td>
          <td></td>
          <td class="text-right">1</td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
        <?php //foreach ($order['total'] as $total) { ?>
        <tr>
          <td class="text-right" colspan="6">Total</td>
          <td class="text-right taxable_total"></td>
          <td class="text-right cgst_total"></td>
          <td class="text-right sgst_total"></td>
          <td class="text-right igst_total"></td>
          <td class="text-right ignore-col ignore-col-cgst"></td>
          <td class="text-right ignore-col ignore-col-sgst"></td>
          <td class="text-right ignore-col ignore-col-igst"></td>
          <td class="text-right total_total"></td>
        </tr>
        <?php //} ?>
    </tbody>
  </table>
  <?php } ?>

  <table class="table table-bordered">
    <tr>
          <td class="text-right">Total Amount (in words):</td>
          <td class="text-right amount_in_wrds">RupeesTwenty one thousnd two hundred and fort only</td>
    </tr>
    <tr>
          <td class="text-right">Taxable Amount: </td>
          <td class="text-right taxable_amount"></td>
    </tr>
     <tr>
          <td class="text-right">Total Tax:  </td>
          <td class="text-right total_tax"></td>
    </tr>
     <tr>
          <td class="text-right">Invoice Total: </td>
          <td class="text-right invoice_total"></td>
    </tr>
  </table>
  <div><button class="btn-primary" id="print-invoice">Print Preview</button></div>
  </div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="view/javascript/bootstrap/js/bootstable.js"></script>
<script>
 $('table').SetEditable();
 $(document).ready(function(){
    var shipping_selling = "<?php echo $shipping_selling?>";     
    if(shipping_selling == 1){
        $(".igst-head, .igst-tax-head, .igst_tax_rate, .igst_value, .ignore-col-igst, .igst_total").addClass("hide");
    }else{
       $(".cgst-head, .cgst-tax-head, .cgst_tax_rate, .cgst_value, .ignore-col-cgst, .cgst_total, .sgst-head, .sgst-tax-head, .sgst_tax_rate, .sgst_value, .ignore-col-sgst, .sgst_total").addClass("hide");
    }
    calculateSum("load");

 });
     $(".save-edits").click(function(){
        calculateSum("edit");  
    });

    function calculateSum(act){
      var sum = 0;
      elem = {"total_value":"total_total", "taxable_value" :"taxable_total", "cgst_value":"cgst_total", "sgst_value" :"sgst_total", "igst_value" :"igst_total"};

      $.each( elem, function( key, value ) {        
        var sum = 0;
        $('.'+key).each(function(){

        //Update taxable value = total rate - discount per product  
        if(key == "taxable_value"){ 
            if(act == "edit"){
              if($(".igst_tax_rate").is(":hidden")){
                var tax = parseFloat($(this).closest("tr").find(".cgst_tax_rate").html()) + parseFloat($(this).closest("tr").find(".sgst_tax_rate").html());
              }else{
                var tax = parseFloat($(this).closest("tr").find(".igst_tax_rate").html());
              }
              var new_price = (parseFloat($(this).closest("tr").find(".actual-price").attr("data-price"))*100)/(100+tax);

              $(this).closest("tr").find(".actual-price").html(new_price.toFixed(2));
              $(this).closest("tr").find(".subtotal").html((new_price*parseFloat($(this).closest("tr").find(".prod-qty").html())).toFixed(2));
            }
          
                       
            $(this).html(parseFloat($(this).closest("tr").find(".subtotal").html().replace("$","").replace(",","")) - parseFloat($(this).closest("tr").find(".discount").html().replace("$","").replace(",","")).toFixed(2));

            if($(this).closest("tr").find(".cgst_tax_rate").html()){

                if($(this).closest("tr").find(".cgst_value").is(":visible")){

                  $(this).closest("tr").find(".cgst_value").html( ((parseFloat($(this).closest("tr").find(".cgst_tax_rate").html())*parseFloat($(this).html()))/100).toFixed(2) + "<br> @"+ parseFloat($(this).closest("tr").find(".cgst_tax_rate").html()) +"%" );
                }  
            }

            if($(this).closest("tr").find(".sgst_tax_rate").html()){

                if($(this).closest("tr").find(".sgst_value").is(":visible")){
                  
                  $(this).closest("tr").find(".sgst_value").html(((parseFloat($(this).closest("tr").find(".sgst_tax_rate").html())*parseFloat($(this).html()))/100).toFixed(2) + "<br> @"+ parseFloat($(this).closest("tr").find(".sgst_tax_rate").html()) +"%" );
                }
            }
            if($(this).closest("tr").find(".igst_tax_rate").html()){

                if($(this).closest("tr").find(".igst_value").is(":visible")){
                  
                  $(this).closest("tr").find(".igst_value").html(((parseFloat($(this).closest("tr").find(".igst_tax_rate").html())*parseFloat($(this).html()))/100).toFixed(2) + "<br> @"+ parseFloat($(this).closest("tr").find(".igst_tax_rate").html()) +"%");
                }
            }
        }  

        if($(this).html())
          sum += parseFloat($(this).html().replace("$","").replace(",",""));
        });
        $("."+value).html(sum);        
      });
      $(".taxable_amount").html(parseFloat($(".taxable_total").html()).toFixed(2));
      $(".invoice_total").html(parseFloat($(".total_total").html()).toFixed(2));
      $(".total_tax").html((parseFloat($(".cgst_total").html())+parseFloat($(".sgst_total").html())+parseFloat($(".igst_total").html())).toFixed(2));
    }   

    function openPrintDialogue(){
        $.ajax({
            url: 'index.php?route=sale/order/print-invoice',
            dataType: 'html',
            data: '&token='+'<?=$token?>&order_id='+'<?=$order['order_id']?>',
            success: function(html) {
              $("[name='buttons'],.cgst_tax_rate,.sgst_tax_rate,.igst_tax_rate,.rate-head,.ignore-col").addClass("hide");

              var out = html.replace(/\{table}/g, $("#prod-table").html()).replace(/\{amount_in_wrds}/g, $(".amount_in_wrds").html()).replace(/\{taxable_amount}/g, $(".taxable_amount").html()).replace(/\{total_tax}/g, $(".total_tax").html()).replace(/\{invoice_total}/g, $(".invoice_total").html()).replace(/\{gstin_num}/g, $("#gstin_num").html()).replace(/\{seller_address}/g, '<?php echo $seller_address?>').replace(/\{seller_single_line_addrs}/g, '<?php echo $seller_single_line_addrs?>'); 

              $("[name='buttons'],.cgst_tax_rate,.sgst_tax_rate,.igst_tax_rate,.rate-head,.ignore-col").removeClass("hide");

              $('<iframe>', {
              name: 'myiframe',
              class: 'printFrame'
              })
              .appendTo('body')
              .contents().find('body')
              .append(out);

              window.frames['myiframe'].focus();
              window.frames['myiframe'].print();

              setTimeout(() => { $(".printFrame").remove(); }, 1000);

            }
          });        
  };

  $("#print-invoice").on('click', openPrintDialogue);

</script>

</body>
</html>



