<?php
$whereat = "post";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

$mailbox_folders = explode(",", $user["mailboxes"]);

if($_GET["folder"] != "post")
{
  $whereat = array_search($_GET["folder"], $mailbox_folders);

  if($whereat !== false)
  {
    unset($mailbox_folders[$whereat]);
     
    if(count($mailbox_folders) > 0)
      $mailboxes = implode(",", $mailbox_folders);
    else
      $mailboxes = "";

    $command = "UPDATE monster_mail SET location='post' WHERE location=" . quote_smart($whereat) . " AND to=" . quote_smart($user["user"]);
    $database->FetchNone($command, 'moving mail out of folder');

    $command = 'UPDATE monster_users SET mailboxes=' . quote_smart($mailboxes) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1";
    $database->FetchNone($command, 'delete folder');
  }
}

 Header("Location: post.php");
?>