<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
  exit();
}

if($user['idnum'] != $group['leaderid'])
{
  header('grouppage.php?id=' . $groupid);
  exit();
}

$inviteid = (int)$_GET['invite'];

$command = 'SELECT * FROM psypets_group_invites WHERE idnum=' . $inviteid . ' LIMIT 1';
$invite = $database->FetchSingle($command, 'groupcancelinvite.php');

if($invite === false)
{
  header('grouppage.php?id=' . $groupid);
  exit();
}

if($invite['groupid'] == $groupid)
{
  $command = 'DELETE FROM psypets_group_invites WHERE idnum=' . $inviteid . ' LIMIT 1';
  $database->FetchNone($command);
}

check_for_group_invites($invite['residentid']);

header('Location: ./grouppage.php?id=' . $groupid);
?>
