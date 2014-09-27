<?php
$whereat = 'post';
$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/inventory.php";

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', strtolower($user['mailboxes']));
else
  $mailbox_folders = array();

$old_folders = $mailbox_folders;

if(array_key_exists('create', $_GET))
{
  $folder = str_replace(',', '', stripslashes(strtolower($_POST['folder'])));

  if(strlen($folder) > 0 && strlen($folder) <= 16 && $folder != 'inbox' && $folder != 'trash' && !in_array($folder, $mailbox_folders))
    $mailbox_folders[] = $folder;
}

if(array_key_exists('delete', $_GET))
{
  $id = (int)$_POST['folder'];

  $folder = $mailbox_folders[$id];

  unset($mailbox_folders[$id]);

  $command = "UPDATE monster_mail SET location='post' WHERE location=" . quote_smart($folder) . " AND `to`=" . quote_smart($user["user"]);
  $database->FetchNone($command, 'moving mail to inbox');
}
 
if($old_folders != $mailbox_folders)
{
  $command = "UPDATE monster_users SET mailboxes=" . quote_smart(implode(',', $mailbox_folders)) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating mailbox folders');
}

header('Location: /postfolders.php');
?>
