<?php
  session_start();
// Include the PHPMailer autoload file
require 'vendor/autoload.php';

$project="TD Sweets";
$username=$_GET['name'];
$email=$_GET['email'];
$temp_otp = $_GET['temp_otp'];

// Create a new PHPMailer instance
$mail = new PHPMailer\PHPMailer\PHPMailer();

// SMTP Configuration
$mail->isSMTP();
$mail->Host = 'mboxhosting.com';
$mail->SMTPAuth = true;
$mail->Username = 'otp@sweetsajmer.onlinewebshop.net'; // Your Gmail email address
$mail->Password = 'Diksha@123'; // Your Gmail password
$mail->SMTPSecure = 'tls'; // Enable TLS encryption; `ssl` also accepted
$mail->Port = 587; // TCP port to connect to

// Sender and recipient settings
$mail->setFrom('otp@sweetsajmer.onlinewebshop.net', $project); // Sender's email and name
$mail->addAddress($email, $username); // Recipient's email and name
$mail->isHTML(true);

// Initialize cURL session
$curl = curl_init();
$remoteURL = "http://localhost/SSMS/phpmailer/mail.php?username=" . urlencode($username) . "&project=" . urlencode($project). "&temp_otp=" . urlencode($temp_otp);

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $remoteURL);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and fetch content
$content = curl_exec($curl);

// Check for cURL errors
if ($content === false) {
    echo 'cURL error: ' . curl_error($curl);
    curl_close($curl);
} else {
    // Close cURL session
    curl_close($curl);

    // Email subject and body
    $mail->Subject = '=?UTF-8?B?' . base64_encode('🔒 Your 2Factor OTP') . '?=';
    $mail->Body = $content;

    // Check if the email was sent successfully
    if ($mail->send()) {
        echo 'Email sent successfully!';
    } else {
        echo 'Error: ' . $mail->ErrorInfo;
    }
}
?>
