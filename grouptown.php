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
require_once 'commons/tiles.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./groupindex.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);

  $can_edit_map = (rank_has_right($ranks, $rankid, 'mapper') || $group['leaderid'] == $user['idnum']);
}
else
  $can_edit_map = false;

$town = MapXHTML($groupid, $can_edit_map);

if($town === false)
{
  CreateMap($groupid);
  
  $town = MapXHTML($groupid, $can_edit_map);

  if($town === false)
    die('Error loading this group\'s town map.  LAME.  (Also, an admin should be notified if this problem persists >_>');
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Town</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Town</h4>
<?php
$activetab = 'town';
include 'commons/grouptabs.php';

if($group['towntiles'] > 0)
  echo '<p>' . $group['towntiles'] . ' tile' . ($group['towntiles'] == 1 ? ' has' : 's have') . ' been used in building this Town.</p>';
else
  echo '<p>This Town has not been built on yet.</p>';

if($can_edit_map)
  echo '<p><i>(Place a Town Square tile by clicking any square on the map.)</i></p>';
else if($a_member && !$can_edit_map)
  echo '<p><i>(You do not have building rights for the Town.)</i></p>';

echo $town;
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
