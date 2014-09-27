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
  header('Location: ./groupindex.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid']);

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);
  $can_clear_logs = (rank_has_right($ranks, $rankid, 'boxlogs') || $group['leaderid'] == $user['idnum']);
}
else
  $can_clear_logs = false;

if(!$a_member)
{
  header('Location: ./groupbox.php?id=' . $groupid);
  exit();
}

if($can_clear_logs && $_GET['action'] == 'clearlogs')
{
  $command = 'DELETE FROM psypets_groupboxlogs WHERE groupid=' . $groupid;
  $database->FetchNone($command, 'deleting group box logs');
  $logs = array();
}
else
{
  $command = 'SELECT timestamp,userid,type,details FROM psypets_groupboxlogs WHERE groupid=' . $groupid . ' ORDER BY idnum DESC';
  $logs = $database->FetchMultiple($command, 'fetching group box logs');
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; <?= $ITEM_BOX ?></title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; <?= $ITEM_BOX ?> Logs</i></h4>
<?php
$activetab = 'box';
include 'commons/grouptabs.php';
?>
     <ul class="tabbed">
      <li><a href="groupbox.php?id=<?= $groupid ?>">Browse/Take Items</a>
      <li><a href="groupbox_add.php?id=<?= $groupid ?>">Add Items</a>
      <li class="activetab"><a href="groupbox_logs.php?id=<?= $groupid ?>">Logs</a></li>
     </ul>
<?php
if(count($logs) > 0)
{
  if($can_clear_logs)
    echo '<ul><li><a href="groupbox_logs.php?id=' . $groupid . '&action=clearlogs">Clear logs</a></li></ul>';
?>
     <table border="0" cellspacing="0" cellpadding="4">
      <tr class="titlerow">
       <th>Member</th>
       <th>Action</th>
       <th>Items</th>
      </tr>
<?php
  $row_class = begin_row_class();

  foreach($logs as $log)
  {
    $display = get_user_byid($log['userid'], 'display');
    if($display === false)
      $display_text = '<i class="dim">departed #' . $log['userid'] . '</i>';
    else
      $display_text = resident_link($display['display']);
?>
      <tr class="<?= $row_class ?>">
       <td><?= $display_text ?></td>
       <td><?= $log['type'] ?></td>
       <td><?= $log['details'] ?></td>
      </tr>
<?php
    $row_class = alt_row_class($row_class);
  }
?>
     </table>
<?php
  if($can_clear_logs)
    echo '<ul><li><a href="groupbox_logs.php?id=' . $groupid . '&action=clearlogs">Clear logs</a></li></ul>';
}
else
  echo '<p>No activity has been logged.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
