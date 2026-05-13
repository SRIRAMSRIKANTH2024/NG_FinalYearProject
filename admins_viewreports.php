<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// 🔥 UPDATE STATUS
if(isset($_POST['update_status'])){

    $id = $_POST['id'];
    $status = $_POST['status'];
    $contact = $_POST['contact'];

   if($status == "Completed"){
    $stmt = $conn->prepare("
        UPDATE reports 
        SET status=?, admin_contact=?, completed_time=NOW() 
        WHERE id=?
    ");
} else {
    $stmt = $conn->prepare("
        UPDATE reports 
        SET status=?, admin_contact=? 
        WHERE id=?
    ");
}
$stmt->bind_param("ssi", $status, $contact, $id);
$stmt->execute();

    // ✅ IMPORTANT: redirect after update (prevents session issues)
    header("Location: admins_viewreports.php?msg=updated");
    exit();
}

// 🔥 FETCH REPORTS (SORT BY PRIORITY)
$result = mysqli_query($conn, "
    SELECT * FROM reports 
    ORDER BY priority_score DESC, id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>

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
}

select, input{
padding:6px;
margin-top:5px;
}

button{
background:#00f7ff;
border:none;
padding:8px 15px;
margin-top:10px;
cursor:pointer;
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

.right-box{
    float: right;
    width: 200px;
    background: rgba(0, 247, 255, 0.08);
    border: 1px solid rgba(0,247,255,0.3);
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,247,255,0.4);
    margin-left: 20px;
    margin-bottom: 10px;
    margin-top: -90px;
}

.right-box label{
    font-size: 14px;
    color: #00f7ff;
}

.right-box select,
.right-box input{
    width: 100%;
    padding: 6px;
    margin-top: 5px;
    border-radius: 5px;
    border: none;
    outline: none;
}

.right-box button{
    width: 100%;
    background: #00f7ff;
    color: black;
    border: none;
    padding: 8px;
    border-radius: 6px;
    margin-top: 8px;
    cursor: pointer;
    font-weight: bold;
}

.right-box button:hover{
    box-shadow: 0 0 10px #00f7ff;
}

/* VERY IMPORTANT */
.clearfix{
    clear: both;
}
</style>
</head>

<body>

<h2>⚡ Admin Hazard Reports ⚡</h2>

<?php if(isset($_GET['msg']) && $_GET['msg']=="updated"){ ?>
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
    ✅ Hazard status updated successfully!
</div>
<?php } ?>

<a href="admin_dashboard.php" class="dashboard-btn">Dashboard</a>

<?php
if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){
?>

<div class="card">

<b>Report ID: NG-<?php echo $row['id']; ?></b><br><br>
<b>Priority:</b> <?php echo round($row['priority_score']); ?><br><br>
<b>Status:</b> <?php echo $row['status'] ?? 'Pending'; ?><br><br>

<!-- 🔥 RIGHT SIDE PANEL -->
<div class="right-box">

<form method="POST">

<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<label>Status:</label><br>
<select name="status">
    <option value="Pending">Pending</option>
    <option value="In Progress">In Progress</option>
    <option value="Completed">Completed</option>
</select>

<br><br>

<label>Contact Number:</label><br>
<input type="text" name="contact" placeholder="Enter mobile number">

<br><br>

<button name="update_status">Update</button>

</form>

</div>

<!-- LEFT CONTENT CONTINUES -->

<b>User Name:</b> <?php echo $row['name']; ?><br><br>

<b>Description:</b> <?php echo $row['description']; ?><br><br>

<b>Hazard Type:</b> <?php echo $row['hazard_type']; ?><br><br>
<br>
<br>
<b>Location:</b>
<a target="_blank"
href="https://www.google.com/maps/search/?api=1&query=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>">
View Location
</a>

<div class="map">
<iframe width="100%" height="250"
src="https://maps.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>&z=15&output=embed">
</iframe>
</div>

<br>

<?php if($row['image']!=""){ ?>
<b>Image:</b><br>
<img src="<?php echo $row['image']; ?>">
<br><br>
<?php } ?>

<?php if($row['audio']!=""){ ?>
<b>Audio:</b><br>
<audio controls>
<source src="<?php echo $row['audio']; ?>">
</audio>
<br><br>
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