<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "neighbourguard_db";

// ✅ CREATE CONNECTION
$conn = new mysqli($host, $user, $password, $dbname);

// ✅ CHECK CONNECTION FIRST
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🧹 AUTO ARCHIVE AFTER 24 HOURS (NOT DELETE)

// 1. MOVE TO ARCHIVE
$conn->query("
    INSERT INTO reports_archive
    SELECT * FROM reports
    WHERE status='Completed'
    AND report_time <= NOW() - INTERVAL 1 DAY
");

// 2. DELETE FROM MAIN TABLE
$conn->query("
    DELETE FROM reports
    WHERE status='Completed'
    AND report_time <= NOW() - INTERVAL 1 DAY
");
?>