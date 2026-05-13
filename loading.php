<?php
session_start();

$uploadDir = "uploads/";
if(!is_dir($uploadDir)){
    mkdir($uploadDir);
}

$_SESSION['user_id'] = $_SESSION['user_id'] ?? null;
$_SESSION['name']=$_POST['name'] ?? null;
$_SESSION['latitude'] = $_POST['latitude'] ?? "";
$_SESSION['longitude'] = $_POST['longitude'] ?? "";
// TEXT DATA
$_SESSION['description'] = $_POST['description'];
$_SESSION['emergency'] = $_POST['emergency'] ?? 0;

// IMAGE UPLOAD
if(!empty($_FILES['image']['name'])){
    $imagePath = $uploadDir . time() . "_" . $_FILES['image']['name'];

    if(move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)){
        $_SESSION['imagePath'] = $imagePath;
    } else {
        $_SESSION['imagePath'] = "";
    }
}

// AUDIO UPLOAD
$audioPath = "";
if(!empty($_FILES['audio']['name'])){
    $audioPath = $uploadDir . time() . "_" . $_FILES['audio']['name'];
    move_uploaded_file($_FILES['audio']['tmp_name'], $audioPath);
}
$_SESSION['audioPath'] = $audioPath;
?>

<!DOCTYPE html>
<html>
<head>
<title>Processing Report</title>

<style>
body{
    background:#0a0f1c;
    color:white;
    font-family:Segoe UI;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    text-align:center;
}

.box{
    padding:40px;
    border-radius:15px;
    background:rgba(255,255,255,0.05);
    box-shadow:0 0 25px #FFBF00;
}

/* LOADER */
.loader{
    border:6px solid #333;
    border-top:6px solid #FFBF00;
    border-radius:50%;
    width:60px;
    height:60px;
    animation:spin 1s linear infinite;
    margin:20px auto;
}

@keyframes spin{
    0%{ transform:rotate(0deg);}
    100%{ transform:rotate(360deg);}
}
</style>

<!-- AUTO REDIRECT AFTER 5 SEC -->
<meta http-equiv="refresh" content="8;url=ai_detect_RF.php">

</head>

<body>

<div class="box">

<h2>Processing Your Report...<br>
    ⏳ Please Wait...</h2>

<div class="loader"></div>

<p>Your report is being verified using AI system.</p>
<p>Do not refresh or close this page. </p>
</div>



</body>
</html>