<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// 📊 Fetch stats

// ACTIVE REPORTS
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports");
$totalReports = mysqli_fetch_assoc($totalResult)['total'];

// ARCHIVED REPORTS
$archiveResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports_archive");
$archivedReports = mysqli_fetch_assoc($archiveResult)['total'];

// TODAY REPORTS
$todayResult = mysqli_query($conn, 
"SELECT COUNT(*) AS today FROM reports WHERE DATE(report_time) = CURDATE()");
$todayReports = mysqli_fetch_assoc($todayResult)['today'];

// WEEK REPORTS
$weekResult = mysqli_query($conn, 
"SELECT COUNT(*) AS week FROM reports WHERE YEARWEEK(report_time, 1) = YEARWEEK(CURDATE(), 1)");
$weekReports = mysqli_fetch_assoc($weekResult)['week'];

// 🚫 LIMIT SYSTEM
$limit = 100;
$remaining = $limit - $totalReports;
if($remaining < 0) $remaining = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
body{
background:
linear-gradient(135deg, #0a0f1c, #12002b),
linear-gradient(45deg, rgba(255,191,0,0.08), rgba(224,176,255,0.08));
background-blend-mode: overlay;
color:white;
font-family:Segoe UI;
}

.navbar{
display:flex;
justify-content:space-between;
padding:15px 40px;
background:rgba(0,0,0,0.4);
}

.logo{
font-size:22px;
font-weight:bold;
color:#FFBF00;
}

.dashboard{
padding:30px;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card{
padding:25px;
border-radius:12px;
background:rgba(255,255,255,0.05);
border:1px solid rgba(255,255,255,0.1);
transition:0.3s;
cursor:pointer;
}

.card:hover{
transform:scale(1.05);
box-shadow:0 0 20px #FFBF00;
}

.card h3{
color:#FFBF00;
}

.stats{
display:flex;
gap:20px;
flex-wrap:wrap;
padding:20px;
}

.stat-box{
padding:25px;
border-radius:10px;
background:rgba(255,255,255,0.05);
text-align:center;
min-width:200px;
}

.stat-box h2{
color:#FFBF00;
}

.logout{
background:#FFBF00;
color:black;
padding:8px 15px;
border-radius:5px;
text-decoration:none;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
<div class="logo">Admin Panel</div>
<div>
Welcome <?php echo $_SESSION['email']; ?> |
<a href="logout.php" class="logout">Logout</a>
</div>
</div>

<!-- ADMIN ACTIONS -->
<div class="dashboard">

<div class="card" onclick="location.href='admins_viewreports.php'">
<h3>📊 View Reports</h3>
<p>See all hazard reports</p>
</div>

<div class="card" onclick="location.href='manage_users.php'">
<h3>👥 Manage Users</h3>
<p>Control system users</p>
</div>

<div class="card" onclick="window.location.href='alerts.php'">
<h3>🚨 Visit Alerts</h3>
<p>View emergency notifications</p>
</div>

<div class="card" onclick="location.href='completed_reports.php'">
<h3>📦 Archived Reports</h3>
<p>View completed history</p>
</div>

</div>

<!-- STATS -->
<div class="stats">

<div class="stat-box">
<h2><?php echo $totalReports; ?></h2>
<p>Active Reports</p>
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
<h2><?php echo $totalReports . " / " . $limit; ?></h2>
<p>Report Limit Usage</p>
<?php echo "Remaining: " . $remaining; ?>
</div>

<div class="stat-box">
<h2><?php echo $archivedReports; ?></h2>
<p>Archived Reports</p>
</div>

</div>

</body>
</html>