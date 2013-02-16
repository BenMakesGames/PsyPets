<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";
require_once 'commons/bannedurls.php';

include 'commons/html.php';
?>
 <head>
  <title>City Hall &gt; <?= $SETTINGS['site_name'] ?> &gt; Help</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; <a href="/help/">Help Desk</a> &gt; Banned URLs</h4>
     <p>The following URLs have been <strong>banned</strong>.  You will not be allowed to send PsyMail or make Plaza posts which contain any of these URLs.</p>
     <p>Banning a URL is not something I do lightly.  Reasons for URL bans are given with each URL.</p>
     <p>If you feel that one of these bannings is unfair, feel free to <a href="contactme.php">contact me</a>.</p>
<?php
foreach($BANNED_URLS as $index=>$url)
  echo '<h5>' . $url . '</h5>' . "\n" .
       '<p>' . $BANNED_EXPLANATIONS[$index] . '</p>' . "\n";
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
