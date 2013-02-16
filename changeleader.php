<?php
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

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
  exit();
}

$members = get_group_member_ids($group['members']);

if($user['idnum'] != $group['leaderid'])
{
  header('Location: ./grouppage.php?id=' . $groupid);
  exit();
}

if($_POST['action'] == 'resign')
{
  $target = get_user_bydisplay($_POST['resident']);

  if($target === false)
    $errors = '<p class="failure">Could not find a resident by that name.</p>';
  else if(array_search($target['idnum'], $members) === false)
    $errors = '<p class="failure">' . $target['display'] . ' is not a member of this group...</p>';
  else if($target['idnum'] == $group['leaderid'])
    $errors = '<p class="failure">' . $target['display'] . ' is already the group organizer...</p>';
  else
  {
    change_group_leader($group, $target['idnum']);
    psymail_group_byarray($members, $SETTINGS['site_ingame_mailer'], $group['name'] . ' has a new Group Organizer!', $user['display'] . ' has resigned as Group Organizer, passing the responsibility to {r ' . $target['display'] . '}!');
    header('Location: ./grouppage.php?id=' . $groupid);
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Invite Resident</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="grouppage.php?id=<?= $groupid ?>"><?= $group['name'] ?></a> &gt; Resign</h4>
<?php
$activetab = 'resign';
include 'commons/grouptabs.php';
?>
<?= strlen($errors) > 0 ? $errors : '' ?>
     <p>If you wish to give up the position of group organizer, you must choose another resident to take your place.</p>
<?php
if(count($members) > 1)
{
?>
     <p>Who will become the new group organizer?</p>
     <form action="changeleader.php?id=<?= $groupid ?>" method="post">
     <ul class="plainlist">
<?php
  foreach($members as $memberid)
  {
    if($memberid == $user['idnum'])
      continue;

    $resident = get_user_byid($memberid);

    if($resident === false)
      continue;
?>
      <li><input type="radio" name="resident" value="<?= $resident['display'] ?>" /> <a href="userprofile.php?user=<?= link_safe($resident['display']) ?>"><?= $resident['display'] ?></a></li>
<?php
  }
?>
     </ul>
     <p><input type="hidden" name="action" value="resign" /><input type="submit" value="Resign" onclick="return(confirm('Really?') ? confirm('Really-really?') : false);" /></p>
<?php
}
else
  echo '<p>There is no one else in the group to give up the position to.</p>';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
