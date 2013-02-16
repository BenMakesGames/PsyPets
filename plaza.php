<?php
/*
header('Location: ./plazaupdate.php');
exit();
*/
$nevercache = true;
$require_petload = 'no';
$wiki = 'The_Plaza';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/grouplib.php';
require_once 'commons/questlib.php';
/*
if($user['idnum'] != 1)
{
  header('Location: ./plazaupdate.php');
  exit();
}
*/

require_once 'commons/leonids.php';

$plaza_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: the plaza');
if($plaza_tutorial_quest === false)
  $no_tip = true;

if(strlen($user['groups']) > 0)
{
  $groupids = take_apart(',', $user['groups']);

  $command = 'SELECT * FROM monster_plaza WHERE groupid IN (' . $user['groups'] . ') LIMIT ' . count($groupids);
  $group_forums = $database->FetchMultiple($command, 'fetching group plazas');
}
else
  $group_forums = array();

// read all the plaza infos (title, last post, etc)
$plazas = $database->FetchMultiple('SELECT * FROM monster_plaza WHERE groupid=0 ORDER BY `order` ASC');

// get the last time the resident has viewed each plaza
// negative 'threadid's correspond to plaza id numbers
$visits = $database->FetchMultiple(
	'SELECT * FROM monster_watching ' .
  'WHERE threadid<0 AND user=' . quote_smart($user['user'])
);

// go through all the database results, and copy the values into the $lastvisit array
foreach($visits as $visit)
  $lastvisit[-$visit['threadid']] = $visit;

if($admin['managewatchers'] == 'yes')
{
  $command = 'SELECT members FROM psypets_groups WHERE idnum=38 LIMIT 1';
  $groupinfo = $database->FetchSingle($command, 'fetching plaza watchers');

  $watcherids = array();
  $members = explode(',', $groupinfo['members']);
  foreach($members as $member)
  {
    list($memberid, $rank) = explode('|', $member);
    $watcherids[] = $memberid;
  }

  $watcher_command = 'SELECT display,lastactivity FROM monster_users WHERE idnum IN (' . implode(',', $watcherids) . ') AND lastactivity<' . ($now - 3 * 24 * 60 * 60);
  $absent_watchers = $database->FetchMultiple($watcher_command, 'fetching absent plaza watchers');
}
else
  $absent_watchers = array();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($plaza_tutorial_quest === false)
{
  include 'commons/tutorial/plaza.php';
  add_quest_value($user['idnum'], 'tutorial: the plaza', 1);
}

echo '<h4>Plaza Forums</h4>';

if(count($absent_watchers) > 0)
{
?>
     <h5>Absent Watchers</h5>
     <table>
      <tr class="titlerow">
       <th>Watcher</th>
       <th>Last Active</th>
      </tr>
<?php
  $rowclass = begin_row_class();
  
  foreach($absent_watchers as $watcher)
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><?= resident_link($watcher['display']) ?></td>
       <td><?= Duration($now - $watcher['lastactivity'], 2) ?> ago</td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';

  if($user['admin']['manageplaza'] == 'yes')
  {
    $command = "SELECT * FROM monster_reports ORDER BY reports DESC";
    $reports = $database->FetchMultiple($command, 'fetching mis-placed thread reports');

    if(count($reports) > 0)
    {
      echo '
        <h5>Move Requests</h5>
        <table>
         <tr class="titlerow">
          <th></th><th>Reports</th><th>Plaza</th><th>Thread</th>
         </tr>
      ';
      
      $bgcolor = begin_row_class();

      foreach($reports as $report)
      {
        $command = 'SELECT title,plaza FROM monster_threads WHERE idnum=' . quote_smart($report['threadid']) . ' LIMIT 1';
        $this_thread = $database->FetchSingle($command, 'fetching reported thread');

        $command = 'SELECT title FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
        $this_plaza = $database->FetchSingle($command, 'fetching plaza info');
?>
        <tr class="<?= $bgcolor ?>">
         <td><input type="checkbox" name="<?= $report['threadid'] ?>" /></td>
         <td class="righted"><?= $report['reports'] ?></td>
         <td><?= $this_plaza['title'] ?></td>
         <td><a href="/viewthread.php?threadid=<?= $report['threadid'] ?>"><?= $this_thread['title'] ?></a></td>
        </tr>
<?php
        $bgcolor = alt_row_class($bgcolor);
      }

      echo '</table>';
    }
  }


  echo '<h5>Plaza Forums</h5>';
}
?>
     <ul>
      <li><a href="/plaza/search.php">Search the Plaza</a></li>
     </ul>
