<?php
session_start();    
include "db.php";


// Total Reports
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalReports = $totalRow['total'];

// Today Reports
$todayResult = mysqli_query($conn, 
"SELECT COUNT(*) AS today FROM reports WHERE DATE(report_time) = CURDATE()");
$todayRow = mysqli_fetch_assoc($todayResult);
$todayReports = $todayRow['today'];

// This Week Reports
$weekResult = mysqli_query($conn, 
"SELECT COUNT(*) AS week FROM reports WHERE YEARWEEK(report_time, 1) = YEARWEEK(CURDATE(), 1)");
$weekRow = mysqli_fetch_assoc($weekResult);
$weekReports = $weekRow['week'];

//Report Limit Check
$maxReports = 100;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>NeighbourGuard Dashboard</title>

<style>
    *{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI, sans-serif;
}

/* 🌈 BACKGROUND (Neon Gradient) */
body{
background:
linear-gradient(135deg, #0a0f1c, #12002b),
linear-gradient(45deg, rgba(255,0,100,0.08), rgba(90,0,255,0.08));
background-blend-mode: overlay;
color:white;
}
/* 🧊 GLASS EFFECT */
.glass{
background: rgba(255,255,255,0.05);
backdrop-filter: blur(12px);
border: 1px solid rgba(255,255,255,0.1);
box-shadow: 0 0 20px rgba(155,0,255,0.3);
}

/* NAVBAR */
.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 40px;
background: rgba(0,0,0,0.3);
backdrop-filter: blur(10px);
border-bottom:1px solid rgba(255,255,255,0.1);
}

.logo{
font-size:22px;
font-weight:bold;
color:#e0aaff;
text-shadow:0 0 10px #9b00ff;
}

.nav-links{
display:flex;
gap:20px;
align-items:center;
}

.nav-links a{
text-decoration:none;
color:#ddd;
transition:0.3s;
}

.nav-links a:hover{
color:#ff2fa0;
text-shadow:0 0 8px #ff2fa0;
}

