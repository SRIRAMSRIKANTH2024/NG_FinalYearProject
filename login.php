<?php
session_start();
include 'db.php';

$admin_message = "";
$user_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login_input = $_POST['email'];
    $type = $_POST['type']; // admin or user
    // ✅ Get password based on type
if($type == "admin"){
    $password = $_POST['admin_password'];
} else {
    $password = $_POST['user_password'];
}
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email=? OR name=?");
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){

        $stmt->bind_result($id, $db_password, $role);
        $stmt->fetch();

        // CHECK ROLE MATCH FIRST
        if($role != $type){
            if($type == "admin"){
                $admin_message = "⚠ Admin Not Found!";
            } else {
                $user_message = "⚠ User Not Found!";
            }
        }
        else {
            // VERIFY PASSWORD
           if($type == "admin"){
            if(password_verify($password, $db_password)){

                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email ?? $login_input;
                $_SESSION['role'] = $role;

                header("Location: admin_dashboard.php");
                exit();

            }     else {
                $admin_message = "❌ Incorrect password";
                }
                }
           if($type == "user"){
            if(password_verify($password, $db_password)){

                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email ?? $login_input;
                $_SESSION['role'] = $role;
                   
                header("Location: user_dashboard.php");
                exit();
            }

            else {
                $user_message = "❌ Incorrect password";
            }
           }
        }

    } else {
        // EMAIL NOT FOUND IN DB
        if($type == "admin"){
            $admin_message = "⚠ Admin Not Found!";
        } else {
            $user_message = "⚠ User Not Found!";
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - NeighbourGuard</title>
<script src="https://accounts.google.com/gsi/client" async defer></script>
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
     margin: 0;
  height: 100vh;
  background: 
    radial-gradient(circle at 20% 20%, rgba(255, 0, 100, 0.4), transparent 40%),
    radial-gradient(circle at 80% 30%, rgba(0, 100, 255, 0.4), transparent 40%),
    radial-gradient(circle at 50% 80%, rgba(255, 150, 0, 0.3), transparent 40%),
    linear-gradient(135deg, #2b0a3d, #0b1a3a);
  background-blend-mode: screen;
}
@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
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
    10%{opacity:0.9;}
    20%{opacity:0;}
    30%{opacity:0.6;}
    100%{opacity:0;}
}

.card{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(20px);
    padding:35px;
    width:400px;
    border-radius:20px;
    box-shadow:0 0 30px rgba(0,255,255,0.4);
    animation:fadeIn 1s ease;
}

/* NEW CONTAINER */
.main-container{
    display:flex;
    gap:50px;
    justify-content:center;
     align-items:stretch;
    width:60%;
}

/* RESPONSIVE (mobile) */
@media(max-width:900px){
    .main-container{
        flex-direction:column;
    }
}

.divider{
    color:#ff2fa0;

}

h2{
    font-family: "Times New Roman";
    text-align:center;
    color:#00f7ff;
    text-shadow:0 0 20px #00f7ff;
    min-height:40px;
    margin-bottom:25px;
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
    font-size:15px;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
    box-shadow:0 0 25px #00f7ff;
}
.divider{
    text-align:center;
    color:#00f7ff;
    text-shadow:0 0 20px #00f7ff;
 
}


a{
    color:#00f7ff;
    text-decoration:none;
}

p{
    text-align:center;
    color:white;
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
@keyframes fadeIn{
    from{opacity:0;transform:translateY(40px);}
    to{opacity:1;transform:translateY(0);}
}
.password-box {
  position: relative;
}

.password-box input {
  width: 270px;
}

.password-box i {
  position: absolute;
  right: 35px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: gray;
}
/* Background floating bubbles */
.bubble{
    position:absolute;
    bottom:-100px;
    width:40px;
    height:40px;
    background:rgba(255,255,255,0.2);
    border-radius:50%;
    animation:rise 10s infinite ease-in;
}

@keyframes rise{
    0%{transform:translateY(0) scale(1);opacity:0.5;}
    100%{transform:translateY(-1200px) scale(0.5);opacity:0;}
}

@keyframes fadeIn{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

.input::placeholder
{
  color: white ;

}

.dashboard-btn{
position:absolute;
top:20px;
right:100px;
background:white;
color:#1e3a5f;
padding:10px 18px;
border-radius:6px;
text-decoration:none;
font-weight:bold;
}

.dashboard-btn:hover{
box-shadow:0 0 10px cyan;
transition:0.3s;
}
</style>

</head>
<body>

<div class="flash" id="flash"></div>
<a href="index.php" class="dashboard-btn">Home</a>

<div class="main-container">

       <!-- ADMIN LOGIN -->
    <div class="card">
        <h2>⚡ ADMIN LOGIN ⚡</h2>
         <?php if($admin_message != ""){ ?>
<div class="msg"><?php echo $admin_message; ?></div>
<?php } ?>
        <form method="POST">
            <input type="hidden" name="type" value="admin">
            <input class="input" type="text" name="email" placeholder="Admin Email OR Username" required style="width: 270px;"> 
           <div class="password-box">
            <input class="input" type="password" id="admin_password" name="admin_password" placeholder="Enter password">
            <i class="fa-solid fa-eye" onclick="admin_togglePassword()" id="admin_eyeIcon"></i>
            </div>
            <button class="btn" type="submit" style="width: 290px;">Admin Sign In</button>
        </form><br>
       <div class="divider">ADMIN ACCESS</div>
       <p style="color: red;">This login is for admins only, not for regular users.</p>
<p style="text-align:center; color:yellow;">Restricted Panel</p>
    </div >

    <!-- USER LOGIN -->
    <div class="card">
        <h2>⚡ USER LOGIN ⚡</h2>
 <?php if($user_message != ""){ ?>
<div class="msg"><?php echo $user_message; ?></div>
<?php } ?>
        <form method="POST">
            <input type="hidden" name="type" value="user">
            <input class="input" type="text" name="email" placeholder="Email OR Username" required style="width:270px ;">
            <div class="password-box">
            <input class="input" type="password" id="user_password" name="user_password" placeholder="Enter password">
            <i class="fa-solid fa-eye" onclick="user_togglePassword()" id="eyeIcon"></i>
            </div>
            <button class="btn" type="submit" style="width:290px;">Sign In</button>
        </form>
        <a href="forgot_passwd.php">Forgot password?</a>
        <div class="divider">OR</div>

        <div id="g_id_onload"
            data-client_id="858593098746-gq01uc7v1hhujcglkbf607t8m6gqv5nj.apps.googleusercontent.com"
            data-callback="handleCredentialResponse">
        </div>

        <div class="g_id_signin" style="width: 290px;"></div>

        <p>No account? <a href="register.php">Sign Up</a></p>
    </div>



</div>

<script>
function createBubbles(){
    for(let i=0;i<15;i++){
        let bubble=document.createElement('div');
        bubble.classList.add('bubble');
        bubble.style.left=Math.random()*100+'%';
        bubble.style.animationDuration=(5+Math.random()*5)+'s';
        bubble.style.width=bubble.style.height=(20+Math.random()*40)+'px';
        document.body.appendChild(bubble);
    }
}
createBubbles();


// Lightning effect
window.onload = function(){
    document.getElementById("flash").style.animation="lightning 1s ease";
}

// Typing animation
const text="⚡NEIGHBOURGUARD SYSTEM⚡";
let i=0;

function type(){
    if(i<text.length){
        document.getElementById("typing").innerHTML+=text.charAt(i);
        i++;
        setTimeout(type,70);
    }
}
type();

function handleLogin(event){
    event.preventDefault();
    const email = event.target.email.value;
    alert("Login attempt: " + email);
    localStorage.setItem("user", email);
    window.location.href="user_dashboard.php";
}

// GOOGLE LOGIN FUNCTION
function handleCredentialResponse(response){

    const responsePayload = JSON.parse(atob(response.credential.split('.')[1]));

    // create form dynamically
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "google_login.php";

    // add hidden inputs
    form.innerHTML = `
        <input type="hidden" name="name" value="${responsePayload.name}">
        <input type="hidden" name="email" value="${responsePayload.email}">
        <input type="hidden" name="sub" value="${responsePayload.sub}">
    `;

    document.body.appendChild(form);
    form.submit(); // ✅ normal POST (session works perfectly)
}

// PASSWORD TOGGLE
function user_togglePassword() {
  const pwd = document.getElementById("user_password");
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

function admin_togglePassword() {
  const pwd = document.getElementById("admin_password");
  const icon = document.getElementById("admin_eyeIcon");
   
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
