<?php
session_start();
include "db.php";


if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// 📦 FETCH ARCHIVED REPORTS
$result = mysqli_query($conn, "
SELECT * FROM reports_archive 
ORDER BY report_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Archived Reports</title>

<style>
body{
font-family:Arial;
background:#0a0f1c;
color:white;
padding:40px;
}

h2{
text-align:center;
color:#00f7ff;
margin-bottom:30px;
}

.card{
background:#111;
padding:20px;
margin-bottom:25px;
border-radius:10px;
box-shadow:0 0 10px #00f7ff;
}

.card b{
color:#00f7ff;
}

img{
width:250px;
border-radius:10px;
margin-top:10px;
max-width:100%;
}

audio{
margin-top:10px;
width:100%;
max-width:400px;
}

.map{
margin-top:10px;
border-radius:10px;
overflow:hidden;
}

.empty{
text-align:center;
color:#aaa;
padding:40px;
font-size:18px;
}

.dashboard-btn{
position:absolute;
top:20px;
right:100px;
background:#00f7ff;
color:black;
padding:10px 18px;
border-radius:6px;
text-decoration:none;
font-weight:bold;
}
</style>
</head>

<body>

<h2>📦 Archived / Completed Reports</h2>

<a href="admin_dashboard.php" class="dashboard-btn">Dashboard</a>

<?php
if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){
?>

<div class="card">

<b>Report ID:</b> NG-<?php echo $row['id']; ?><br><br>
<b>Status:</b> <?php echo $row['status']; ?><br><br>
<b>Resolved At:</b> 
<?php $date = new DateTime($row['report_time']); 
echo $date->format('l, d M Y - h:i A'); ?><br><br>

<b>User:</b> <?php echo $row['name']; ?><br><br>

<b>Description:</b> <?php echo $row['description']; ?><br><br>

<b>Hazard Type:</b> <?php echo $row['hazard_type']; ?><br><br>

<b>Admin Contact:</b> <?php echo $row['admin_contact']; ?><br><br>

<!-- 📍 LOCATION -->
<b>Location:</b>
<a target="_blank"
href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>">
View Exact Location
</a>

<br><br>

<div class="map">
<iframe
width="100%"
height="250"
style="border:0"
loading="lazy"
allowfullscreen
src="https://maps.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>&z=15&output=embed">
</iframe>
</div>

<br>

<?php if(!empty($row['image'])){ ?>
<b>Image Evidence:</b><br>
<img src="<?php echo $row['image']; ?>">
<br><br>
<?php } ?>

<?php if(!empty($row['audio'])){ ?>
<b>Voice Evidence:</b><br>
<audio controls>
<source src="<?php echo $row['audio']; ?>">
</audio>
<?php } ?>

</div>

<?php
}
}else{
echo "<div class='empty'>No Archived Reports Found</div>";
}
?>

</body>
</html>