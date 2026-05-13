<?php
session_start();
include "db.php";
include "auto_cleanup.php";
// 🔐 LOGIN CHECK
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// USER → only their reports
$result = mysqli_query($conn, "
SELECT * FROM reports 
WHERE user_id='$user_id' 
ORDER BY id DESC
");


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>NeighbourGuard Reports</title>

<style>

body{
font-family:Arial;
background:#0a0f1c;
color:white;
padding:40px;
}

h2{
text-align:center;
color:cyan;
margin-bottom:40px;
font-size:28px;
}

.card{
background:#111;
padding:20px;
margin-bottom:25px;
border-radius:10px;
box-shadow:0 0 15px cyan;
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
padding:40px;
color:#aaa;
font-size:18px;
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

/* 🔥 RIGHT SIDE STATUS BOX */
.right-box{
float:right;
width:240px;
background:rgba(0,247,255,0.08);
padding:15px;
border-radius:10px;
box-shadow:0 0 10px cyan;
margin-left:15px;
}

.right-box b{
color:#00f7ff;
}

.right-box button{
width:100%;
background:#00f7ff;
border:none;
padding:8px;
margin-top:10px;
cursor:pointer;
font-weight:bold;
}

.right-box button:hover{
box-shadow:0 0 10px cyan;
}

/* IMPORTANT */
.clearfix{
clear:both;
}

</style>
</head>

<body>

<h2>⚡NeighbourGuard Crime Reports⚡</h2>
<?php if (isset($_GET['msg']) && $_GET['msg'] == "confirmed") { ?>
<div style="
    background:rgba(0,255,0,0.1);
    border-left:5px solid lime;
    padding:12px;
    margin-bottom:20px;
    border-radius:8px;
    text-align:center;
    color:lightgreen;
    font-weight:bold;
">
    ✅ Report saved as resolved. Thank you for your contribution!
</div>
<?php } ?>

<a href="user_dashboard.php" class="dashboard-btn">Dashboard</a>

<?php
if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){
?>

<div class="card">

<b>Report ID: NG Report No - <?php echo $row['id']; ?> | 
Report Time: <?php echo $row['report_time']; ?></b><br><br>

<!-- 🔥 RIGHT SIDE PANEL -->
<div class="right-box">

<b>Hazard Status:</b>
<?php echo $row['status'] ?? 'Pending'; ?><br><br>

<b>Admin Contact:</b>
<?php echo  !empty($row['admin_contact']) ? $row['admin_contact'] : "Not assigned"; ?>

<br><br>

<?php if($row['status'] == "Completed"){ ?>

<form method="POST" action="confirm_delete.php">
<input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
<button>✔ OK (Resolved)</button>
</form>

<?php } ?>

</div>

<b>User Name:</b> <?php echo $row['name']; ?><br><br>

<b>Hazard Message:</b> <?php echo $row['description']; ?><br><br>

<b>Hazard Type:</b> <?php echo $row['hazard_type']; ?><br><br>

<b>Location:</b>
<a target="_blank"
href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>">
View Exact Street Location
</a>

<br><br>

<b>Location Map:</b>

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

<?php if($row['image']!=""){ ?>
<b>Image Evidence:</b><br>
<img src="<?php echo $row['image']; ?>">
<br><br>
<?php } ?>

<?php if($row['audio']!=""){ ?>
<b>Voice Evidence:</b><br>
<audio controls>
<source src="<?php echo $row['audio']; ?>">
</audio>
<?php } ?>

<div class="clearfix"></div>

</div>

<?php
}
}else{
echo "<div class='empty'>No Reports Found</div>";
}
?>

</body>
</html>