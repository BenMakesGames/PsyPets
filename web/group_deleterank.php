<?php
$child_safe = false;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';
require_once 'commons/messages.php';

$groupid = (int)$_GET['group'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
  exit();
}

if($user['idnum'] != $group['leaderid'])
{
  header('Location: ./grouprights.php?id=' . $groupid);
  exit();
}

$rankid = (int)$_GET['rank'];

$rank = get_rank_by_id($rankid);

if($rank['groupid'] == $groupid)
{
  $members = explode(',', $group['members']);
  foreach($members as $i=>$member)
  {
    list($userid, $userrank) = explode('|', $member);
    
    if($userrank == $rankid)
      $members[$i] = $userid . '|0';
  }

  update_group_members($groupid, $members);
  
  delete_rank_by_id($rankid);
}

header('Location: ./grouprights.php?id=' . $groupid);
?>
