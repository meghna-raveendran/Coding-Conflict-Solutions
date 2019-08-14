<!DOCTYPE html>

<html dir="ltr" lang="en">

<html>
<head>
<style>
body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 12pt "Tahoma";
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 5mm 5mm;
        margin: 10mm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage{
      padding: 0px;
      border: 0px red solid;
      height: 257mm;
      outline: 0cm #FFEAEA solid;
    }
    /*.subpage {
        padding: 1cm;
        border: 5px red solid;
        height: 257mm;
        outline: 2cm #FFEAEA solid;
    }*/
    .right{
        text-align: right;
    }
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm; /*297*/        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }

    table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

 th {
    
    text-align: left;
    padding: 8px;
    font-size: 10px;
}

td{
    border: 1px solid #0c0c0c;
    text-align: left;
    padding: 3px;
    font-size: 10px;
    font-weight: 500;
}

/*tr:nth-child(odd) {
    background-color: #dddddd;
}*/
.hide {
    display: none !important;
}


</style>
</head>
<body>
  <?php foreach ($orders as $order) { ?>


<div class="book">
    <div class="page">
        <div class="subpage">

             <table>
         
                  <tr>
                        <th><img src="http://www.bombaybuy.com/image/catalog/BB%20logo.png" style="width: 75px;"></th>
                                    <th style="padding-left:165px;">{seller_address}
                                    </th>
                                    <th style="text-align:right;">Invoice No.: <?php echo $invoice_num; ?></br>
                                        Invoice Date: <?php echo date("d/m/Y")?>
                                    </th>
                  </tr>
                  
            </table>


            <table>
         
                  <tr>
                        <th>-----------------------------------------------------------------------------------------</th>
                                    <th>RETAIL INVOICE</th>
                                    <th>--------------------------------------------------------------------------------------------------</th>
                  </tr>
                  
            </table>


            <table>
         
                  <tr>
                        <th>Billing Address:</br>
                            <?php echo $order['payment_address']; ?>
                          </th>
                          <th style="padding-left:165px;">Shipping Address: </br>
                            <?php echo $order['shipping_address']; ?>
                          </th>
                          <th style="text-align:right;">Order Date: <?php echo $order['date_added']; ?></br>
                            Order No.: <?php echo $order['order_id']; ?></br>
                            Payment Mode: <?php echo $order['payment_method']; ?></br>
                              GSTIN No: {gstin_num}
                          </th>
                  </tr>
                  
            </table>

            <table style="border-top:1px solid black;border-bottom:1px solid black;">
         
                  <tr>
                        <th>Country Of Supply: India</th>
                                    <th>Place of supply: <?php echo $order['shipping_city']; ?></th>
                                    <th>Date:<?php echo date("d/m/Y")?></th>
                  </tr>
                  
            </table>

            <table><br></table>



            <table style="border:1px solid black;">
         
                  {table}
                  
            </table>

            <table>
         
                  <tr>
                        <th>Total Amount (in words): {amount_in_wrds}</th>
                                    
                                    <th>Taxable Amount: </br>
                                      Total Tax: </br>
                                      Invoice Total: </br>
                                        
                                    </th>

                                    <th class="right"> <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{taxable_amount}</br>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{total_tax}</br>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{invoice_total}</br>
                                    </th>
                  </tr>
                  
            </table>

            <table>
         
                  <tr>
                        <th>Purchased Online at BombayBuy.com</th>
                                    
                                    <th>Signature</th>
                  </tr>
                  <tr>
                    <td style="border:none;">If undelivered please return to {seller_single_line_addrs}</td>
                    <td style="border:none;"></td>
                  </tr>
                  
            </table>

            <hr>

            <table>
         
                  <tr>
                        <th style="text-align:center;">For terms & Conditions & Return Policy, Please visit wwww.bombaybuy.com</th>
                                    
                                    
                  </tr>
                  
                  
            </table>

            <!-- second Table-->

            <table>
         
                  <tr>
                        <th><img src="http://www.bombaybuy.com/image/catalog/BB%20logo.png" style="width: 75px;"></th>
                                    <th style="padding-left:165px;">{seller_address} </th>
                                    <th style="text-align:right;">Invoice No.: <?php echo $invoice_num; ?></br>
                                        Invoice Date: <?php echo date("d/m/Y")?>
                                    </th>
                  </tr>
                  
            </table>


            <table>
         
                  <tr>
                        <th>-----------------------------------------------------------------------------------------</th>
                                    <th>RETAIL INVOICE</th>
                                    <th>--------------------------------------------------------------------------------------------------</th>
                  </tr>
                  
            </table>


            <table>
         
                  <tr>
                        <th>Billing Address:</br>
                            <?php echo $order['payment_address']; ?>
                          </th>
                          <th style="padding-left:165px;">Shipping Address: </br>
                            <?php echo $order['shipping_address']; ?>
                          </th>
                          <th style="text-align:right;">Order Date: <?php echo $order['date_added']; ?></br>
                            Order No.: <?php echo $order['order_id']; ?></br>
                            Payment Mode: <?php echo $order['payment_method']; ?></br>
                              GSTIN No: {gstin_num}
                          </th>
                  </tr>
                  
            </table>

            <table style="border-top:1px solid black;border-bottom:1px solid black;">
         
                  <tr>
                        <th>Country Of Supply: India</th>
                                    <th>Place of supply: <?php echo $order['shipping_city']; ?></th>
                                    <th>Date:<?php echo date("d/m/Y")?></th>
                  </tr>
                  
            </table>

            <table><br></table>



            <table style="border:1px solid black;">         

              {table}
                            
            </table>

            <table>
         
                            <tr>
                                    <th>Total Amount (in words): {amount_in_wrds}</th>
                                    
                                    <th>Taxable Amount: </br>
                                      Total Tax: </br>
                                      Invoice Total: </br>
                                        
                                    </th>

                                    <th class="right"> <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{taxable_amount}</br>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{total_tax}</br>
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Indian_Rupee_symbol.svg/10px-Indian_Rupee_symbol.svg.png" style="width: 6px;">{invoice_total}</br>
                                    </th>
                            </tr>
                            
                </table>

                <table>
         
                            <tr>
                                    <th>Purchased Online at BombayBuy.com</th>
                                    
                                    <th>Signature</th>
                            </tr>
                            <tr>
                                <td style="border:none;">If undelivered please return to {seller_single_line_addrs}</td>
                                <td style="border:none;"></td>
                            </tr>
                            
                </table>


            <hr>

            <table>
         
                  <tr>
                        <th style="text-align:center;">For terms & Conditions & Return Policy, Please visit wwww.bombaybuy.com</th>
                                    
                                    
                  </tr>
                  
                  
            </table>



        </div>  

    </div>
   
</div>
<?php } ?>
</body>

</html>