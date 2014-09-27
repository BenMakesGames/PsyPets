<?php
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
  header('Location: ./post.php');
  exit();
}

$group = get_group_byid($invitation['groupid']);

if($group === false)
{
  header('Location: ./post.php');
  exit();
}

delete_invitation($id);
check_for_group_invites($user['idnum']);

$organizer = get_user_byid($group['leaderid']);

psymail_user($organizer['user'], $SETTINGS['site_ingame_mailer'], $user['display'] . ' has declined your invitation!', $user['display'] . ' will not be joining ' . $group['name'] . '...');

header('Location: ./post.php');
exit();
?>
