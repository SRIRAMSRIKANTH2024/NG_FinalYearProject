<?php
session_start();
include 'db.php';

$msg = "";

if (isset($_POST['otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND otp=?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        header("Location: reset_passwd.php");
        exit();
    } else {
        $msg = "❌ Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>

<style>
<?php /* SAME STYLE */ ?>
body{margin:0;font-family:'Segoe UI';background:#0a0f1c;display:flex;justify-content:center;align-items:center;height:100vh;}
.card{background:rgba(255,255,255,0.05);padding:35px;width:400px;border-radius:20px;box-shadow:0 0 30px rgba(0,255,255,0.4);}
h2{text-align:center;color:#00f7ff;}
input{width:100%;padding:12px;margin:10px 0;border-radius:10px;border:none;background:rgba(255,255,255,0.08);color:white;}
.btn{width:100%;padding:12px;margin-top:15px;border-radius:10px;border:none;background:linear-gradient(90deg,#00f7ff,#0072ff);color:white;}
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
<h2>Verify OTP</h2>

<?php if($msg!="") echo "<div class='msg'>$msg</div>"; ?>

<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required style="width: 200px;"> 
    <button class="btn">Verify</button>
</form>

</div>
</div>

</body>
</html>