<?php
//include 'commons/bcmessage.php';
?>
     <table>
      <tr class="titlerow">
       <th>&nbsp;</th>
       <th>Title</th>
       <th class="centered">Posts</th>
       <th class="centered">Last&nbsp;Post</th>
      </tr>
<?php
$thread_num = 0;
$group_mode = false;
$group_index = 0;
$plaza_index = 0;
$keep_going = true;

$bgcolor = begin_row_class();

// go through each plaza (from $plaza_result)
while(1)
{
  if(!$group_mode)
  {
		if($plaza_index == count($plazas))
		{
			break;
		}
		
    $plaza = $plazas[$plaza_index];
		$plaza_index++;
  }
  else
  {
    if($group_index == count($group_forums))
    {
      $group_mode = false;
      continue;
    }

    $plaza = $group_forums[$group_index];
    $group_index++;
  }

  if(substr($plaza['title'], 0, 1) == '#')
  {
    if($plaza['title'] == '#My Groups')
    {
      if(count($group_forums) > 0)
        $group_mode = true;
      else
        continue;
    }
?>
      <tr class="<?= $bgcolor ?>">
       <td colspan="4">
        <b><?= substr($plaza['title'], 1) ?></b>
       </td>
      </tr>
<?php
  }
  else
  {
    if($plaza['groupid'] == 0)
      $tags = ' height="32" width="32"';
    else
      $tags = '';

    echo '<tr class="' . $bgcolor . ' firstcellinrow">' .
         '<td rowspan="3" valign="top" align="center">';

    // if the last visit to this plaza was earlier than the latest post to this plaza
    if($lastvisit[$plaza['idnum']]['lastread'] < $plaza['updatedate'])
    {
       // display the row with extra <b> tags
      if($plaza['graphic'] != '')
        echo '<a href="/viewplaza.php?plaza=' . $plaza['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/' . $plaza['graphic'] . '" alt="' . $plaza['title'] . '"' . $tags . ' /></a>';
?>
       </td>
       <td><b><?= $plaza['locked'] == 'yes' ? '<img src="/gfx/lock.gif" width="16" height="16" alt="locked" /> ' : '' ?><a href="/viewplaza.php?plaza=<?= $plaza['idnum'] ?>"><?= format_text($plaza['title']) ?></a></b></td>
       <td align="center"><b><?= $plaza['replies'] ?></b></td>
       <td align="center"><b><?= Duration($now - $plaza['updatedate'], 2) ?> ago</b></td>
<?php
    }
    // else (we've already been here recently)
    else
    {
       // display the row without <b> tags
      if($plaza['graphic'] != '')
        echo '<a href="/viewplaza.php?plaza=' . $plaza['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/' . $plaza['graphic'] . '" alt="' . $plaza['title'] . '"' . $tags . ' class="transparent_image" /></a>';
?>
       </td>
       <td><?= $plaza['locked'] == 'yes' ? '<img src="/gfx/lock.gif" width="16" height="16" alt="locked" /> ' : '' ?><a href="/viewplaza.php?plaza=<?= $plaza['idnum'] ?>"><?= format_text($plaza['title']) ?></a></td>
       <td align="center"><?= $plaza['replies'] ?></td>
       <td align="center"><?= Duration($now - $plaza['updatedate'], 2) ?> ago</td>
<?php
    }

    echo '</tr>';

    $moderators = array();
   
    $moderator = array();
   
    // explode the list of this plaza's watchers into the array $mods
    $mods = explode(',', $plaza['admins']);

    foreach($mods as $idnum)
    {
      // if we don't have information on this watcher, get it
      if($moderator[$idnum]['idnum'] != $idnum)
        $moderator[$idnum] = get_user_byid($idnum, 'display');

      // add the link to this watcher's profile in the array $moderators
      $moderators[] = '<a href="/userprofile.php?user=' . link_safe($moderator[$idnum]['display']) . '">' . $moderator[$idnum]['display'] . '</a>';
    }

    // give the plaza's description, and list its watchers
?>
      <tr class="<?= $bgcolor ?> firstcellinrow">
       <td colspan="3" style="padding-top:0;">
        <?= $plaza['description'] ?>
       </td>
      </tr>
      <tr class="<?= $bgcolor ?>">
       <td colspan="3" style="padding-top:0;">
        <span class="size8" onmouseover="Tip('Watchers can sticky and move threads, and give them special icons,<br />but that is all.  Other issues should be directed to an administrator.')">&nbsp;&nbsp;&nbsp;watchers: <?= implode(', ', $moderators) ?></span>
       </td>
      </tr>
<?php
    // alternate background color
    $bgcolor = alt_row_class($bgcolor);
  } // is a plaza (not just a heading)
} // for each entry
?>
     </table>
     <ul>
      <li><a href="/grouppage.php?id=38">Watcher FAQ</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
