<?php
session_start();
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0){
    header("Location:./");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | TD Sweets</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        html, body{
            height:100%;
        }
        body{
            background-image:url('./images/im.png');
            background-size:650px 450px;
            background-repeat:no-repeat;
            background-position:right;
            background-Color:#7851b8;
        }
        h1#sys_title {
            font-size: 6em;
            text-shadow: 3px 3px 10px #000000;
        }
        @media (max-width:700px){
            h1#sys_title {
                font-size: inherit !important;
            }
        }
        #logos{
         position: absolute;
           top: 10%;
           left: 10%;
           height:10%;
           width:20%;
           
        }
        login-form{
            height:10%;
            width:30%;
        }
    </style>
</head>
<body class="">
   <div class="h-100 d-flex jsutify-content-center align-items-center">
       <div class='w-100'>
    
        <!---<h1 class="py-5 text-center text-light px-4" id="sys_title">TD Sweets</h1>-->
        <a href="index.html" class="logo me-lg-0"><img src="images/logo10.png" alt="" class="img-fluid" id="logos"></a>
        <div class="card my-2 col-md-3 offset-md-1">
            <div class="card-body">
                <form action="" id="login-form">
                    <strong><center>Please enter your credentials.</center></strong>
                    <div class="form-group">
                        <strong><label for="username" class="control-label">Username</label></strong>
                        <input type="text" id="username" autofocus name="username" class="form-control form-control-sm rounded-0" value="admin" autocomplete"off" required>
                    </div>
                    <div class="form-group">
                        <strong><label for="password" class="control-label">Password</label></strong>
                        <input type="password" id="password" name="password" class="form-control form-control-sm rounded-0" value="admin123" required>
                    </div>
                    <div class="form-group d-flex w-100 justify-content-end">
                        <button class="btn btn-sm btn-primary rounded-0 my-1">Login</button>
                    </div>
                </form>
				<div id="verify" style="display:none;">
                <center><h4>🔒 Two Factor Verification</h4></center>
				<center>
                <p id="email_sent" style="display:none;"><strong>Otp has been sent to your email <span id="user_email" style="color:green;"></span></strong></p>
				<p id="email_sending"><strong>Otp sending....</strong></p>
				<p id="email_failed" style="display:none;"><strong>failed to send otp, something is invalid, try again later</strong></p>
                </center>

                <div id="otp_true" style="display:none;" class="alert alert-success" role="alert">
                  OTP Verified Successfully
                    </div>
                    <div id="otp_false" style="display:none;" class="alert alert-danger" role="alert">
                  Invalid OTP
                    </div>
				 <div class="form-group">
                        <label for="password" class="control-label">Enter OTP</label>
                        <input type="number" id="last_otp" name="last_otp" class="form-control form-control-sm rounded-0" required>
                    </div>
					  <div class="form-group d-flex w-100 justify-content-end">
                        <button id="btn_verify" class="btn btn-sm btn-primary rounded-0 my-1">Verify</button>
                    </div>
				</div>
            </div>
        </div>
       </div>
   </div>
</body>
<script>
    $("#btn_verify").click(function(){
        var otp = $("#last_otp").val();
        $.get(`./Actions.php?a=verify_otp&last_otp=${otp}`,function(data){
            data = JSON.parse(data);
            console.log(data);
            if(data.status==='success'){
                $("#otp_true").fadeIn();
                setTimeout(() => {
                    $("#otp_true").fadeOut();
                        }, 1000);
                setTimeout(() => {
                            location.replace('./');
                        }, 2000);
            } else {
                $("#otp_false").fadeIn();
                setTimeout(() => {
                    $("#otp_false").fadeOut();
                        }, 1000);
            }
        });
    });
    $(function(){
        $('#login-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Loging in...')
            $.ajax({
                url:'./Actions.php?a=login',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.replace('./');
                        }, 2000);
                    } else if(resp.status == 'otp'){
                      
                       $("#verify").show();
					   $("#login-form").hide();
					   $("#user_email").text(resp.email);
                       $.get(`./phpmailer/index.php?temp_otp=${resp.temp_otp}&name=${resp.name}&email=${resp.email}`, function(data) {
                            if (data === 'Email sent successfully!') {
                                $("#email_sent").show();
                                $("#email_sending").hide();
                            } else {
                                // Handle the case when the email sending fails
                                $("#email_failed").show(); // Show an element indicating the failure
                                $("#email_sending").hide();
                            }
                        });

                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
</html>