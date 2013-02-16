<?php
$wiki = 'My_Friends';
$child_safe = false;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/utility.php';

function render_friends($friends, $stalkers, $sort_by, $sorty_bits = true)
{
  global $now;
?>
<table>
 <thead>
  <tr class="titlerow">
   <th></th>
   <th></th>
   <th><nobr>Name <?php if($sorty_bits) echo ($sort_by == 'b.display ASC' ? '&#9660;' : '<a href="?sort=name">&#9661;</a>'); ?></nobr></th>
   <th></th>
   <th class="centered"><nobr>Last&nbsp;Active <?php if($sorty_bits) echo ($sort_by == 'b.lastactivity DESC' ? '&#9660;' : '<a href="?sort=activity">&#9661;</a>'); ?></nobr></th>
   <th class="centered"><nobr>Updated&nbsp;Profile <?php if($sorty_bits) echo ($sort_by == 'c.last_update DESC' ? '&#9660;' : '<a href="?sort=profile">&#9661;</a>'); ?></nobr></th>
  </tr>
 </thead>
 <tbody>
<?php
  $bgcolor = begin_row_class();

  foreach($friends as $friend)
  {
    $name = $friend['display'];

    if($friend !== false)
    {
      if($friend['is_npc'] == 'yes')
        $friend['lastactivity'] = $now;
?>
  <tr class="<?= $bgcolor ?>">
   <td rowspan="2">
    <form method="post">
     <input type="hidden" name="action" value="rembuddy,<?= link_safe($friend['display']) ?>" height="16" width="16" />
     <input type="image" src="gfx/remove_buddy.gif" height="16" width="16" alt="Remove from Friend List" title="Remove from Friend List" />
    </form>
   </td>
   <td rowspan="2"><a href="/residentprofile.php?resident=<?= link_safe($name) ?>"><img src="<?php echo user_avatar($friend); ?>" width="48" height="48" alt="" /></a></td>
   <td valign="bottom"><a href="/residentprofile.php?resident=<?= link_safe($name) ?>"><?= $name ?></a></td>
   <td rowspan="2"><?= array_key_exists($friend['idnum'], $stalkers) ? '<span title="(mutual friends)" style="color:red;">&hearts;</span>' : '' ?></td>
   <td rowspan="2" class="centered"><?= Duration($now - $friend['lastactivity'], 1) ?> ago</td>
   <td rowspan="2" class="centered"><?= $friend['last_update'] > 0 ? Duration($now - $friend['last_update'], 1) . ' ago' : 'never' ?></td>
  </tr>
  <tr class="<?= $bgcolor ?>">
   <td valign="top">
<?php
      echo '<a href="/writemail.php?sendto=' . link_safe($name) . '"><img src="/gfx/sendmail.gif" alt="Write Mail" title="Write Mail" height="16" width="16" /></a>';

      if($friend['license'] == 'yes')
        echo '<a href="/newtrade.php?user=' . link_safe($name) . '"><img src="/gfx/dotrade.gif" alt="Initiate Trade" title="Initiate Trade" height="16" width="16" /></a>';
      if($friend['openstore'] == 'yes')
        echo '<a href="/userstore.php?user=' . link_safe($name) . '"><img src="/gfx/forsale.png" width="16" height="16" alt="Visit Store" title="Visit Store" /></a>';
      if($friend['museumcount'] >= 100)
        echo '<a href="/museum/view.php?resident=' . link_safe($name) . '"><img src="/gfx/museum.png" width="16" height="16" alt="View Museum Wing" title="View Museum Wing" /></a>';
?>
   </td>
  </tr>
<?php
    }
    else
    {
?>
  <tr class="<?= $bgcolor ?>">
   <form method="post">
   <input type="hidden" name="action" value="rembuddy,<?= $display ?>" height="16" width="16" />
   <td><input type="image" src="/gfx/remove_buddy.gif" title="Remove from Friend List" /></td>
   </form>

   <td colspan="4"><i class="dim">[departed #<?= $display ?>]</i></td>
  </tr>
<?php
    }

    $bgcolor = alt_row_class($bgcolor);
  }
?>
 </tbody>
</table>
<?php
}

if(substr($_POST['action'], 0, 9) == 'rembuddy,')
{
  $target = substr($_POST['action'], 9);

  $friend = get_user_bydisplay($target, 'idnum,display,lastactivity');
  if($friend === false)
  {
    $friend['idnum'] = $target;
    $false_friend = true;
  }
  else
    $false_friend = false;

  remove_friend($user, $friend);

  if($false_friend)
    $CONTENT['messages'][] = '<span class="success">"<i>[departed #' . $friend['idnum'] . ']</i>" has been removed from your friends list.</span>';
  else
    $CONTENT['messages'][] = '<span class="success">"' . $friend['display'] . '" has been removed from your friends list.</span>';
}
else if($_POST['action'] == 'addbyname')
{
  $targetuser = get_user_bydisplay($_POST['displayname']);

  if($targetuser !== false)
  {
    add_friend($user, $targetuser);
    remove_enemy($user, $targetuser);
    $CONTENT['messages'][] = '<span class="success">"' . $targetuser['display'] . '" has been added to your friends list.</span>';
  }
  else
    $CONTENT['messages'][] = '<span class="failure">There is no resident named "' . $_POST['displayname'] . '"</span>';
}

if($user['newfriend'] == 'yes')
{
  $command = 'UPDATE monster_users SET newfriend=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'clearing new friend report flag');

  $user['newfriend'] = 'no';
}

if($_POST['submit'] == '\'kay~!')
{
  $lastid = (int)$_POST['lastid'];
  if($lastid > 0)
  { 
    $command = 'DELETE FROM psypets_friendreport WHERE userid=' . $user['idnum'] . ' AND idnum<=' . $lastid;
    $database->FetchNone($command, 'deleting friending reports');
  }
}

$command = 'SELECT idnum,timestamp,friendedby FROM psypets_friendreport WHERE userid=' . $user['idnum'] . ' ORDER BY idnum DESC';
$friend_reports = $database->FetchMultiple($command, 'fetching new friend reports');

if($_GET['sort'] == 'name')
  $sort_by = 'b.display ASC';
else if($_GET['sort'] == 'profile')
  $sort_by = 'c.last_update DESC';
else
  $sort_by = 'b.lastactivity DESC';

$friends = $database->FetchMultiple('
  SELECT b.idnum,b.is_npc,b.display,b.lastactivity,b.openstore,b.license,b.graphic,b.is_a_whale,b.museumcount,c.last_update
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.friendid=b.idnum
    LEFT JOIN psypets_profile_text AS c
      ON a.friendid=c.player_id
  WHERE a.userid=' . (int)$user['idnum'] . '
  ORDER BY ' . $sort_by . '
');

foreach($friends as $friend)
  $friend_ids[] = $friend['idnum'];

$stalkers = $database->FetchMultipleBy('
  SELECT a.userid
  FROM psypets_user_friends AS a
  WHERE
    a.friendid=' . (int)$user['idnum'] . '
', 'userid');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Friends</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>My Friends</h5>
		 <ul class="tabbed">
		  <li class="activetab"><a href="/myfriends.php">My Friends</a></li>
			<li><a href="/myignorelist.php">My Ignore List</a></li>
		  <li><a href="/myfollowers.php">My Followers</a></li>
		 </ul>
<?php
if(count($friend_reports) > 0)
{
?>
     <h5>New Friendings</h5>
     <table>
      <tr class="titlerow">
       <th>Who</th><th></th><th class="centered">When</th>
      </tr>
<?php
  $rowclass = begin_row_class();
  
  foreach($friend_reports as $report)
  {
    $friender = get_user_byid($report['friendedby'], 'display');
    if($friender === false)
      $display = '<i class="dim">[Departed #' . $report['friendedby'] . ']</i>';
    else
      $display = resident_link($friender['display']);
?>
      <tr class="<?= $rowclass ?>">
       <td><?= $display ?></td>
       <td><?= in_array($report['friendedby'], $friend_ids) ? '<span title="(mutual friends)" style="color:red;">&hearts;</span>' : '' ?></td>
       <td class="centered"><?= Duration($now - $report['timestamp']) ?> ago</td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <form method="post">
     <p><input type="hidden" name="lastid" value="<?= $friend_reports[0]['idnum'] ?>" /><input type="submit" name="submit" value="'kay~!" /></p>
     </form>
     <h5>Friends List</h5>
<?php
}
?>
     <form method="post">
     <p><input name="displayname" maxlength="32" /> <input type="hidden" name="action" value="addbyname" /><input type="submit" value="Quick Add" /></p>
     </form>
<?php
if(count($friends) >= 10)
{
  $friends2 = array_slice($friends, floor(count($friends) / 2));
  $friends = array_slice($friends, 0, floor(count($friends) / 2));
  echo '<div style="float: left; width:430px;">';
  render_friends($friends, $stalkers, $sort_by);
  echo '</div><div style="margin-left:446px; width:430px;">';
  render_friends($friends2, $stalkers, false, false);
  echo '</div><div style="clear:both;"></div>';
}
else if(count($friends) > 0)
  render_friends($friends, $stalkers, $sort_by);
else
  echo '<p>You haven\'t added anyone to your friend list... <em>yet!</em></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
