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

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
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
  $can_kick = (rank_has_right($ranks, $rankid, 'memberkick') || $group['leaderid'] == $user['idnum']);
}
else
  $can_kick = false;

consider_group_badges($group);

include 'commons/html.php';
?>
 <head>
<?php include "commons/head.php"; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Profile</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <table border="0" cellspacing="0" cellpadding="2" style="background-color: white; border: 1px solid black; float: right; padding: 0.5em;">
<?php
if($group['graphic'] != '')
  echo '<tr><td colspan="2" align="center"><img src="gfx/' . $group['graphic'] . '" alt="" /></td></tr>';
?>
      <tr><th>Started</th><td><?= local_date($group['birthdate'], $user['timezone'], $user['daylightsavings']) ?></td></tr>
      <tr><th>Members</th><td><?= count($members) ?></td></tr>
      <tr><th>Organizer</th><td><a href="residentprofile.php?resident=<?= link_safe($organizer['display']) ?>"><?= $organizer['display'] ?></a></td></tr>
      <tr><td colspan="2"><?php render_group_badges_xhtml($group) ?></td></tr>
<?php
if($user['admin']['clairvoyant'] == 'yes')
  echo '<tr><th>Max &curren;</th><td>' . get_group_max_currencies($group) . '</td></tr>';
?>
      </table>
     <h4><?= $group['name'] ?>  &gt; Profile</h4>
<?php
$activetab = 'profile';
include 'commons/grouptabs.php';
?>
     <ul>
      <li><a href="http://<?= $SETTINGS['wiki_domain'] ?>/Group:<?= $group['name'] ?>">View group's PsyHelp entry</a></li>
     </ul>
     <p><?= format_text($group['profile']) ?></p>
     <h5>Members</h5>
     <table>
<?php
$member_index = 1;
foreach($members as $member_data)
{
  list($memberid, $rankid) = explode('|', $member_data);

  $member = get_user_byid($memberid, 'idnum,display,graphic,is_a_whale,lastactivity');

  if($member_index % 3 == 1)
    echo '<tr>';

  if($can_kick)
  {
    echo '<td>';
    if($memberid != $group['leaderid'])
      echo '<form action="groupkick.php?id=' . $groupid . '" method="post"><input type="hidden" name="resident" value="' . ($member === false ? urlencode('#' . $memberid) : urlencode($member['display'])) . '" /><input type="submit" value="X" style="width: 2em;" onclick="return confirm(\'Kick this member out of the group?\');" /></form>';
    echo '</td>';
  }

  if($member === false)
    echo '<td></td><td style="padding-right: 1em;"><i class="dim">Departed #' . $memberid . '</i></td>';
  else
  {
    echo '<td><img src="' . user_avatar($member) . '" alt="" width="48" height="48" /></td><td style="padding-right: 1em;"><a href="residentprofile.php?resident=' . link_safe($member['display']) . '">' . $member['display'] . '</a>';
    if($memberid == $group['leaderid'])
      echo '<br />Group Organizer';
    else if(array_key_exists($rankid, $ranks))
      echo '<br />' . $ranks[$rankid]['name'];
    echo '</td>';
  }

  if($member_index % 3 == 0)
    echo "</tr>\n";

  $member_index++;
}
?>
     </table>
<?php
if($can_kick)
  echo '<p><i>(Click the "X" button next to a member\'s name to kick that member out of the group.)</i></p>';
?>
<?php
if(count($invites) > 0)
{
?>
     <h5>Pending Invites</h5>
     <ul>
<?php
foreach($invites as $invite)
{
  $member = get_user_byid($invite['residentid'], 'idnum,display,graphic');

  if($member === false)
    echo '<li><i style="color:#888;">Departed #' . $invite['residentid'] . '</i>';
  else
    echo '<li><a href="residentprofile.php?resident=' . link_safe($member['display']) . '">' . $member['display'] . '</a>';

  if($group['leaderid'] == $user['idnum'])
    echo ' (<a href="groupcancelinvite.php?id=' . $group['idnum'] . '&invite=' . $invite['idnum'] . '">cancel invitation</a>)';

  echo '</li>';
}
?>
     </ul>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
