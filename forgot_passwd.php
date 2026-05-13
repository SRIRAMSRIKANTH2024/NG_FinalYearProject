<?php
session_start();
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$msg = ""; // ✅ FIX: define message

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // ✅ safer query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $otp = rand(100000, 999999);

        $conn->query("UPDATE users SET otp='$otp' WHERE email='$email'");

        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'sri223770@gmail.com';
            $mail->Password = 'vxilfvecdvqurctw';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sri223770@gmail.com', 'Neighbourguard');
            $mail->addAddress($email);

            $mail->Subject = 'OTP Verification';
            $mail->Body = "Your OTP is: $otp";

            $mail->send();

            // ✅ SHOW IN YOUR STYLE BOX
           $msg = "✅ OTP sent to " . $email . "\nRedirecting ...";

            // ✅ redirect after short delay
            header("refresh:3;url=verify_otp.php");

        } catch (Exception $e) {
            $msg = "❌ Mailer Error: " . $mail->ErrorInfo;
        }

    } else {
        $msg = "⚠ Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>

<style>
/* YOUR STYLE */
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#0a0f1c;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(20px);
    padding:35px;
    width:400px;
    border-radius:20px;
    box-shadow:0 0 30px rgba(0,255,255,0.4);
}
h2{
    text-align:center;
    color:#00f7ff;
}
input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:none;
    background:rgba(255,255,255,0.08);
    color:white;
}
.btn{
    width:100%;
    padding:12px;
    margin-top:15px;
    border-radius:10px;
    border:none;
    background:linear-gradient(90deg,#00f7ff,#0072ff);
    color:white;
    cursor:pointer;
}
.msg{
    background:rgba(0,247,255,0.15);
    border-left:5px solid #00f7ff;
    padding:14px;
    margin-bottom:15px;
    border-radius:10px;
    text-align:center;
    color:white;
    font-weight:bold;
    box-shadow:0 0 15px rgba(0,247,255,0.3);
}
.main-container{
    display:flex;
    gap:50px;
    justify-content:center;
     align-items:stretch;
    width: 240px;
}

/* RESPONSIVE (mobile) */
@media(max-width:900px){
    .main-container{
        flex-direction:column;
    }
}
</style>

</head>
<body>
<div class="main-container">
<div class="card">
<h2>Forgot Password</h2>

<?php if($msg != ""){ ?>
<div class="msg"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter Email" required style="width: 260px">
    <button class="btn" style="width: 290px;">Send OTP</button>
</form>

</div>
</div>

</body>
</html>