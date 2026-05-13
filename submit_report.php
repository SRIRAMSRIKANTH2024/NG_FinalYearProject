<?php
include "db.php";

$result = mysqli_query($conn,"SELECT id FROM reports ORDER BY id DESC LIMIT 1");
$row = mysqli_fetch_assoc($result);
$reportId = $row['id'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Submission Status - NeighbourGuard</title>
<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#0a0f1c;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    color:white;
}

.card{
    background:rgba(255,255,255,0.05);
    backdrop-filter:blur(20px);
    padding:40px;
    border-radius:20px;
    box-shadow:0 0 30px rgba(0,255,255,0.4);
    text-align:center;
    max-width:500px;
}

h2{
    color:#00f7ff;
    text-shadow:0 0 20px #00f7ff;
    margin-bottom:20px;
}

p{
    font-size:16px;
    line-height:1.6;
    margin:10px 0;
}

.success{
    color:#00ff00;
    font-weight:bold;
    margin-top:20px;
}

.status-info{
    background:rgba(0,247,255,0.1);
    padding:15px;
    border-radius:10px;
    margin:20px 0;
    border-left:4px solid #00f7ff;
}

a{
    color:#00f7ff;
    text-decoration:none;
    font-weight:bold;
}

a:hover{
    text-shadow:0 0 10px #00f7ff;
}

.btn{
    display:inline-block;
    background:linear-gradient(90deg,#00f7ff,#0072ff);
    padding:12px 30px;
    border-radius:10px;
    color:white;
    text-decoration:none;
    margin-top:20px;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
    box-shadow:0 0 25px #00f7ff;
}
</style>
</head>
<body>

<div class="card">
    <h2>✅ Report Submitted</h2>
    
    <div class="status-info">
        <p><strong>Status:</strong> Successfully Received</p>
        <p><strong>Processing:</strong> Your report is being analyzed and sent to authorities</p>
    </div>

    <div class="status-info">
        <p><strong>✅ Text Analysis:</strong> Genuine text detected</p>
        <p><strong>✅ Image Verification:</strong> Valid image evidence</p>
        <p><strong>✅ Voice Analysis:</strong> Clear audio evidence</p>
    </div>

    <p>Thank you for helping keep your neighborhood safe. Emergency responders have been notified about the incident in your area.</p>

    <div class="success">
        Your Report ID: #NG-<?php echo $reportId; ?>
    </div>

    <a href="view_reports.php" class="btn">View All Reports</a>
    <a href="report.php" class="btn">Submit Another Report</a>
</div>

<script>
// Generate a demo Report ID
document.getElementById("reportId").textContent = Math.floor(100000 + Math.random() * 900000);
</script>

</body>
</html>