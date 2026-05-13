<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// USER-SPECIFIC COUNT
$totalResult = mysqli_query($conn, "
SELECT COUNT(*) AS total 
FROM reports 
WHERE user_id='$user_id'
");

$totalReports = mysqli_fetch_assoc($totalResult)['total'];

$limit = 100;
$remaining = $limit - $totalReports;
if($remaining < 0) $remaining = 0;


?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>

<style>
body{
    background:#0a0f1c;
    color:white;
    font-family:Segoe UI;
    margin:0;
}

/* CONTAINER */
.container{
    padding:30px;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.dashboard-btn{
    background:#FFBF00;
    color:black;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}

.dashboard-btn:hover{
    box-shadow:0 0 10px #FFBF00;
}

/* 🔥 GRID LAYOUT */
.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.05);
    padding:20px;
    border-radius:12px;
    box-shadow:0 0 15px #FFBF00;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px) scale(1.03);
    box-shadow:0 0 25px #FFBF00;
}

.card h3{
    color:#FFBF00;
    margin-bottom:10px;
}

.card p{
    color:#ccc;
    font-size:14px;
}

.card a{
    display:inline-block;
    margin-top:10px;
    color:#FFBF00;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>
   

<div class="container">
 
<!-- HEADER -->
<div class="header">
    <h1>👤 Welcome, <?php echo $_SESSION['email']; ?></h1>
    <a href="logout.php" class="dashboard-btn">🚪 Logout</a>
</div>

<!-- DASHBOARD GRID -->
<div class="dashboard">

<div class="card" onclick="window.location.href='report.php'">
<h3>📢 Report Hazard</h3>
<p>Submit new hazard reports</p>
<a href="report.php">Go →</a>
</div>

<div class="card" onclick="window.location.href='users_viewreports.php'">
<h3>📋 My Reports</h3>
<p>View your submitted reports</p>
<a href="users_viewreports.php">Go →</a>
</div>

<div class="card" onclick="window.location.href='alerts.php'">
<h3>⚠ Alerts</h3>
<p>Check latest alerts</p>
</div>

<div class="card">
<h3>📍 Nearby Hazards</h3>
<p>View hazards around your location</p>
</div>

<div class="card">
<h3>📊 Report Limit</h3>
<p>
<?php echo $totalReports . " / " . $limit; ?><br>
Remaining: <?php echo $remaining; ?>
</p>
</div>

</div>

</div>


</body>
</html>