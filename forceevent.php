<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/doevent.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/checkpet.php";

if($admin["manageevents"] != "yes")
{
  Header("Location: ./myhouse.php");
  exit();
}

DoEvent($_POST["idnum"]);

Header("Location: eventdetails.php?idnum=" . $_POST["idnum"]);
?>
