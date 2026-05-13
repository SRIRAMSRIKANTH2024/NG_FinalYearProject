<?php
session_start();
include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Live Alerts</title>

<style>
body{
    background: linear-gradient(135deg,#0a0f1c,#1a0033);
    font-family: Segoe UI;
    color:white;
    margin:0;
    padding:20px;
}

h2{
    color:#ff4d9d;
}

.alert-box{
    background: rgba(255,255,255,0.05);
    border-left: 5px solid #ff4d9d;
    padding:15px;
    margin:15px 0;
    border-radius:10px;
    box-shadow:0 0 15px rgba(255,77,157,0.5);
    transition:0.3s;
}

.alert-box:hover{
    transform: scale(1.02);
    box-shadow:0 0 25px rgba(255,77,157,0.8);
}

.time{
    font-size:12px;
    color:#ccc;
    margin-top:5px;
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

</style>

</head>

<body>

<h2>🚨 Recent Alerts</h2>

<?php
$sql = "SELECT alert_message, report_time FROM reports 
        WHERE alert_message != '' 
        ORDER BY report_time DESC";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

    while($row = mysqli_fetch_assoc($result)){

        $msg = $row['alert_message'];
        $time = $row['report_time'];
        $day = date("l", strtotime($time));

        // ICON DETECTION
        $icon = "⚠️";

        if(stripos($msg, "fire") !== false){
            $icon = "🔥";
        } elseif(stripos($msg, "flood") !== false){
            $icon = "🌊";
        } elseif(stripos($msg, "accident") !== false){
            $icon = "🚗";
        } elseif(stripos($msg, "damage") !== false){
            $icon = "🚧";
        }
?>

<?php
$dashboard_link = ($_SESSION['role'] == 'admin') 
    ? 'admin_dashboard.php' 
    : 'user_dashboard.php';
?>

<a href="<?php echo $dashboard_link; ?>" class="dashboard-btn">Dashboard</a>

<div class="alert-box">
    <strong><?php echo $icon . " " . $msg; ?></strong>
    <div class="time">
        <?php echo $time . " (" . $day . ")"; ?>
    </div>
</div>

<?php
    }

} else {
    echo "<p>❌ No alerts found</p>";
}
?>

</body>
</html>