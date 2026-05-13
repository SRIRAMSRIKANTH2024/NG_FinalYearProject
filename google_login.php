<?php
session_start();
include 'db.php';

if(isset($_POST['email'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $google_id = $_POST['sub'];
    $login_type = "google";

    // default password (hashed)
    $default_password = password_hash("user", PASSWORD_DEFAULT);

    // check user
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $role = $row['role'];

    } else {

        // insert new user
        $stmt = $conn->prepare("INSERT INTO users (name,email,google_id,login_type,password) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$name,$email,$google_id,$login_type,$default_password);
        $stmt->execute();

        $user_id = $stmt->insert_id;
        $role = "user";
    }

    // ✅ set session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;

    // ✅ redirect directly (NO JS)
    header("Location: user_dashboard.php");
    exit();
}
?>