<?php
session_start();
include "db.php";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>