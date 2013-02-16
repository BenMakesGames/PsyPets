<?php
require_once "commons/dbconnect.php";
require_once "commons/light_sessions.php";

echo strtotime($user["birthday"]) . "<br />\n";
echo (strtotime($user["birthday"]) + (16 * 365 * 24 * 60 * 60)) . "<br />\n";
echo time() . "<br />\n";
echo "you appear to be " . ((time() - strtotime($user["birthday"])) / (365 * 24 * 60 * 60)) . " years old.<br />";
?>