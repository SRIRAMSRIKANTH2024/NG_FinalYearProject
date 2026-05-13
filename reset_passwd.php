<?php
session_start();
include 'db.php';
$msg = "";
$redirect = false; // ✅ add this
if (isset($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['email'];

    $conn->query("UPDATE users SET password='$password', otp=NULL WHERE email='$email'");

    $msg = "Password updated successfully! Redirecting...";
     $redirect = true; // ✅ trigger redirect
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Reset Password</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI';
    background:#0a0f1c;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    width: 100%;
}
.card{background:rgba(255,255,255,0.05);padding:35px;width:400px;border-radius:20px;box-shadow:0 0 30px rgba(0,255,255,0.4);}
h2{text-align:center;color:#00f7ff;}
input{width:100%;padding:12px;margin:10px 0;border-radius:10px;border:none;background:rgba(255,255,255,0.08);color:white;}
.btn{width:100%;padding:12px;margin-top:15px;border-radius:10px;border:none;background:linear-gradient(90deg,#00f7ff,#0072ff);color:white;}
.msg
{
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
    width: 350px;
}

/* RESPONSIVE (mobile) */
@media(max-width:900px){
    .main-container{
        flex-direction:column;
    }
}
.passwd i{

  position: absolute;
  left:790px;
  top: 51%;
  transform: translateY(-50%);
  cursor: pointer;
  color: gray;
}

</style>

</head>
<body>
<div class="main-container">
<div class="card">
<h2>Reset Password</h2>

<?php if($msg!="") echo "<div class='msg'>$msg</div>"; ?>

<form method="POST">
    <div class="passwd">
    <input type="password" id="password"  name="password" placeholder="New Password" required style="width: 250px;">
    <i class="fa-solid fa-eye" onclick="togglePassword()" id="eyeIcon"></i>
    </div>
    <button class="btn">Reset Password</button>
    
</form>
</div>
</div>
</div>

<script>
<?php if($redirect){ ?>
    setTimeout(function(){
        window.location.href = "login.php";
    }, 3000); // 3 seconds
<?php } ?>

function togglePassword() {
  const pwd = document.getElementById("password");
  const icon = document.getElementById("eyeIcon");
   
  if (pwd.type === "password") {
    pwd.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    pwd.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
</script>
</body>
</html>