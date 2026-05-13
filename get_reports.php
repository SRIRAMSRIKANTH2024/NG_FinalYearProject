<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
SELECT id, hazard_type, status, description
FROM reports
WHERE user_id='$user_id'
ORDER BY id DESC
");

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>