.login-btn{
background:linear-gradient(45deg,#ff2fa0,#5a00ff);
padding:8px 16px;
border-radius:6px;
color:white;
}

/* HERO */
.hero{
padding:40px;
text-align:center;
}

.hero h1{
font-size:32px;
color:#e0aaff;
text-shadow:0 0 15px #9b00ff;
}

.hero p{
margin-top:10px;
color:#ccc;
}

/* DASHBOARD */
.dashboard{
padding:30px 40px;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card{
padding:25px;
border-radius:15px;
cursor:pointer;
transition:0.3s;
background: rgba(255,255,255,0.05);
backdrop-filter: blur(10px);
border:1px solid rgba(255,255,255,0.1);
box-shadow:0 0 15px rgba(155,0,255,0.3);
}

.card:hover{
transform:translateY(-8px) scale(1.02);
box-shadow:0 0 30px #ff2fa0;
}

.card h3{
color:#ff2fa0;
margin-bottom:10px;
}

.card p{
color:#ccc;
font-size:14px;
}

/* STATS */
.stats{
display:flex;
flex-wrap:wrap;
justify-content:center;
gap:20px;
margin-top:30px;
}

.stat-box{
padding:30px 50px;
min-width:220px;
border-radius:15px;
text-align:center;
transition:0.3s;

/* NEW DESIGN */
background: linear-gradient(135deg, rgba(90,0,255,0.25), rgba(255,47,160,0.15));
border:1px solid rgba(255,255,255,0.15);

box-shadow: 
0 4px 20px rgba(0,0,0,0.3),
0 0 15px rgba(155,0,255,0.3);
}

.stat-box h2{
font-size:36px;
color:#ffffff;
text-shadow:0 0 8px rgba(255,255,255,0.3);
}

.stat-box p{
font-size:14px;
color:#ddd;
letter-spacing:1px;
}

.stat-box:hover{
transform:translateY(-5px) scale(1.03);
box-shadow: 
0 8px 30px rgba(0,0,0,0.4),
0 0 25px rgba(255,47,160,0.4);
}


/* ALERTS */
.alerts{
padding:30px 40px;
}

.alerts h2{
color:#e0aaff;
margin-bottom:10px;
}

.alert-item{
padding:15px;
margin-top:10px;
border-left:4px solid #ff2fa0;
border-radius:8px;
background: rgba(255,255,255,0.05);
backdrop-filter: blur(10px);
box-shadow:0 0 10px rgba(255,47,160,0.3);
}

/* FOOTER */
footer{
margin-top:40px;
padding:20px;
text-align:center;
background: rgba(0,0,0,0.4);
color:#ccc;
border-top:1px solid rgba(255,255,255,0.1);
}
.msg{
     background:rgba(0,247,255,0.15);
    border-left:5px solid #00f7ff;
    padding:14px;
    margin-bottom:15px;
    border-radius:10px;
    text-align:center;
    color:red;
    font-weight:bold;
    box-shadow:0 0 15px rgba(0,247,255,0.3);
}
</style>
</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

<div class="logo">
NeighbourGuard
</div>

<div class="nav-links">

<a href="#">Reports</a>
<a href="#">Alerts</a>
<a href="#">Community</a>

<a href="login.php" class="login-btn">Login</a>

</div>
</div>


<!-- HERO -->
<div id="topMessage" class="msg" style="display:none;"></div>
<div class="hero">
<h1>Smart Hazard Reporting & Response System</h1>
<p>Helping communities quickly report hazards and respond to emergencies.</p>
</div>


<!-- QUICK ACTIONS -->

<div class="dashboard">

<div class="card" onclick="checkLogin_hazard()">
<h3>Report Hazard</h3>
<p>Submit hazards like fire, road damage, floods or other emergencies.</p>
</div>

<div class="card" onclick="CheckLogin_viewReports()">
<h3>View Reports</h3>
<p>Check hazards reported in your neighbourhood.</p>
</div>

<div class="card">
<h3>Live Alerts</h3>
<p>Receive real-time emergency alerts.</p>
</div>

<div class="card">
<h3>Hazard Map</h3>
<p>View hazard locations on an interactive map.</p>
</div>

</div>


<!-- STATS -->
<div class="stats">

<div class="stat-box">
<h2><?php echo $totalReports; ?></h2>
<p>Total Reports</p>
</div>

<div class="stat-box">
<h2><?php echo $todayReports; ?></h2>
<p>Today Reports</p>
</div>

<div class="stat-box">
<h2><?php echo $weekReports; ?></h2>
<p>This Week</p>
</div>

<div class="stat-box">
<h2>--</h2>
<p>Location Wise</p>
</div>

<div class="stat-box">
<h2 style="
<?php 
if($totalReports>=100) echo 'color:red;';
else if($totalReports>=80) echo 'color:orange;';
else echo 'color:lightgreen;';
?>
">
<?php echo $totalReports . " / " . $maxReports; ?>
</h2>
<p>Report Capacity</p>
</div>

</div>

<!-- RECENT ALERTS -->

<div class="alerts">

<h2>Recent Alerts</h2>

<div class="alert-item">
⚠ Flood reported near River Road – Response team notified.
</div>

<div class="alert-item">
🔥 Fire hazard reported in Market Street.
</div>

<div class="alert-item">
🚧 Road damage reported near Central Park.
</div>

</div>


<footer>
© 2026 NeighbourGuard | Smart Hazard Reporting System
</footer>


<script>

// example JS animation

function navigate(){
alert("Link this card to your hazard report page");
}

// auto counter animation

function counter(id,start,end,duration){
let obj=document.getElementById(id);
let range=end-start;
let step=range/(duration/20);
let current=start;

let timer=setInterval(()=>{
current+=step;
obj.innerText=Math.floor(current);

if(current>=end){
clearInterval(timer);
obj.innerText=end;
}

},20);
}

counter("reports",0,128,1000);
counter("active",0,32,1000);
counter("resolved",0,96,1000);

// CHECK LOGIN BEFORE REPORTING
function checkLogin_hazard(){

    var isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>;

    if(!isLoggedIn){

        let msgBox = document.getElementById("topMessage");
        msgBox.style.display = "block";
        msgBox.innerHTML = "⚠ Please login for Reporting Hazards!";

        // auto hide after 3 sec
        setTimeout(() => {
            msgBox.style.display = "none";
        }, 3000);

    } else {
        window.location.href = "index.php";
    }
}
// CHECK LOGIN BEFORE VIEWING REPORTS
function CheckLogin_viewReports(){

    var isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>;

    if(!isLoggedIn){

        let msgBox = document.getElementById("topMessage");
        msgBox.style.display = "block";
        msgBox.innerHTML = "⚠ Please login for Viewing Reports!";

        // auto hide after 3 sec
        setTimeout(() => {
            msgBox.style.display = "none";
        }, 3000);

    } else {
        window.location.href = "index.php";
    }

}
</script>

</body>
</html>