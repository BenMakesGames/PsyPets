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

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);
  $can_kick = (rank_has_right($ranks, $rankid, 'memberkick') || $group['leaderid'] == $user['idnum']);
}
else
  $can_kick = false;

if(!$can_kick)
{
  header('Location: ./grouppage.php?id=' . $groupid);
  exit();
}

$to_kick = urldecode($_POST['resident']);
if($to_kick{0} == '#')
{
  $target = false;
  $targetid = (int)substr($to_kick, 1);
}
else
{
  $target = get_user_bydisplay($to_kick);
  $targetid = $target['idnum'];
}

// the group organizer cannot be kicked out
if($targetid == $group['leaderid'])
{
  header('Location: ./grouppage.php?id=' . $groupid);
  exit();
}

kick_group_member($group, $targetid);

$group = get_group_byid($groupid);
update_group_watchers($group, $ranks);

header('Location: ./grouppage.php?id=' . $groupid);
?>
