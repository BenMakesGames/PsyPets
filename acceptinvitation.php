<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$id = (int)$_GET['id'];

$invitation = get_invitation_byid($id);

if($invitation['residentid'] != $user['idnum'])
{
  header('Location: /post.php');
  exit();
}

$group = get_group_byid($invitation['groupid']);

if($group === false)
{
  header('Location: /post.php');
  exit();
}

$members = take_apart(',', $group['members']);

if(array_search($user['idnum'], $members) === false)
{
  $members[] = $user['idnum'];

  update_group_members($invitation['groupid'], $members);
}

$groups = take_apart(',', $user['groups']);

if(array_search($invitation['groupid'], $groups) === false)
{
  $groups[] = $invitation['groupid'];

  update_user_groups($user['idnum'], $groups);
}

delete_invitation($id);
check_for_group_invites($user['idnum']);

$organizer = get_user_byid($group['leaderid']);

psymail_user($organizer['user'], $SETTINGS['site_ingame_mailer'], $user['display'] . ' has joined ' . $group['name'] . '!', '{r ' . $user['display'] . '} is now a member of ' . $group['name'] . '!');

header('Location: /grouppage.php?id=' . $invitation['groupid']);
exit();
?>
