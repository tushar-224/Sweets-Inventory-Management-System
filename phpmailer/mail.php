<?php
  //  $random_otp = rand(000000,999999);
  session_start();
    $name = $_GET['username'];
    $project = $_GET['project'];
    $otp = $_GET['temp_otp'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $project;?></title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center; background-color: #f2f2f2; padding: 20px;">

    <div style="background-color: #ffffff; border-radius: 10px; padding: 20px; margin: 0 auto; max-width: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
  <center>  <img style="width:70px;" src="https://www.pngmart.com/files/23/Lock-PNG-HD.png"/>    </center>
    <h1><?= $project;?></h1>
        <p>Hello, <?= $name;?></p>
        <p>Your one-time password (OTP) is:</p>
        <p style="font-size: 24px; font-weight: bold; color: #007bff;"><?= $otp;?></p>
        <p>Please use this code to access your account or complete your action. </p>
        <p>If you didn't request this OTP, please ignore this email.</p>
        <p>Thank you for using our service!</p>
    </div>

    <p style="margin-top: 20px; color: #888;">This email was sent by <?= $project;?></p>
</body>
</html>
