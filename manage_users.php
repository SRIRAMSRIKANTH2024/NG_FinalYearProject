<?php
session_start();
include "db.php";

// 🔐 Admin check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// 🔍 SEARCH
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
}

// 🗑 DELETE USER
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];

    // Prevent admin deleting themselves
    if($delete_id != $_SESSION['user_id']){
        $conn->query("DELETE FROM users WHERE id='$delete_id'");
        header("Location: manage_users.php");
        exit();
    } else {
        echo "<script>alert('You cannot delete yourself!');</script>";
    }
}

// 📊 FETCH USERS + REPORT COUNT
$query = "
SELECT users.id, users.name, users.email, users.role,
COUNT(reports.id) AS total_reports
FROM users
LEFT JOIN reports ON users.id = reports.user_id
WHERE (users.name LIKE '%$search%' OR users.email LIKE '%$search%') AND users.role != 'admin'
GROUP BY users.id
ORDER BY users.id DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Users</title>

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
    margin-bottom:20px;
}

.search-box{
    text-align:center;
    margin-bottom:20px;
}

input[type="text"]{
    padding:10px;
    width:250px;
    border-radius:5px;
    border:none;
}

button{
    padding:10px 15px;
    background:cyan;
    border:none;
    cursor:pointer;
    border-radius:5px;
}

table{
    width: 100%;
    border-collapse:collapse;
    background:#111;
}

th, td{
    padding:12px;
    text-align:center;
    border:1px solid #333;
}

th{
    background:#00f7ff;
    color:black;
}

tr:hover{
    background:#1a1a1a;
}

.delete-btn{
    color:red;
    font-weight:bold;
    text-decoration:none;
}

.dashboard-btn{
    position:absolute;
    top:20px;
    right:40px;
    background:white;
    color:#1e3a5f;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}

.dashboard-btn:hover{
    box-shadow:0 0 10px cyan;
}
</style>

</head>

<body>

<h2>👥 Manage Users</h2>

<a href="admin_dashboard.php" class="dashboard-btn">Dashboard</a>

<!-- 🔍 SEARCH -->
<div class="search-box">
<form method="GET">
<input type="text" name="search" placeholder="Search by name or email" value="<?php echo $search; ?>">
<button type="submit">Search</button>
</form>
</div>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Total Reports</th>
<th>Action</th>
</tr>

<?php if($result->num_rows > 0){ ?>

    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td><?php echo $row['total_reports']; ?></td>
        <td>
            <a class="delete-btn"
            href="manage_users.php?delete=<?php echo $row['id']; ?>"
            onclick="return confirm('Delete this user?');">
            Delete
            </a>
        </td>
    </tr>
    <?php } ?>

<?php } else { ?>

    <tr>
        <td colspan="6" style="padding:20px; color:#aaa;">
            No users found
        </td>
    </tr>

<?php } ?>

</table>

</body>
</html>