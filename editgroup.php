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
  header('Location: /directory.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $invites = get_invites_bygroup($groupid);
  $rankid = get_member_rank($group, $user['idnum']);
  $can_edit = (rank_has_right($ranks, $rankid, 'editprofile') || $group['leaderid'] == $user['idnum']);
}
else
  $can_edit = false;

if(!$can_edit)
{
  header('Location: /grouppage.php?id=' . $groupid);
  exit();
}

if($_POST['submit'] == 'Save')
{
  $profile = trim($_POST['newprofile']);
  update_group_profile($groupid, nl2br($profile));

  header('Location: /grouppage.php?id=' . $groupid);
  exit();
}

if($user['idnum'] == $group['leaderid'])
{
  if($_GET['step'] == 2)
  {
    $_POST['itemname'] = trim($_POST['itemname']);
    $item_icon = get_item_byname($_POST['itemname']);
    if($item_icon['custom'] != 'no')
      $item_icon = false;
  }
  else if($_GET['step'] == 3)
  {
    $item_icon = get_item_byid($_GET['itemid']);
    if($item_icon === false || $item_icon['custom'] != 'no')
      $_GET['step'] = 2;
    else
    {
      $group['graphic'] = 'items/' . $item_icon['graphic'];
  
      $command = 'UPDATE psypets_groups SET graphic=' . quote_smart($group['graphic']) . ' WHERE idnum=' . $groupid . ' LIMIT 1';
      $database->FetchNone($command, 'updating group graphic...');
      
      $command = 'UPDATE monster_plaza SET graphic=' . quote_smart($group['graphic']) . ' WHERE idnum=' . $group['forumid'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating plaza graphic...');
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="grouppage.php?id=<?= $groupid ?>"><?= $group['name'] ?></a> &gt; Edit Profile</h4>
<?php
$activetab = 'edit';
include 'commons/grouptabs.php';
?>
     <form action="editgroup.php?id=<?= $groupid ?>" method="post">
     <textarea name="newprofile" cols="50" rows="10" style="width:500px;"><?= br2nl($group['profile']) ?></textarea>
     <p><input type="submit" name="submit" value="Save" /></p>
     </form>
<?php
if($user['idnum'] == $group['leaderid'] && $group['graphic'] == '')
{
?>
     <h5>Group Symbol</h5>
     <p>You have not chosen a Group Symbol.  Choose one now!</p>
     <p>Once you pick something, you can't change it later (that'd be confusing), so choose carefully!</p>
<?php
  if($_GET['step'] == 2 && $item_icon !== false)
  {
?>
     <form action="editgroup.php?id=<?= $groupid ?>&itemid=<?= $item_icon['idnum'] ?>&step=3" method="post">
     <table><tr><td><img src="gfx/items/<?= $item_icon['graphic'] ?>" alt="graphic" /></td><td><input type="submit" name="submit" value="Yes!  That one!" class="bigbutton" /></td></tr></table>
     </form>
     <p>No, no!  It was something else...</p>
<?php
  }
  else if($_GET['step'] == 2)
    echo '<p>There is no item called "' . $_POST['itemname'] . '"...</p>';
  else
    echo '<p>Enter the name of an item whose graphic you\'d like to use for your Group Symbol.  Only standard-availability items may be used.</p>';
?>
     <form action="editgroup.php?id=<?= $groupid ?>&step=2" method="post">
     <p><input name="itemname" /> <input type="submit" name="submit" value="Search" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
