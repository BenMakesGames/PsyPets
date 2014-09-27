<?php
$child_safe = false;

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

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: /directory.php');
  exit();
}

$members = take_apart(',', $group['members']);

$a_member = is_a_member($group, $user['idnum']);

if(!$a_member)
{
  header('Location: /grouppage.php?id=' . $groupid);
  exit();
}

$ranks = get_group_ranks($group['idnum']);
$rankid = get_member_rank($group, $user['idnum']);

if($user['idnum'] == $group['leaderid'])
  $my_power = 10000; // max power for ranks is 9999
else if(rank_has_right($ranks, $rankid, 'memberposition'))
  $my_power = $ranks[$rankid]['power'];
else
  $my_power = 0;

if($_POST['action'] == 'updatememberranks')
{
  $changed_group = false;
  $errors = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 6) == 'member')
    {
      $errored = false;
    
      $memberid = (int)substr($key, 6);
      list($userid, $rankid) = explode('|', $members[$memberid]);

      $rankid = get_member_rank($group, $userid);
      if($rankid === false)
        $power = 0;
      else
        $power = $ranks[$rankid]['power'];
        
      if($value == 0)
        $newpower = 0;
      else if(array_key_exists($value, $ranks))
        $newpower = $ranks[$value]['power'];
      else
      {
        $errored = true;
        $errors[] = '<span class="failure">The rank chosen does not exist.</span>';
      }
      
      if(!$errored)
      {
        if($my_power > $power && $my_power > $newpower)
        {
          assign_member_rank($group, $userid, $value);
          $changed_group = true;
        }
        else
          $errors[] = '<span class="failure">Your rank is not high enough.</span>';
      }
    }
  }
  
  if($changed_group)
  {
    update_group_members($group['idnum'], explode(',', $group['members']));
    update_group_watchers($group, $ranks);
  }
}
else if($_POST['action'] == 'updategroupranks' && $my_power == 10000)
{
  $changed_group = false;

  foreach($ranks as $idnum=>$rank)
  {
    $rights = array();

    $newpower = (int)$_POST['power' . $idnum];
    $newtitle = substr(trim($_POST['title' . $idnum]), 0, 32);
    
    if(strlen($newtitle) == 0)
      $errors[] = '<span class="failure">Rank names may not be blank.</span>';
    else if($newpower < 0 || $newpower > 9999)
      $errors[] = '<span class="failure">Rank power must be between 0 and 9999.</span>';
    else
    {
      $ranks[$idnum]['power'] = $newpower;
      $ranks[$idnum]['name'] = $newtitle;

      foreach($GROUP_RIGHTS as $right)
      {
        if($_POST[$right . $idnum] == 'yes' || $_POST[$right . $idnum] == 'on')
          $rights[] = $right;
      }

      $ranks[$idnum]['rights'] = implode(',', $rights);

      save_rank($ranks[$idnum]);
      $changed_group = true;
    }
  }
  
  $newrankname = trim($_POST['newrankname']);
  $newrankpower = (int)$_POST['newrankpower'];
  if(strlen($newrankname) > 0)
  {
    $newrankname = substr(trim($_POST['newrankname']), 0, 32);

    if($newrankpower < 0 || $newrankpower > 9999)
      $errors[] = '<span class="failure">Rank power must be between 0 and 9999.</span>';
    else
    {
      $rights = array();
      foreach($GROUP_RIGHTS as $right)
      {
        if($_POST['newrank' . $right] == 'yes' || $_POST['newrank' . $right] == 'on')
          $rights[] = $right;
      }

      new_group_rank($groupid, $newrankname, $newrankpower, $rights);
      $changed_group = true;
    }
  }
  
  if($changed_group)
  {
    $ranks = get_group_ranks($group['idnum']);
    update_group_watchers($group, $ranks);
  }
}

$command = 'SELECT * FROM monster_plaza WHERE groupid=' . $group['idnum'] . ' LIMIT 1';
$group_plaza = $database->FetchSingle($command, 'fetching group plaza watchers');

