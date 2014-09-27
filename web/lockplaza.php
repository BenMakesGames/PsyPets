<?php
$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";

if($admin["manageplaza"] == "no")
{
  Header("Location: ./plaza.php");
  exit();
}

$command = "SELECT * FROM monster_plaza WHERE idnum=" . quote_smart($_GET["plaza"]) . " LIMIT 1";
$plazainfo = $database->FetchSingle($command, 'fetching plaza info');

if(substr($plazainfo['title'], 0, 1) == "#" || $plazainfo === false)
{
  header('Location: ./plaza.php');
  exit();
}

if($plazainfo["locked"] == "yes")
  $newval = "no";
else
  $newval = "yes";
  
$command = "UPDATE monster_plaza SET locked='$newval' WHERE idnum=" . $plazainfo["idnum"] . " LIMIT 1";
$database->FetchNone($command, 'toggling plaza lock');

Header("Location: ./viewplaza.php?plaza=" . $plazainfo["idnum"]);
exit();
?>