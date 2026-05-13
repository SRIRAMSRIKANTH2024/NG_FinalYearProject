<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>NeighbourGuard Crime Report</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<style>

/* ✅ YOUR ORIGINAL STYLE — UNCHANGED */
body{
font-family:Arial;
background:#0a0f1c;
color:white;
margin:0;
padding-top:60px;
}

.container{
    display:flex;
    gap:20px;
    padding:20px;
    box-sizing:border-box;
    margin-top:10px;
    min-height:100vh;
}

.card{
    background:#111;
    padding:30px;
    width:420px;
    border-radius:15px;
    border:2px solid #00f7ff;
    box-shadow:0 0 25px rgba(0,247,255,0.6);

    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.card:hover{
box-shadow:0 0 35px cyan;
transition:0.3s;
}

input, textarea{
width:100%;
padding:8px 10px;
margin:8px 0;
border:none;
border-radius:8px;
box-sizing:border-box;
font-size:14px;
}

input[type="text"],
textarea,
input[type="file"]{
background:#0f1629;
color:white;
}

h2{
text-align:center;
color:#00f7ff;
}

input::placeholder,
textarea::placeholder{
color:#00f7ff;
opacity:1;
font-size:13px;
}

button{
width:100%;
padding:12px;
border:none;
background:#00f7ff;
color:black;
font-weight:bold;
cursor:pointer;
border-radius:8px;
margin-top:10px;
transition:0.3s;
}

button:hover{
box-shadow:0 0 20px #00f7ff;
}

.alertBtn{
background:red;
animation:pulse 1s infinite;
}

@keyframes pulse{
0%{box-shadow:0 0 5px red;}
50%{box-shadow:0 0 25px red;}
100%{box-shadow:0 0 5px red;}
}

label{
display:block;
margin-top:10px;
color:#00f7ff;
font-weight:bold;
}

.dashboard-btn{
position:absolute;
top:20px;
right:20px;
background:white;
color:#1e3a5f;
padding:10px 18px;
border-radius:6px;
text-decoration:none;
font-weight:bold;
}

.dashboard-btn:hover{
box-shadow:0 0 10px cyan;
transition:0.3s;
}

/* MAP */
#map{
flex:1;
height:stretch;
width:100%;
border-radius:10px;
}

@media(max-width:900px){
.container{
flex-direction:column;
}

#map{
height:500px;
}
}

.upload-box input[type="file"]{
border:2px dashed #00f7ff;
padding:12px;
border-radius:10px;
background:#0f1629;
color:white;
cursor:pointer;
}

.upload-box input[type="file"]:hover{
background:#111a30;
box-shadow:0 0 10px cyan;
transition:0.3s;
}

.msg{
background:rgba(0,247,255,0.15);
border-left:5px solid #00f7ff;
padding:14px;
margin-bottom:15px;
border-radius:10px;
text-align:center;
color:red;
font-weight:bold;
box-shadow:0 0 15px rgba(0,247,255,0.3);
}
</style>
</head>

<body>

<a href="user_dashboard.php" class="dashboard-btn">Dashboard</a>

<div class="container">

<div class="card">

<h2>⚡NeighbourGuard Crime Report⚡</h2>

<?php if(isset($_GET['error'])){ ?>
<div class="msg">⚠ Please select hazard location on map!</div>
<?php } ?>

<form method="POST" action="loading.php" enctype="multipart/form-data" onsubmit="return validateLocation()">

<input type="hidden" name="emergency" id="emergencyFlag" value="0">

<!-- OPTIONAL SAFE ADD -->
<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

<input type="text" name="name" placeholder="Your Name" required>

<textarea name="description" placeholder="Describe the crime" required></textarea>

<div class="upload-box">

<label>Upload or Capture Image</label>
<input type="file" name="image" accept="image/*" capture="environment">

<label>Upload Voice Evidence</label>
<input type="file" name="audio" id="audioInput" accept="audio/*">

<label style="text-align:center">Or</label>

</div>

<label>Record Voice Evidence</label>
<button type="button" id="recordBtn" onclick="toggleRecording()">🎤 Start Recording</button>

<input type="hidden" name="latitude" id="lat">
<input type="hidden" name="longitude" id="lon">

<button type="submit">Submit Report</button>

<button type="button" onclick="handleEmergency()" class="alertBtn">
🚨 EMERGENCY ALERT
</button>

</form>

</div>

<div id="map"></div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// ✅ YOUR ORIGINAL MAP — FIXED ONLY SYNTAX
var map = L.map('map').setView([11.1271,78.6569],7);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
maxZoom:19
}).addTo(map);

var marker;

map.on('click',function(e){

document.getElementById("lat").value = e.latlng.lat;
document.getElementById("lon").value = e.latlng.lng;

if(marker){
map.removeLayer(marker);
}

marker = L.marker(e.latlng).addTo(map);

});

function validateLocation(){
if(document.getElementById("lat").value === ""){
alert("⚠ Please select hazard location!");
return false;
}
return true;
}

function handleEmergency(){
document.getElementById("emergencyFlag").value = "1";
alert("🚨 Emergency Mode Activated!");
}

// Marker variable
var marker;
var redIcon = new L.Icon({
iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
iconSize: [25, 41],
iconAnchor: [12, 41],
popupAnchor: [1, -34],
shadowSize: [41, 41]
});

var selectedLat = null;
var selectedLon = null;
// Click map to select location
map.on('click',function(e){

var lat = e.latlng.lat;
var lon = e.latlng.lng;

selectedLat = lat;
selectedLon = lon;

document.getElementById("lat").value = lat;
document.getElementById("lon").value = lon;

if(marker){
map.removeLayer(marker);
}

marker = L.marker([lat,lon],{icon:redIcon}).addTo(map)
.bindPopup(
"<b>Crime Location Selected</b><br>Latitude: "
+ lat.toFixed(5) +
"<br>Longitude: " +
lon.toFixed(5)
)
.openPopup();

});


// 🎙️ VOICE RECORDING
// 🎤 VOICE RECORDING (RESTORED)
let mediaRecorder;
let audioChunks = [];

function toggleRecording(){

    if(!mediaRecorder || mediaRecorder.state === "inactive"){

        navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {

            mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.start();

            audioChunks = [];

            mediaRecorder.ondataavailable = e => {
                audioChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {

                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });

                const audioFile = new File([audioBlob], "recorded_audio.webm");

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(audioFile);

                document.getElementById("audioInput").files = dataTransfer.files;

                alert("✅ Audio recorded and attached!");
            };

            document.getElementById("recordBtn").innerText = "⏹ Stop Recording";
        });

    } else {
        mediaRecorder.stop();
        document.getElementById("recordBtn").innerText = "🎤 Start Recording";
    }
}e
</script>

</body>
</html>