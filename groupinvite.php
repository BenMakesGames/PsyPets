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
$organizer = get_user_byid($group['leaderid']);

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);
  $can_invite = (rank_has_right($ranks, $rankid, 'memberadd') || $group['leaderid'] == $user['idnum']);
}
else
  $can_invite = false;

if(!$can_invite)
{
  header('Location: ./grouppage.php?id=' . $groupid);
  exit();
}

if($_POST['action'] == 'Invite')
{
  $message = trim($_POST['message']);
  $target = get_user_bydisplay($_POST['sendto']);
  $groups = take_apart(',', $target['groups']);
  if($target === false)
    $errors = '<p class="failure">Could not find a resident by that name.</p>';
  else
  {
    $_POST = array();

    if(!is_a_member($group, $target['idnum']) || array_search($groupid, $groups) === false)
    {
      $invitation = get_invitation($groupid, $target['idnum']);
      if($invitation === false)
      {
        create_invite($groupid, $target['idnum'], $message);
        check_for_group_invites($target['idnum']);

        $errors = '<p class="success">Invitation sent!</p>';
      }
      else
        $errors = '<p class="failure">That resident has already been invited.</p>';
    }
    else
      $errors = '<p class="failure">That resident is already a member of this group!</p>';
  }
}

include 'commons/html.php';
?>
 <head>
<?php include "commons/head.php"; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Invite Resident</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="grouppage.php?id=<?= $groupid ?>"><?= $group['name'] ?></a> &gt; Send Invitation</h4>
<?php
$activetab = 'invite';
include 'commons/grouptabs.php';
?>
<?= strlen($errors) > 0 ? $errors : '' ?>
     <form action="groupinvite.php?id=<?= $groupid ?>" method="post" name="invitation" id="invitation">
     <table>
      <tr>
       <th>To:</th>
       <td><input name="sendto" value="<?= $_POST['sendto'] ?>" style="width:200px;">&nbsp;<span class="size13">&lArr;</span>&nbsp;<select name="buddylist" style="width:200px;" onchange="sendto.value=buddylist.value;">
        <option value=""></option>
<?php
$friend_list = take_apart(',', $user['friends']);

if(count($friend_list) > 0)
{
  foreach($friend_list as $idnum)
  {
    $friend = get_user_byid($idnum);
    $names[] = $friend['display'];
  }

  sort($names);

  foreach($names as $name)
    echo "        <option value=\"$name\">$name</option>\n";
}
?>
       </select></td>
      </tr>
      <tr><th>Message:</th><td><input name="message" maxlength="120" style="width:400px;" /></td></tr>
      <tr><td colspan="2" align="center"><input type="submit" name="action" value="Invite" /></td></tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
