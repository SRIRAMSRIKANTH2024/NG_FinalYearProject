<?php
include "db.php";

// 🔁 MOVE TO ARCHIVE (SAFE)
$moveQuery = $conn->query("
    SELECT * FROM reports 
    WHERE status='Completed' 
    AND user_confirmed=1
");

while($row = $moveQuery->fetch_assoc()){

    $archive = $conn->prepare("
        INSERT INTO reports_archive 
        (id, user_id, name, description, image, audio, latitude, longitude, hazard_type, alert_message, status, admin_contact, user_confirmed, priority_score, report_time, completed_time)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $archive->bind_param("iissssssssssssis",
        $row['id'],
        $row['user_id'],
        $row['name'],
        $row['description'],
        $row['image'],
        $row['audio'],
        $row['latitude'],
        $row['longitude'],
        $row['hazard_type'],
        $row['alert_message'],
        $row['status'],
        $row['admin_contact'],
        $row['user_confirmed'],
        $row['priority_score'],
        $row['report_time'],
        $row['completed_time']
    );

    $archive->execute();

    // DELETE AFTER ARCHIVE
    $del = $conn->prepare("DELETE FROM reports WHERE id=?");
    $del->bind_param("i", $row['id']);
    $del->execute();
}


// ⏱ AUTO DELETE AFTER 24 HOURS (NO USER CONFIRM)
$conn->query("
    DELETE FROM reports 
    WHERE status='Completed' 
    AND (user_confirmed IS NULL OR user_confirmed=0)
    AND completed_time IS NOT NULL
    AND completed_time <= NOW() - INTERVAL 1 DAY
");
?>