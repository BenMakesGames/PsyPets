<?php
$wiki = 'My_Groups';
$child_safe = false;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$invites = get_invites_byuser($user['idnum']);
$groups = take_apart(',', $user['groups']);

if($_POST['action'] == 'leave')
{
  $remove_group_membership = false;

  $groupid = (int)$_POST['groupid'];

  $group = get_group_byid($groupid);

  if($group !== false)
  {
    if($group['systemgroup'] == 'yes')
      $error_message = '<span class="failure">You cannot leave this group, but you may ask the Group Organizer to kick you out.</span>';
    else if($group['leaderid'] == $user['idnum'])
      $error_message = '<span class="failure">You cannot leave a group for which you are the organizer.</span>';
    else
    {
      if(kick_group_member($group, $user['idnum']))
      {
        $leader = get_user_byid($group['leaderid']);

        if($leader !== false)
          psymail_user($leader['user'], $SETTINGS['site_ingame_mailer'], $user['display'] . ' has left ' . $group['name'], '{r ' . $user['display'] . '} left your group, ' . $group['name'] . '.');

        header('Location: ./mygroups.php');
        exit();
      }
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Groups</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>My Groups</h5>
      <ul><li><a href="groupindex.php">Browse all groups</a></li></ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($messages) > 0)
{
  echo "<ul>\n";
  foreach($messages as $message)
    echo "<li>$message</li>\n";
  echo "</ul>\n";
}

if(count($invites) > 0)
{
?>
<h5>Invitations</h5>
<table>
 <tr class="titlerow">
  <th>Group</th>
  <th>Action</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($invites as $invite)
  {
    $groupname = get_group_name_byid($invite['groupid']);
?>
 <tr class="<?= $rowclass ?>">
  <td><a href="grouppage.php?id=<?= $invite['groupid'] ?>"><?= $groupname ?></a></td>
  <td><a href="acceptinvitation.php?id=<?= $invite['idnum'] ?>">Accept</a> | <a href="declineinvitation.php?id=<?= $invite['idnum'] ?>">Decline</a></td>
 </tr>
 <tr class="<?= $rowclass ?>">
  <td colspan="2"><?= $invite['message'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<?php
}
?>
     <h5>Memberships</h5>
<?php
if(count($groups) > 0)
{
?>
     <form action="mygroups.php" method="post">
     <table>
      <tr class="titlerow"><th></th><th></th><th>Name</th><th>Organizer</th><th>Members</th></tr>
<?php
  $rowclass = begin_row_class();

  foreach($groups as $groupid)
  {
    $group = get_group_byid($groupid);
    $members = take_apart(',', $group['members']);
    $leader = get_user_byid($group['leaderid']);
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="groupid" value="<?= $group['idnum'] ?>" /></td>
       <td class="centered"><?= ($group['graphic'] != '' ? '<a href="grouppage.php?id=' . $group['idnum'] . '"><img src="gfx/' . $group['graphic'] . '" alt="" /></a>' : '') ?></td>
       <td><a href="grouppage.php?id=<?= $group['idnum'] ?>"><?= $group['name'] ?></a></td>
       <td><a href="userprofile.php?user=<?= link_safe($leader['display']) ?>"><?= $leader['display'] ?></a></td>
       <td align="right"><?= count($members) ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="leave" /><input type="submit" value="Leave Group" class="bigbutton" /></p>
     </form>
<?php
}
else
  echo '<p>You are not a member of any group.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
