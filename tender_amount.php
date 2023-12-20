<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
<center>
            <div id="box_qrcode" style="display:none;" class="p-2">
                <img width="300" id="qrcode" src="https://i.gifer.com/ZKZg.gif"/>
                <div id="qr_id"></div>
            </div>
        </center>
    <div class="form-group">
        <label for="change" class="control-label fs-4 fw-bold">Payment Method</label>
        <select class="form-control" id="payment_method" name="payment_method">
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
        </select>
    </div>
    <br>
    <div class="form-group">
        <label for="amount" class="control-label fs-4 fw-bold">Payable Amount</label>
        <input type="text" id="amount" class="form-control form-control-lg text-end" value="<?php echo $_GET['amount'] ?>" disabled>
    </div>
    <div id="_amount" class="form-group">
        <label for="tender" class="control-label fs-4 fw-bold">Tendered Amount</label>
        <input type="number" step="any" id="tender" class="form-control form-control-lg text-end" value="0">
    </div>
    <div id="_change" class="form-group">
        <label for="change" class="control-label fs-4 fw-bold">Change</label>
        <input type="text" id="change" class="form-control form-control-lg text-end" value="0" disabled>
    </div>
    <div class="w-100 d-flex justify-content-end mt-2">
            <button class="btn btn-sm btn-primary me-2 rounded-0" type="button" id="save_trans">Save</button>
            <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
        </div>
      
</div>
<script>
    function generateRandomString(length) {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let randomString = '';

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    randomString += characters.charAt(randomIndex);
  }

  return randomString;
}

var randomString = generateRandomString(50);
console.log(randomString);
//$("#qr_id").text(randomString);
//

var getAmount = $("#amount").val();
var upi_code = `${encodeURIComponent('upi://pay?pa=9799291563@ybl&pn=Sweets Shop&am='+getAmount+'&tn='+randomString+'&cu=INR')}`;
var qrcode = `https://quickchart.io/qr?text=${upi_code}&dark=ff6c0a&size=500&centerImageUrl=${encodeURIComponent("https://apps.mgov.gov.in/details/icon/1493")}`;

console.log(qrcode);
//var qrcode = `https://quickchart.io/qr?text=${randomString}&dark=ff6c0a&size=500&centerImageUrl=${encodeURIComponent("https://apps.mgov.gov.in/details/icon/1493")}`;
$("#qrcode").attr('src',qrcode);
$("#qr_id").html(`<p style="color:blue;"><b>Waiting for payment....</b></p>`);
setInterval(() => {
    $.get(`./Actions.php?a=payment_status&payment_id=${randomString}`,function(data){
        data =JSON.parse(data);
        if(data.status==='success'){
            $("#qr_id").html(`<b><p style="color:green;"><b>Payment Received, Thank you<br>Saving...</b></p>`);
            setTimeout(() => {
                $('#change').removeClass('border-danger')
            
            $('#uni_modal').modal('hide')
                $('#transaction-form').submit() 
            }, 5000);
           
        }
    });
}, 5000);

$(function(){
        $('#payment_method').on('change',function(){
            $('#input_payment_method').val($(this).val());
            $("#_amount").show();
                $("#_change").show();
                $("#box_qrcode").hide();
               $("#save_trans").show();
            if($(this).val()==='upi'){
               $("#box_qrcode").show();
                $("#_amount").hide();
                $("#_change").hide();
                $("#tender").val(0);
                $("#change").val(0);
                $("#save_trans").hide();
            }
        })

        $('#uni_modal').on('shown.bs.modal',function(){
            if($(this).find('#tender').length > 0)
            $('#tender').trigger('focus').select();

        })
        $('#tender').on('keydown',function(e){
            if(e.which == 13){
                e.preventDefault()
                $('#save_trans').trigger('click')
            }
        })
        $('#tender').on('keypress input',function(){
            var tender = $(this).val() > 0? $(this).val() : 0;
            var amount = $('#amount').val().replace(/,/gi,"")
            $('[name="tendered_amount"]').val(tender)
            var change = parseFloat(tender) - parseFloat(amount)
            $('#change').val(parseFloat(change).toLocaleString('en-US'))
            $('[name="change"]').val(parseFloat(change))
        })
        $('#tender').focusout(function(){
            if($(this).val() <=0)
            $(this).val(0);
        })
        $('#save_trans').click(function(){
            $('#change').removeClass('border-danger') 
            if($('[name="change"]').val() < 0){
               alert("Amount is too low");
            } else {
                $('#uni_modal').modal('hide')
            $('#transaction-form').submit()
            }
          
        })
    })
</script>