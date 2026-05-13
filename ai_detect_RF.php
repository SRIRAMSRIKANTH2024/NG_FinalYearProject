<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* ---------------- EMERGENCY FLAG ---------------- */
$isEmergency = $_SESSION['emergency'] ?? 0;

/* ---------------- BASIC DATA ---------------- */
$user_id = $_SESSION['user_id'];
$description = $_SESSION['description'] ?? "";
$imagePath = $_SESSION['imagePath'] ?? "";
$audioPath = $_SESSION['audioPath'] ?? "";
$name = $_SESSION["name"] ?? "";

$latitude = $_SESSION['latitude'] ?? "";
$longitude = $_SESSION['longitude'] ?? "";

/* ======================================================
   🔥 FIX 1: HAZARD TYPE FROM DESCRIPTION
====================================================== */
$desc = strtolower($description);

if($isEmergency == "1"){
    $hazard_type = "emergency";
}
elseif(strpos($desc, "fire") !== false || strpos($desc, "smoke") !== false){
    $hazard_type = "fire";
}
elseif(strpos($desc, "dog") !== false || strpos($desc, "cat") !== false || strpos($desc, "animal") !== false){
    $hazard_type = "stray_animal";
}
else{
    $hazard_type = "unknown";
}

/* ---------------- DEFAULT VALUES ---------------- */
$alert_message = "";
$aiTrust = 0;
$aiStatus = "UNKNOWN";
$text_label = "N/A";
$image_label = "N/A";

/* ---------------- RANDOM FOREST ---------------- */
$textLengthScore = (strlen(trim($description)) >= 20) ? 25 : 0;

$fakeWords = ["test","fake","hello","checking"];
$keywordScore = 25;

foreach($fakeWords as $word){
    if(stripos($description, $word) !== false){
        $keywordScore = 0;
        break;
    }
}

$imageScore = !empty($imagePath) ? 25 : 0;
$audioScore = !empty($audioPath) ? 25 : 0;

/* ======================================================
   🚨 EMERGENCY OVERRIDE
====================================================== */
if($isEmergency == "1") {

    $aiStatus = "🚨 EMERGENCY ALERT";
    $aiTrust = 100;
    $alert_message = "🚨 USER TRIGGERED EMERGENCY ALERT";

    $rfScore = 100;
    $trustScore = 100;
    $isFake = false;

} else {

    /* ======================================================
       🤖 AI CALL
    ====================================================== */
    if(!empty($imagePath)){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/analyze");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $postData = [
            'text' => $description,
            'image' => new CURLFile($imagePath)
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);

        if(!curl_errno($ch)){
            $result = json_decode($response, true);

            if($result){
                $text_label = $result['text_detected'] ?? "unknown";
                $image_label = $result['image_detected'] ?? "unknown";
                $aiTrust = $result['trust_score'] ?? 0;
                $aiStatus = $result['status'] ?? "UNKNOWN";

                /* ======================================================
                   🔥 FIX 2: STRICT MATCH VALIDATION
                ====================================================== */
                if($text_label !== $image_label){
                    $aiTrust = 0;
                    $aiStatus = "❌ TEXT & IMAGE MISMATCH";
                }
            }
        }
    }

    /* ---------------- FINAL TRUST SCORE ---------------- */
    $rfScore = $textLengthScore + $keywordScore + $imageScore + $audioScore;
    $trustScore = ($rfScore * 0.5) + ($aiTrust * 0.5);

    $isFake = ($trustScore <= 60);
}

