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

function render_followers($friends, $sort_by, $sorty_bits = true)
{
  global $now;
?>
<table>
 <thead>
  <tr class="titlerow">
   <th></th>
   <th><nobr>Name <?php if($sorty_bits) echo ($sort_by == 'b.display ASC' ? '&#9660;' : '<a href="?sort=name">&#9661;</a>'); ?></nobr></th>
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
   <td rowspan="2"><a href="/residentprofile.php?resident=<?= link_safe($name) ?>"><img src="<?php echo user_avatar($friend); ?>" width="48" height="48" alt="" /></a></td>
   <td valign="bottom"><a href="/residentprofile.php?resident=<?= link_safe($name) ?>"><?= $name ?></a></td>
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

if($_POST['action'] == 'addbyname')
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

if($_GET['sort'] == 'name')
  $sort_by = 'b.display ASC';
else if($_GET['sort'] == 'profile')
  $sort_by = 'c.last_update DESC';
else
  $sort_by = 'b.lastactivity DESC';

$exclude_ids = array();

$friends = $database->FetchMultiple('
  SELECT friendid
  FROM psypets_user_friends
  WHERE userid=' . (int)$user['idnum'] . '
');

foreach($friends as $friend)
  $exclude_ids[] = $friend['friendid'];

$enemies = $database->FetchMultiple('
  SELECT enemyid
  FROM psypets_user_enemies
  WHERE userid=' . (int)$user['idnum'] . '
');

foreach($enemies as $enemy)
  $exclude_ids[] = $enemy['enemyid'];
  
$followers = $database->FetchMultiple('
  SELECT b.idnum,b.is_npc,b.display,b.lastactivity,b.openstore,b.license,b.graphic,b.is_a_whale,b.museumcount,c.last_update
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.userid=b.idnum
    LEFT JOIN psypets_profile_text AS c
      ON a.userid=c.player_id
  WHERE
    a.friendid=' . (int)$user['idnum'] . '
    ' . (count($exclude_ids) > 0 ? 'AND userid NOT IN(' . implode(', ', $exclude_ids) . ')' : '') . '
  ORDER BY ' . $sort_by . '
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Followers</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>My Followers</h5>
		 <ul class="tabbed">
		  <li><a href="/myfriends.php">My Friends</a></li>
			<li><a href="/myignorelist.php">My Ignore List</a></li>
		  <li class="activetab"><a href="/myfollowers.php">My Followers</a></li>
		 </ul>
     <p>These people are <em>not</em> on your friend list... but you're on theirs!</p>
     <p>It only seems fair that if they get to keep tabs on you (when you're online, and stuff), then you should get to keep tabs on them!  So here they are! <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/nyeh.gif" class="inlineimage" width="16" height="16" alt="[razz]" /></p>
<?php
if(count($followers) >= 10)
{
  $followers2 = array_slice($followers, floor(count($followers) / 2));
  $followers = array_slice($followers, 0, floor(count($followers) / 2));
  echo '<div style="float: left; width:430px;">';
  render_followers($followers, $sort_by);
  echo '</div><div style="margin-left:446px; width:430px;">';
  render_followers($followers2, false, false);
  echo '</div><div style="clear:both;"></div>';
}
else if(count($followers) > 0)
  render_followers($followers, $sort_by);
else
  echo '<p>Oh: except either no one has friended you yet, <em>or</em> you\'ve friended everyone who friended you!  (Goodness!)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
