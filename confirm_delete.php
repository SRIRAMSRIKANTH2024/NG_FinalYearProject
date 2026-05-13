<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['report_id'])){

    $report_id = $_POST['report_id'];
    $user_id = $_SESSION['user_id'];

    // ✅ VERIFY OWNERSHIP
    $stmt = $conn->prepare("
        SELECT id FROM reports 
        WHERE id=? AND user_id=? AND status='Completed'
    ");
    $stmt->bind_param("ii", $report_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        // ✅ ONLY MARK AS CONFIRMED (NO DELETE)
        $update = $conn->prepare("
            UPDATE reports 
            SET user_confirmed=1 
            WHERE id=?
        ");
        $update->bind_param("i", $report_id);
        $update->execute();

        header("Location: users_viewreports.php?msg=confirmed");
        exit();

    } else {
        echo "Invalid Action!";
    }
}
?>