/* ======================================================
   💾 SAVE ONLY IF VALID
====================================================== */
if(!$isFake){

    $limitCheck = $conn->query("SELECT COUNT(*) as total FROM reports");
    $row = $limitCheck->fetch_assoc();

    if($row['total'] >= 100){
        die("<h2 style='color:red;text-align:center;'>🚫 Limit Reached (100)</h2>");
    }

    /* ---------------- SEVERITY MAP ---------------- */
    $hazard_type = strtolower(trim($hazard_type));

    $severityMap = [
        "fire" => 100,
        "stray_animal" => 40,
        "emergency" => 120,
        "unknown" => 20
    ];

    $severity = $severityMap[$hazard_type] ?? 20;

    /* ---------------- LOCATION SCORE ---------------- */
    $stmt2 = $conn->prepare("
        SELECT COUNT(*) as total FROM reports
        WHERE ABS(latitude-?) < 0.01 AND ABS(longitude-?) < 0.01
    ");
    $stmt2->bind_param("dd", $latitude, $longitude);
    $stmt2->execute();
    $count = $stmt2->get_result()->fetch_assoc()['total'];

    $locationScore = min($count * 10, 100);

    /* ---------------- PRIORITY ---------------- */
    if($isEmergency == "1") {
        $priority = 999;
    } else {
        $priority = ($severity*0.4) + ($locationScore*0.3) + (100*0.2) + ($trustScore*0.1);
    }

    /* ---------------- INSERT ---------------- */
    $stmt = $conn->prepare("
        INSERT INTO reports 
        (user_id,name,description,image,audio,latitude,longitude,hazard_type,alert_message,priority_score,report_time)
        VALUES (?,?,?,?,?,?,?,?,?,?,NOW())
    ");

    $stmt->bind_param("issssssssi", 
        $user_id, 
        $name, 
        $description, 
        $imagePath, 
        $audioPath, 
        $latitude, 
        $longitude,
        $hazard_type,
        $alert_message,
        $priority
    );

    $stmt->execute();
    $reportID = $conn->insert_id;
}

/* ---------------- CLEAR SESSION ---------------- */
unset($_SESSION['description']);
unset($_SESSION['imagePath']);
unset($_SESSION['audioPath']);
unset($_SESSION['name']);
unset($_SESSION['emergency']);
?>

<!DOCTYPE html>
<html>
<head>
<title>AI Detection - Smart System</title>

<style>
body{
    background:#0a0f1c;
    color:white;
    font-family:Segoe UI;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.container{
    width:650px;
    padding:30px;
    border-radius:15px;
    background:rgba(255,255,255,0.05);
    box-shadow:0 0 25px #FFBF00;
    text-align:center;
}

.success{ color:#16A34A; }
.fail{ color:#DC2626; }

.score{
    font-size:24px;
    margin:15px 0;
    font-weight:bold;
}

.btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 20px;
    background:#FFBF00;
    color:black;
    text-decoration:none;
    border-radius:6px;
    font-weight:bold;
}

.btn:hover{
    box-shadow:0 0 10px #FFBF00;
}

.mini-container{
  display: flex;
  gap:35px;
  justify-content: center;
}
</style>
</head>

<body>

<div class="container">

<h1>🤖 Smart AI Report Analysis</h1>

<div class="score">
Final Trust Score: <?php echo round($trustScore); ?>%
</div>

<p>🧠 Text Detected: <b><?php echo $text_label; ?></b></p>
<p>🖼 Image Detected: <b><?php echo $image_label; ?></b></p>
<p>⚡ AI Status: <b><?php echo $aiStatus; ?></b></p>

<hr>

<?php if(!$isFake){ ?>

    <h2 class="success">✅ Report Submitted</h2>

    <p><b>Status:</b> Successfully Received</p>
    <p><b>Processing:</b> Sent to authorities</p>

    <hr>

    <p class="success">✅ Validation Passed</p>

    <h3>Your Report ID: #<?php echo $reportID; ?></h3>

    <div class="mini-container">
        <a href="users_viewreports.php" class="btn">View Reports</a>
        <a href="report.php" class="btn">Submit Another Report</a>
    </div>

<?php } else { ?>

    <h2 class="fail">❌ Suspicious / Fake Report</h2>

    <p>This report failed AI validation.</p>

    <a href="report.php" class="btn">Try Again</a>

<?php } ?>

</div>

</body>
</html>