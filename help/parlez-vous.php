<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Drawing Board &gt; "Parlez-vous"</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/drawing_board.php">Drawing Board</a> &gt; "Parlez-vous"</h4>
     <p>It means "speak" in French (kinda' literally, "speak, you"), and it's pronounced <i>par-lay voo</i>.</p>
     <p>I apologize if it sounds a little demanding.  Mostly I just wanted to throw a little French in there :P</p>
     <p>Now you know.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
