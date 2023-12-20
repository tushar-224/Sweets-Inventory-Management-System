<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile QR Code Scanner</title>
    <!-- Include Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
     .circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 30px;
            animation: pulse 1s infinite; /* Pulse animation */
        }
		
		  html{
            background-Color:#4f1eab;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
		
		#qr-video {
			
			border-radius:20px;
			border:5px solid white;
		}

    </style>
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-half">
                    <!-- Create a video element for the camera stream -->
                    <div id="scanner" class="">
                        <center><h1 class="title has-text-white">TD Sweets</h1></center>
                        <br>
						
                        <video id="qr-video" autoplay></video>
                        <!-- Display the QR code value -->
                    </div>
                    <!-- Payment details -->
                    <div id="payment-details" class="box is-hidden">
                       <center>
                       <h2 class="subtitle">Make a Payment</h2>
                        <br>
                        <div  class="circle has-background-primary">
                        ₹<span id="payment-amount">0</span>
                        </div>
                        <br>
                        <br>
                        <h1 class="title" id="payment-user"></h1>
                        <button style="width:100%;" id="pay-button" class="button is-primary">Pay</button>
                       </center>
                    </div>

                    <div id="payment-success" class="box is-hidden">
                       <center>
                       <h2 class="subtitle">Payment Successful</h2>
                        <br>
                        <div  class="circle">
                        <span style="font-size:150px;" id="">✔️</span>
                        </div>
                        <br>
                        <br>
                        <button style="width:100%;" id="spayment-amount" class="button is-dark"></button>
                       </center>
                    </div>
                    <div id="payment-failed" class="box is-hidden">
                       <center>
                       <h2 class="subtitle">Payment Failed, Try Again</h2>
                        <br>
                        <div  class="circle">
                        <span style="font-size:150px;" id="">❌</span>
                        </div>
                        <br>
                        <br>
                       </center>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript to handle QR code scanning and payment details display -->
	
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>


    <script>
const video = document.getElementById('qr-video');
const canvasElement = document.createElement('canvas');
const canvas = canvasElement.getContext('2d');
const paymentDetails = document.getElementById('payment-details');
const paymentAmount = document.getElementById('payment-amount');
 const spaymentAmount = document.getElementById('spayment-amount');
const paymentUser = document.getElementById('payment-user');
const payButton = document.getElementById('pay-button');

if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    alert('Your browser does not support accessing the camera.');
} else {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function (stream) {
            video.srcObject = stream;

            // Listen for video play to get the video dimensions
            video.addEventListener('play', () => {
                canvasElement.width = video.videoWidth;
                canvasElement.height = video.videoHeight;
            });

            // Continuously capture video frames and decode QR codes
            const captureFrames = () => {
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    // Assuming the QR code content is in the format "upi://pay?pa=user@upi&pn=Name&am=Amount"
                    const params = new URLSearchParams(code.data.slice(8));
                    const amount = params.get('am');
                    const user = params.get('pn');
					const paycode = params.get('tn');

                    // Display payment details
                    paymentAmount.textContent = `${amount}`;
					 spaymentAmount.textContent = `₹${amount}`;
                    paymentUser.textContent = `${user}`;
                    paymentDetails.classList.remove('is-hidden');
                    $("#scanner").addClass("is-hidden");


					//	$("#mera").text(`Actions.php?a=set_payment_status&payment_id=${paycode}`);
                    // Handle the payment button click (implement your payment logic here)
                    payButton.addEventListener('click', function () {
                        
                            $.get(`Actions.php?a=set_payment_status&payment_id=${paycode}`,function(data){
                                data =JSON.parse(data);
                                if(data.status==='success'){
                                  $("#payment-details").addClass("is-hidden");
                                  $("#payment-success").removeClass("is-hidden");
								  video.remove();
                                } else {
                                    $("#payment-details").addClass("is-hidden");
                                  $("#payment-failed").removeClass("is-hidden");
								   video.remove();
                                }
                            });

                    });
                }

                requestAnimationFrame(captureFrames);
            };

            // Start capturing frames
            captureFrames();
        })
        .catch(function (error) {
            console.error('Error accessing the camera:', error);
        });
}

    </script>
</body>
</html>
