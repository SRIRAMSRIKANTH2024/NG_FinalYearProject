<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s",$email);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $message = "⚠ Email already registered!";
    }
    else{

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $message = "✅ User registered successfully! Redirecting to login page...";
            $redirect = true;
        } else {
            $message = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
}
?>



<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - NeighbourGuard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>

body{
margin:0;
font-family:'Segoe UI',sans-serif;
background:#0a0f1c;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
overflow:hidden;
}

/* Lightning flash */

.flash{
position:fixed;
width:100%;
height:100%;
background:white;
opacity:0;
pointer-events:none;
}

@keyframes lightning{
0%{opacity:0;}
10%{opacity:0.8;}
20%{opacity:0;}
30%{opacity:0.6;}
100%{opacity:0;}
}

/* Card */

.card{
background:rgba(255,255,255,0.05);
backdrop-filter:blur(20px);
padding:35px;
width:350px;
border-radius:20px;
box-shadow:0 0 30px rgba(0,255,255,0.4);
animation:fadeIn 1s ease;
}

/* Title */

h2{
text-align:center;
color:#00f7ff;
text-shadow:0 0 20px #00f7ff;
min-height:40px;
margin-bottom:25px;
}

/* Inputs */

input{
width:290px;;
padding:12px;
margin:10px 0;
margin-left: 35px;
border-radius:10px;
border:none;
background:rgba(255,255,255,0.08);
color:white;
font-size:14px;
box-sizing:border-box;
}

/* Button */

.btn{
width: 290px;
padding:12px;
margin-top:15px;
margin-left: 35px;
border-radius:10px;
border:none;
background:linear-gradient(90deg,#00f7ff,#0072ff);
color:white;
font-size:16px;
cursor:pointer;
transition:0.3s;
}

.btn:hover{
transform:scale(1.05);
box-shadow:0 0 25px #00f7ff;
}

/* Links */

a{
color:#00f7ff;
text-decoration:none;
}

p{
text-align:center;
color:white;
margin-top:15px;
}
.msg{
background:rgba(0,247,255,0.15);
border-left:4px solid #00f7ff;
padding:12px;
margin-bottom:15px;
border-radius:8px;
text-align:center;
color:white;
font-weight:bold;
}
/* Animation */

@keyframes fadeIn{
from{opacity:0;transform:translateY(40px);}
to{opacity:1;transform:translateY(0);}
}

.password-box {
  position: relative;
}



.password-box i {
  position: absolute;
  right: 40px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: gray;
}
</style>
</head>

<body>


<div class="flash" id="flash"></div>

<div class="card">
<?php if($message != ""){ ?>
<div class="msg"><?php echo $message; ?></div>
<?php } ?>

<h2 id="typing"></h2>

<form method="POST">

<input type="text" name="name" placeholder="User Name" required>
<input type="email" name="email" placeholder="Email" required>
<div class="password-box">
            <input type="password" id="password" name="password" placeholder="Enter password">
            <i class="fa-solid fa-eye" onclick="togglePassword()" id="eyeIcon"></i>
            </div>

<button class="btn" type="submit" name="register">REGISTER</button>

</form>



<p>Already have an account? <a href="login.php">Login</a></p>

</div>

<script>

// Lightning effect
window.onload=function(){
document.getElementById("flash").style.animation="lightning 1s ease";
}

// Typing animation
const text="⚡ CREATE AN ACCOUNT ⚡";
let i=0;

function type(){
if(i<text.length){
document.getElementById("typing").innerHTML+=text.charAt(i);
i++;
setTimeout(type,70);
}
}

type();

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
<script>

<?php if($redirect){ ?>
    setTimeout(function(){
        window.location.href = "login.php";
    }, 3000); // 3 seconds
<?php } 
?>
</script>

</body>
</html>