$watchers = take_apart(',', $group_plaza['admins']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Member Rights</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="grouppage.php?id=<?= $groupid ?>"><?= $group['name'] ?></a> &gt; Member Rights</h4>
<?php
$activetab = 'rights';
include 'commons/grouptabs.php';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p>' . $error_message . '</p>';

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

$row = begin_row_class();
?>
<h5>Group Ranks</h5>
<p>Rights are given to Ranks, and each member is assigned a Rank who then inherits the rights of that Rank.</p>
<?php
if($my_power == 10000)
  echo '<p>Tip for the Group Organizer: create Ranks that describe general functions which members might have, then assign members to those Ranks.  For example, an "Initiate" might be allowed only to add and remove items from the box, while a "Treasurer" could also clear the item box logs and manage the forums.</p>' .
       '<p>Assign members Ranks using the "Group Members" section below.</p>';
?>
<form method="post">
<table>
 <tr class="titlerow">
<?= $my_power == 10000 ? '<th></th>' : '' ?>
  <th>Rank</th>
  <th class="centered">Power *</th>
  <th class="centered">Forum Watcher</th>
  <th class="centered">Edit Profile</th>
  <th class="centered">Invite Members</th>
  <th class="centered">Kick Members</th>
  <th class="centered">Assign Ranks **</th>
  <th class="centered">Add to Item Box</th>
  <th class="centered">Remove from Item Box</th>
  <th class="centered">Clear Item Box logs</th>
  <th class="centered">Mass Mail</th>
  <th class="centered">Add to Town Map</th>
 </tr>
 <tr class="<?= $row ?>">
<?= $my_power == 10000 ? '<td></td>' : '' ?>
  <td>Group Organizer</td>
  <td class="centered">&#8734;</td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
  <td class="centered"><span class="success">Yes</span></td>
 </tr>
<?php
$row = alt_row_class($row);

if(count($ranks) > 0)
{
  foreach($ranks as $rank)
  {
    $rights = explode(',', $rank['rights']);

    if($my_power == 10000)
    {
?>
 <tr class="<?= $row ?>">
  <td><a href="/group_deleterank.php?group=<?= $groupid ?>&rank=<?= $rank['idnum'] ?>" style="color: red; font-weight: bold;">X</a></td>
  <td><input name="title<?= $rank['idnum'] ?>" value="<?= $rank['name'] ?>" maxlength="32" /></td>
  <td><input name="power<?= $rank['idnum'] ?>" value="<?= $rank['power'] ?>" maxlength="4" size="4" /></td>
<?php
    foreach($GROUP_RIGHTS as $right)
    {
?>
  <td class="centered"><input type="checkbox" name="<?= $right . $rank['idnum'] ?>"<?= in_array($right, $rights) ? ' checked' : '' ?> /></td>
<?php
    }
?>
 </tr>
<?php
    }
    else
    {
?>
 <tr class="<?= $row ?>">
  <td><?= $rank['name'] ?></td>
  <td class="centered"><?= $rank['power'] ?></td>
<?php
    foreach($GROUP_RIGHTS as $right)
    {
?>
  <td class="centered"><?= in_array($right, $rights) ? '<span class="success">Yes' : '<span class="failure">No' ?></span></td>
<?php
    }
?>
 </tr>
<?php
    }

    $row = alt_row_class($row);
  }
}

if($my_power == 10000)
{
?>
 <tr class="<?= $row ?>">
  <td></td>
  <td><input name="newrankname" maxlength="32" /></td>
  <td><input name="newrankpower" maxlength="4" size="4" /></td>
<?php
    foreach($GROUP_RIGHTS as $right)
    {
?>
  <td class="centered"><input type="checkbox" name="newrank<?= $right ?>" /></td>
<?php
    }
?>
 </tr>
<?php
}
?>
</table>
<?php if($my_power === 10000) { ?><p><input type="hidden" name="action" value="updategroupranks" /><input type="submit" value="Update" /></p><?php } ?>
</form>
<p>* "Power" indicates a chain of command:  if the Group Organizer stops playing, a member from the next-highest powered Rank will take over.</p>
<p>** This allows members to change the Ranks of other members, but only among lower-powered Ranks.</p>
<h5>Group Members</h5>
<form method="post">
<table>
 <tr class="titlerow">
  <th>Member</th>
  <th>Rank</th>
  <th><?= ($my_power > 0 ? 'New Rank' : '') ?></th>
 </tr>
<?php
$row = begin_row_class();
$may_update = false;

foreach($members as $i=>$member)
{
  list($userid, $position_data) = explode('|', $member);
  $position = (int)$position_data;

  $this_user = get_user_byid($userid, 'display');
  $options = array();
  
  echo '
    <tr class="' . $row . '">
     <td>' . $this_user['display'] . '</td>
     <td>
  ';

  if($userid == $group['leaderid'])
    echo 'Group Organizer</td><td>';
  else
  {
    $rankid = get_member_rank($group, $userid);
    if($rankid === false)
      $rank_power = 0;
    else
      $rank_power = $ranks[$rankid]['power'];
    
    echo $ranks[$rankid]['name'], '</td>';

    if($my_power > $rank_power)
    {
      if($my_power > 0)
        $options[] = '<option value="0">none</option>';

      foreach($ranks as $rank)
      {
        if($rank['power'] < $my_power)
          $options[] = '<option value="' . $rank['idnum'] . '"' . ($rank['idnum'] == $rankid ? ' selected' : '') . '>' . $rank['name'] . '</option>';
      }

      echo '<td>';

      if(count($options) > 0)
      {
        $may_update = true;
        echo '<select name="member' . $i . '">';
        echo implode('', $options);
        echo '</select>';
      }
    }
    else
      echo '<td>';
  }

  echo '
     </td>
    </tr>
  ';

  $row = alt_row_class($row);
}
?>
</table>
<?php if($may_update) { ?><p><input type="hidden" name="action" value="updatememberranks" /><input type="submit" value="Update" /></p><?php } ?>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
