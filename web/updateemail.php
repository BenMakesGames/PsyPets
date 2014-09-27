<?php
require_once "commons/rpgfunctions.php";
require_once "commons/encryption.php";
require_once "commons/dbconnect.php";
require_once "commons/formatting.php";
require_once "commons/userlib.php";

if($_GET["id"] && $_GET["confirm"])
{
  $this_user = get_user_byid((int)$_GET["id"]);

  if($this_user["idnum"] == $_GET["id"])
  {
    if($this_user["activateid"] == $_GET["confirm"] && strlen($this_user["newemail"]) >= 5)
    {
      $command = "UPDATE monster_users SET `email`=" . quote_smart($this_user["newemail"]) . ",`newemail`='' WHERE idnum=" . $this_user["idnum"] . " LIMIT 1";
      $database->FetchNone($command, 'updating resident e-mail address');
    }
  }
}

header('Location: /myaccount/security.php');
?>
