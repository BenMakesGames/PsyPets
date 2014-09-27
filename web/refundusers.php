<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

if($admin["massgift"] != "yes")
{
  header('Location: ./myhouse.php');
  exit();
}

foreach($_POST as $user=>$amount)
{
  if($amount > 0)
  {
    $command = "UPDATE monster_users SET money=money+" . (int)$amount . " WHERE `user`=" . quote_smart($user) . " LIMIT 1";
   $database->FetchNone($command, 'refunding event participant');
  }
}

header('Location: /admin/tools.php');
?>
