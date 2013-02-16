<?php
$child_safe = false;
$require_petload = 'no';
$require_login = 'no';

if($_GET['resident'] == $SETTINGS['site_ingame_mailer'])
{
  header('Location: /cityhall.php');
  exit();
}

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/utility.php';
require_once 'commons/totemlib.php';
require_once 'commons/badges.php';
require_once 'commons/blimplib.php';
require_once 'commons/messages.php';
require_once 'commons/petblurb.php';
require_once 'commons/backgrounds.php';
require_once 'commons/profiles.php';
require_once 'commons/fireplacelib.php';

$profile_user = get_user_bydisplay(trim($_GET['resident']));

if($profile_user === false)
{
  header('Location: /directory.php?msg=159:' . trim($_GET['resident']));
  exit();
}

if($profile_user['is_npc'] == 'yes')
{
  header('Location: /npcprofile.php?npc=' . link_safe($profile_user['display']));
  exit();
}

if($user['idnum'] == 0)
{
  header('Location: /publicprofile.php?resident=' . link_safe($profile_user['display']));
  exit();
}

if(($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no') && $user['admin']['manageaccounts'] !== 'yes')
{
  header('Location: /directory.php');
  exit();
}

$badges = get_badges_byuserid($profile_user['idnum']);
$profile_text = get_user_profile_text($profile_user['idnum']);

$pet_time = microtime(true);

$command = 'SELECT * ' .
           'FROM monster_pets ' .
           'WHERE `user`=' . quote_smart($profile_user['user']) . ' AND location=\'home\' ORDER BY orderid,idnum ASC';
$profile_pets = $database->FetchMultiple($command, 'fetching pets at home');

$command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=' . quote_smart($profile_user['user']) . ' AND location=\'shelter\'';
$data = $database->FetchSingle($command, 'fetching daycared pets count');

$daycared_pets = (int)$data['c'];

$p_love_options = '';
$p_love_ajax_post = '';

foreach($profile_pets as $profile_pet)
{
  if(
    $profile_pet['dead'] == 'no' && $profile_pet['zombie'] == 'no' && $profile_pet['changed'] == 'no' && $profile_pet['sleeping'] == 'no' &&
    (
      $profile_pet['last_love'] < $now - 60 * 60 ||
      ($profile_pet['last_love'] < $now - 30 * 60 && $profile_pet['last_love_by'] != $profile_user['idnum'])
    )
  )
  {
    $p_love_ajax_post .= '&love' . $profile_pet['idnum'] . '=yes';
    $p_love_options .= '<input type="hidden" name="love' . $profile_pet['idnum'] . '" value="yes" />';
    $p_love_pets++;
  }
}

$pet_time = microtime(true) - $pet_time;

$command = 'SELECT * ' .
           'FROM monster_admins ' .
           'WHERE `user`=' . quote_smart($profile_user['user']) . ' LIMIT 1';
$profile_admin = $database->FetchSingle($command, 'userprofile.php');

$update_list = false;

$searchable_profile = get_user_profile($profile_user['idnum']);

if($_POST['action'] == 'addbuddy')
{
  $messages[] = add_friend($user, $profile_user);
  remove_enemy($user, $profile_user);
}
else if($_POST['action'] == 'rembuddy')
{
  $messages[] = remove_friend($user, $profile_user);
}
else if($_POST['action'] == 'addignore')
{
  add_enemy($user, $profile_user);
  $messages[] = remove_friend($user, $profile_user);
}
else if($_POST['action'] == 'remignore')
{
  remove_enemy($user, $profile_user);
}

$mantle_items = array();

// get mantle items
$fireplace = get_fireplace_byuser($profile_user['idnum']);
if($fireplace !== false)
{
  $command = 'SELECT a.*,b.graphic,b.graphictype,a.message,a.message2 FROM monster_inventory AS a,monster_items AS b WHERE a.itemname=b.itemname AND a.user=' . quote_smart($profile_user['user']) . " AND a.location='fireplace'";
  $mantle_items = $database->FetchMultipleBy($command, 'idnum', 'userprofile.php');

  if(strlen($fireplace['mantle']) > 0)
    sort_items_by_mantle($mantle_items, explode(',', $fireplace['mantle']));
}

$treasure_time = microtime(true);

// get profile items from inventory
$display_items = get_display_items($profile_user);

$treasure_time = microtime(true) - $treasure_time;

if($profile_user['idnum'] == $user['idnum'])
{
  if($user['newcomment'] == 'yes')
  {
    $database->FetchNone('UPDATE monster_users SET newcomment=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

    $user['newcomment'] = 'no';
  }
  
  require_once 'commons/questlib.php';

  $profile_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my profile');
  if($profile_tutorial_quest === false)
    $no_tip = true;
}

if($profile_user['meteor'] == 'yes')
{
  $CONTENT_STYLE = 'background: url(\'/gfx/walls/meteor.png\') no-repeat';
}
else if(strlen($profile_user['profile_wall']) > 0)
{
  $CONTENT_STYLE = 'background: url(\'/gfx/' . $profile_user['profile_wall'] . '\')';

  if($profile_user['profile_wall_repeat'] == 'no')
    $CONTENT_STYLE .= ' no-repeat';
  else if($profile_user['profile_wall_repeat'] == 'horizontal')
    $CONTENT_STYLE .= ' repeat-x';
  else if($profile_user['profile_wall_repeat'] == 'vertical')
    $CONTENT_STYLE .= ' repeat-y';

  $CONTENT_STYLE .= ';';
}

$CONTENT_CLASS = 'profilepadded';

if($badges['worstideaever'] == 'no' && $user['idnum'] != $profile_user['idnum'])
{
  // if the browsing account is older than the profile's account by one year, or the browsing account was one
  // of the first 10 created, then the browsing account may give the profile's account the 'worstideaever' badge.
  if($user['signupdate'] <= $profile_user['signupdate'] - 365 * 24 * 60 * 60 || $user['idnum'] <= 10)
    $give_worst_idea_ever_badge = 'yes';
  else
    $give_worst_idea_ever_badge = 'no';
}
else
  $give_worst_idea_ever_badge = false;

if($user['fireworks'] != '' && $user['idnum'] == $profile_user['idnum'])
{
  require_once 'commons/threadfunc.php';

  $firework_string = '<div><p>How will you decorate your profile?</p><table>';

  $fireworks = explode(',', $user['fireworks']);

  foreach($fireworks as $firework)
  {
    list($fireworkid, $quantity) = explode(':', $firework);

    if($user['profile_wall'] == 'postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png')
      $firework_string .= '<tr style="border-top: 1px solid #000;"><td style="background-image: url(/gfx/postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png); text-align: center;"><img src="/gfx/shim.png" width="260" height="50" alt="" /><p><span class="dim">Like this!</span> (' . $quantity . ' available)</p><img src="gfx/shim.png" width="260" height="50" alt="" /></td>';
    else
      $firework_string .= '<tr style="border-top: 1px solid #000;"><td style="background-image: url(/gfx/postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png); text-align: center;"><img src="/gfx/shim.png" width="260" height="50" alt="" /><p><a href="giveprofilebackground.php?firework=' . $fireworkid . '">Like this!</a> (' . $quantity . ' available)</p><img src="/gfx/shim.png" width="260" height="50" alt="" /></td>';
  }

  $firework_string .= '</table><center>[ <a href="#" onclick="firework_hide(); return false;">oops! nvm!</a> ]</center></div>';

  $firework_link = ' <a href="#" onclick="firework_popup(0, \'\'); return false;"><img src="/gfx/fireworks.png" width="16" height="16" alt="Apply Background" /></a>';
}

include 'commons/html.php';
?>
 <head>
<?php
include 'commons/head.php';
?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Profile</title>
<?php
if($firework_string != '')
{
?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/scrolldetect.js"></script>
  <script type="text/javascript">
   var firework_string = '<?= $firework_string ?>';
  </script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/thread.js"></script>
<?php
}
?>
  <script type="text/javascript">
   function ajax_love()
   {
     $('#petpets').html('<img src="/gfx/throbber.gif" />');
   
     $.ajax({
       type: 'post',
       url: 'lovefriendspets.php',
       data: 'resident=<?= urlencode($profile_user['display']) ?>&ajax=yes<?= $p_love_ajax_post ?>',
       success: function(msg)
       {
         $('#petpets').html(msg);
       }
     });
   }
   
   function ajax_comment()
   {
     $('#commentbutton').html('<img src="/gfx/throbber.gif" />');
     $('#commentfield').attr('disabled', 'disabled');
     
     $.ajax({
       type: 'post',
       url: 'leavecomment.php',
       data: 'resident=<?= urlencode($profile_user['display']) ?>&ajax=yes&comment=' + encodeURIComponent($('#commentfield').val()),
       success: function(msg)
       {
         if(msg == 'failed')
         {
           $('#commentbutton').html('<span class="failure">Cannot leave a comment on this Resident\'s profile.</span>');
           $('#commentmessage').hide();
         }
         else if(msg == 'blank')
         {
           $('#commentbutton').html('<input type="submit" value="Leave Comment" class="bigbutton" />');
           $('#commentfield').removeAttr('disabled');
           $('#commentmessage').html('<p class="failure">Oops!  Your message was blank!</p>');
           $('#commentmessage').show();
         }
         else
         {
           $('#commentform').hide();
           $('#newcomment').html(msg);
           $('#commentmessage').hide();
         }
       }
     });
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($profile_tutorial_quest === false)
{
  include 'commons/tutorial/myprofile.php';
  add_quest_value($user['idnum'], 'tutorial: my profile', 1);
}

if($firework_string != '')
{
?>
     <div class="shadowed-box" id="kitchen" style="display: none;"><div><center>
      <br /><br /><img src="/gfx/throbber.gif" width="16" height="16" /><br /><br /><br />
     </center></div></div>
<?php
}

if($profile_user['cornergraphic'] != '')
  echo '<div id="cobweb"><img src="/gfx/' . $profile_user['cornergraphic'] . '" width="256" height="192" /></div>';
?>
     <table>
      <tr>
       <td valign="top"><img src="<?= user_avatar($profile_user) ?>" width="48" height="48" alt="" /></td>
       <td valign="top"><h4 style="margin: -4px 0 4px 0;"><?= $profile_user['display'] . ', ' . $profile_user['title'] ?></h4>
<div id="badges"><?php
foreach($badges as $badge=>$value)
{
  if($value == 'yes')
    echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/' . $badge . '.png" height="20" width="20" title="' . $BADGE_DESC[$badge] . '" /> ';
}
?></div>
       </td>
      </tr>
     </table>
     <ul>
<?php
if($admin['manageaccounts'] == 'yes')
{
  if($profile_user['is_a_bad_person'] == 'yes')
    $notes[] = 'GL suspension';

  if(count($notes) > 0)
    $note = '; ' . implode(', ', $notes);
  else
    $note = '';
}

if($admin['clairvoyant'] == 'yes')
{
  echo '<p>Login: <a href="/admin/resident.php?user=' . $profile_user['user'] . '" title="lookup">' . $profile_user['user'] . '</a> (#' . $profile_user['idnum'] . ')' . $note;
  if($profile_user['disabled'] == 'yes') echo ', <span class="failure">login disabled</span>'; 
  if($profile_user['childlockout'] == 'yes') echo ', child lock-out ENABLED';
  echo '</p>';
}

if($profile_user['idnum'] == $user['idnum'])
  echo '      <li><a href="/myaccount/profile.php">Edit my profile</a></li>';
else if($admin["managedonations"] == "yes")
  echo '      <li><a href="/myaccount/favorhistory.php?idnum=' . $profile_user['idnum'] . '">View Favor history</a></li>';

if($admin['manageaccounts'] == 'yes')
{
  echo '
    <li><a href="/admin/residentwarnings.php?resident=' . link_safe($profile_user['display']) . '">View abuse history</a></li>
    <li><a href="/admin/tracktrades.php?resident=' . link_safe($profile_user['display']) . '">View trade history</a></li>
    <li><a href="/myaccount/loginhistory.php?as=' . $profile_user['idnum'] . '">View login history</a></li>
    <li><a href="/admin/residentplazause.php?userid=' . $profile_user['idnum'] . '">View Plaza post counts</a></li>
  ';
}
?>
      <li><a href="http://<?= $SETTINGS['wiki_domain'] ?>/User:<?= $profile_user['display'] ?>">View resident's PsyHelp entry</a></li>
     </ul>
<?php
if($profile_user['idnum'] != $user['idnum'])
{
  include 'commons/residentprofile_actions.php';
}

if($profile_text === false)
  $profile_xhtml = 'Encourage this Resident to post a profile!';
else if($now_month == 1 && $now_day == 18 && $now_year == 2012)
  $profile_xhtml = '<a href="/viewthread.php?threadid=72226">CENSORED</a>';
else
  $profile_xhtml = format_text($profile_text['text']);
?>
     <h5>Resident Profile<?= $firework_link ?></h5>
     <div class="profiletext userformatting">
      <?= $profile_xhtml ?>
     </div>
<?php
if(count($searchable_profile) > 0 && $searchable_profile['enabled'] == 'yes')
{
  echo '<table>';
  include 'commons/residentprofile_searchable.php';
  echo '</table>';
}

echo '<table>';

$virgin_post_time = microtime(true);

$command = 'SELECT a.idnum,a.threadid,a.title,b.title AS thread_title FROM monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum WHERE a.createdby=' . $profile_user['idnum'] . ' ORDER BY a.idnum ASC LIMIT 1';
$first_post = $database->FetchSingle($command, 'fetching virgin post');

$virgin_post_time = microtime(true) - $virgin_post_time;

if($first_post !== false)
{
  if($first_post['title'] == '')
    $title = '[untitled]';
  else
    $title = $first_post['title'];
?>
      <tr>
       <td valign="top"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/cherries.png" width="16" height="16" alt="" /></td>
       <td>Virgin post: <a href="/jumptopost.php?postid=<?= $first_post['idnum'] ?>"><?= $title ?></a> in <a href="viewthread.php?threadid=<?= $first_post['threadid'] ?>"><?= $first_post['thread_title'] ?></a></td>
      </tr>
<?php
}

if($profile_user['stickers_given'] > 0)
{
?>
      <tr>
       <td valign="top"><img src="/gfx/goldstar.png" width="16" height="16" alt="" /></td>
       <td>Has been given <?= $profile_user['stickers_given'] ?> gold star stickers (<a href="specialposts.php?resident=<?= $profile_user["display"] ?>">list of starred posts by <?= $profile_user["display"] ?></a>)</td>
      </tr>
<?php
}
?>
     </table>
     <table>
      <tr><td valign="top" style="padding-right: 1em;">
     <h5>Resident Information</h5>
     <table>
<?php
if($profile_user['publicfriends'] == 'yes')
{
  $friends = $database->FetchMultiple('
    SELECT b.idnum,b.is_npc,b.display,b.lastactivity,b.openstore,b.license,b.graphic,b.is_a_whale,b.museumcount,c.last_update
    FROM psypets_user_friends AS a
      LEFT JOIN monster_users AS b
        ON a.friendid=b.idnum
      LEFT JOIN psypets_profile_text AS c
        ON a.friendid=c.player_id
    WHERE a.userid=' . (int)$profile_user['idnum'] . '
    ORDER BY b.display ASC
  ');
  
  if(count($friends) > 0)
  {
?>
      <tr>
       <td><img src="/gfx/friends.gif" width="16" height="16" alt="" /></td>
       <td>Has Friends</td>
      </tr>
      <tr>
       <td></td>
       <td>
        <table class="nomargin">
         <tr>
          <td style="padding-left:0;">
        <ul class="plainlist" valign="top">
<?php
    if(count($friends) >= 9)
    {
      $columns = array_chunk($friends, ceil(count($friends) / 3), true);

      foreach($columns[0] as $friend)
        echo '<li>' . resident_link($friend['display']) . '</li>';

      echo '</ul></td><td style="padding-left:2em;" valign="top"><ul class="plainlist">';

      foreach($columns[1] as $friend)
        echo '<li>' . resident_link($friend['display']) . '</li>';

      echo '</ul></td><td style="padding-left:2em;" valign="top"><ul class="plainlist">';

      foreach($columns[2] as $friend)
        echo '<li>' . resident_link($friend['display']) . '</li>';
    }
    else
      foreach($friends as $friend)
        echo '<li>' . resident_link($friend['display']) . '</li>';
?>
        </ul>
          </td>
         </tr>
        </table>
       </td>
      </tr>
<?php
  }
}

if(strlen($profile_user['groups'] > 0))
{
  require_once 'commons/grouplib.php';
?>
      <tr>
       <td><img src="/gfx/friends.gif" width="16" height="16" alt="" /></td>
       <td>Belongs to Groups</td>
      </tr>
      <tr>
       <td></td>
       <td>
        <ul class="plainlist">
<?php
  $groupids = take_apart(',', $profile_user['groups']);
  $groupnames = array();

  foreach($groupids as $groupid)
    $groups[$groupid] = get_group_name_byid($groupid);

  foreach($groups as $groupid=>$groupname)
    echo '<li><a href="grouppage.php?id=' . $groupid . '">' . $groupname . '</a></li>';
?>
        </ul>
       </td>
      </tr>
<?php
}
?>
      <tr>
       <td valign="top"><img src="/gfx/timer.gif" width="16" height="16" alt="" /></td>
       <td>
        <p>
         Signed up <?= Duration($now - $profile_user['signupdate'], 2) ?> ago (on <?= local_date($profile_user['signupdate'], $user['timezone'], $user['daylightsavings']) ?>)<br />
         Last active <?= Duration($now - $profile_user['lastactivity'], 2) ?> ago<br />
<?php
if($admin['manageaccounts'] == 'yes')
{
  require_once 'commons/houselib.php';

  echo '
    <div style="width: 400px;">
    Last logged in from <a href="http://www.geobytes.com/IpLocator.htm?GetLocation&ipaddress=' . $profile_user['last_ip_address'] . '">' . $profile_user['last_ip_address'] . '</a><br />
    Last logged in on ' . local_time($profile_user['logintime'], $user['timezone'], $user['daylightsavings']) . '<br />
    Has logged in ' . $profile_user['logins'] . ' times<br />
    Registered birthday is ' . $profile_user['birthday'] . '<br />
    Last-known client version: ' . $profile_user['lastclient'] . '
  ';

	$house = get_house_byuser($user['idnum']);

	if($house === false)
		echo '<b class="failure">Failed to load house!  Has no house!?</b>';
  else
		echo 'Has earned ' . $house['hoursearned'] . ' hours toward allowance and will get its next hour at ' . local_time($house['lasthour'] + (60 * 60), $user['timezone'], $user['daylightsavings']) . '<br />';

  echo '</div>';
}

echo '
    </p>
   </td>
  </tr>
';

if($profile_user['license'] == 'yes')
{
  echo '<tr><td valign="top">';

  if($profile_user['openstore'] == 'yes')
  {
    echo '<a href="userstore.php?user=' . $profile_user['display'] . '"><img src="/gfx/forsale.png" width="16" height="16" border="0" alt="" /></a>';
    $store_is = 'open';
  }
  else
  {
    echo '<img src="/gfx/forsale.png" width="16" height="16" alt="" />';
    $store_is = 'closed';
  }
  
  echo '</td><td>This store is currently ' . $store_is;

  if($store_is == 'open')
    echo ' (<a href="userstore.php?user=' . $profile_user['display'] . '">visit user store</a>)';

  echo '.<br />';

  if($profile_user['totalsells'] > 0)
    echo $profile_user['totalsells'] . ' items have been sold for a total of ' . $profile_user['totalvalue'] . '<span class="money">m</span>.<br />';

  echo '</td></tr>';
}

if($admin['clairvoyant'] == 'yes')
{
?>
      <tr>
       <td valign="top" class="centered"><span class="money">m</span></td>
       <td>
        <?= $profile_user['money'] ?> cash<br />
        <?= $profile_user['savings'] ?> in the bank<br />
       </td>
      </tr>
<?php
}

if($profile_admin["admintag"] == "yes")
{
?>
      <tr>
       <td valign="top"><a href="admincontact.php"><img src="/gfx/admintag.gif" width=16 height=16 border=0 alt="" /></a></td>
       <td><?= $SETTINGS['site_name'] ?> administrator</td>
      </tr>
<?php
}

echo '</table>';

$totem = get_totem_byuserid($profile_user['idnum']);

if(($totem !== false && $user['show_totemgardern'] == 'yes') || count($display_items) > 0 || count($mantle_items) > 0)
{
  echo '<h5 id="profileitems">Awards, Medals, Trophies and Treasures</h5>';

  if($totem !== false)
    echo '<ul>';

  if($totem !== false && $user['show_totemgardern'] == 'yes')
  {
    $pieces = take_apart(',', $totem['totem']);
    $height = '(of ' . count($pieces) . ' totem' . (count($pieces) != 1 ? 's' : '') . ')';
    echo '<li><a href="/totempoles.php?resident=' . link_safe($profile_user['display']) . '">View totem pole</a> ' . $height . '</li>';
  }

  if($profile_user['museumcount'] >= 100)
    echo '<li><a href="/museum/view.php?resident=' . link_safe($profile_user['display']) . '">View museum wing</a> (of ' . $profile_user['museumcount'] . ' items)</li>';

  echo '<li><a href="/residentprofile_hoard.php?resident=' . link_safe($profile_user['display']) . '">View entire hoard</a></li>';

  if($totem !== false)
    echo '</ul>';

  if(count($display_items) > 0 || count($mantle_items) > 0)
    echo '<table id="treasures">';

  if(count($mantle_items) > 0)
  {
    echo '<tr>';
    foreach($mantle_items as $item)
    {
      $messages = array($item['itemname'] . '<i>');

      if(strlen($item['message']))
        $messages[] = $item['message'];
      if(strlen($item['message2']))
        $messages[] = $item['message2'];
?>
       <td align="center"><?= item_display($item, "onmouseover=\"Tip('<table class=\\'tip\\'><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), implode('<br />', $messages)) . "</i></td></tr></table>');\"") ?><br /></td>
<?php
    }
    echo '</tr>';

    if(count($display_items) > 0)
      echo '<tr><td colspan="10"><hr /></td></tr>';
  }

  if(count($display_items) > 0)
  {
    $i = 0;
    $row_style = begin_row_class();

    foreach($display_items as $item)
    {
      $messages = array($item['itemname']);

      if($item['qty'] == 1)
      {
        $messages[0] .= '<i>';
        if(strlen($item['message']))
          $messages[] = $item['message'];
        if(strlen($item['message2']))
          $messages[] = $item['message2'];
      }

      if($i % 10 == 0)
        echo '<tr>';

      if($i < 10)
        echo '<td align="center" valign="top">';
      else
        echo '<td style="border-top: 1px solid #ccc;" align="center" valign="top">';

      echo item_display($item, "onmouseover=\"Tip('<table class=\\'tip\\'><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), implode('<br />', $messages)) . "</i></td></tr></table>');\"") . '<br />' . ($item['qty'] > 1 ? '<b class="dim"><nobr>&times; ' . $item['qty'] . '</nobr></b>' : '') . '</td>';

      if(($i + 1) % 10 == 0)
      {
        echo '</tr>';
        $row_style = alt_row_class($row_style);
      }

      $i++;
      
      if($i >= 140)
        break;
    }

    if($i % 10 != 0)
      echo '</tr>';
  }

  if(count($display_items) > 0 || count($mantle_items) > 0)
    echo '</table>';
}

$command = 'SELECT * FROM psypets_profilecomments WHERE userid=' . $profile_user['idnum'] . ' ORDER BY idnum DESC LIMIT 10';
$comments = $database->FetchMultiple($command, 'residentprofile.php');

$command = 'SELECT COUNT(idnum) FROM psypets_profilecomments WHERE userid=' . $profile_user['idnum'];
$data = $database->FetchSingle($command, 'residentprofile.php');
$comment_count = $data['COUNT(idnum)'];

echo '</td>';

if($profile_user['profilecomments'] != 'none' && !($now_month == 1 && $now_day == 18 && $now_year == 2012))
{
?>
      <td valign="top" id="comments">
       <h5>Comments</h5>
<?php
  if(($profile_user['profilecomments'] == 'friends' && !is_friend($profile_user, $user)) || is_enemy($user, $profile_user) || is_enemy($profile_user, $user))
    echo '<p><i>(Only ' . $profile_user['display'] . '\'s friends may leave comments.)</i></p>';
  else if($comments[0]['authorid'] != $user['idnum'] || $comments[0]['timestamp'] < $now - (60 * 60))
  {
?>
  <div id="commentform">
  <div id="commentmessage" style="display:none;"></div>
  <form action="/leavecomment.php?resident=<?= link_safe($profile_user['display']) ?>" method="post" onsubmit="ajax_comment(); return false;">
  <p><textarea name="comment" style="width: 100%;" id="commentfield"></textarea></p>
  <p id="commentbutton"><input type="submit" value="Leave Comment" class="bigbutton" /></p>
  </form>
  </div>
<?php
  }
  else
    echo '<p><i>(You cannot leave two comments in rapid succession.  Either wait for someone else to comment, or wait an hour.)</i></p>';

  if(count($comments) == 0)
    echo '<p><i>No one has left any comments on this profile.</i></p>';
  else
  {
    if($comment_count == 1)
      echo '<p><i>There is 1 comment on this profile!';
    else
      echo '<p><i>There are ' . $comment_count . ' comments on this profile!';
    if($comment_count > 10)
      echo ' (<a href="viewcomments.php?resident=' . link_safe($profile_user['display']) . '">see them all</a>)';
    echo '</i></p><div id="newcomment"></div>';

    $first = true;
    foreach($comments as $comment)
    {
      if(!$first)
      {
        echo '<hr />';
        $first = false;
      }

      $author = get_user_byid($comment['authorid'], 'display,graphic,is_a_whale');

      if($author === false)
        $author = array('graphic' => '../shim.gif', 'display_fmt' => '<i class="dim">Departed #' . $comment['authorid'] . '</i>');
      else
        $author['display_fmt'] = '<a href="residentprofile.php?resident=' . link_safe($author['display']) . '">' . $author['display'] . '</a>';
?>
<table class="profilecomment">
 <tr>
  <td valign="top" class="centered" width="56">
   <img src="<?= user_avatar($author) ?>" width="48" height="48" alt="" /><br />
<?php
      if($profile_user['idnum'] == $user['idnum'])
        echo '   [<a href="deletecomment.php?idnum=' . $comment['idnum'] . '" onclick="return confirm(\'Really delete this comment?\');">delete</a>]';
?>
  </td>
  <td valign="top">
   <table style="width:100%;" class="nomargin">
    <tr style="border-bottom: 1px solid #000">
     <td><b><?= $author['display_fmt'] ?> says...</b></td>
     <td align="right"><a href="commentdialog.php?1=<?= $profile_user['display'] ?>&2=<?= $author['display'] ?>"><img src="/gfx/speak.gif" alt="(dialog view)" height="16" width="16" /></a></td>
    </tr>
    <tr>
     <td colspan="2">
      <p><?= format_text($comment['comment']) ?></p>
      <p class="nomargin"><i class="dim">Posted <?= local_time($comment['timestamp'], $user['timezone'], $user['daylightsavings']) ?></p>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<?php
    }
  }
  
  echo '</td>';
}

$num_pets = count($profile_pets);

echo '</tr></table>';

if($daycared_pets > 0)
  $extra = ' <a href="/daycaredpets.php?resident=' . link_safe($profile_user['display']) . '">and ' . $daycared_pets . ' in Daycare</a>';

echo '<h5 id="petlist">Pet Information (' . $num_pets . ' pet' . ($num_pets == 1 ? '' : 's') . $extra . ')</h5>';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

ob_start();

$live_pets = false;

if($num_pets > 0)
{
  $colstart = begin_row_class();
  $pet_count = 0;

  echo '<table>';

  foreach($profile_pets as $profile_pet)
  {
    if($pet_count % 3 == 0)
    {
      if($pet_count != 0)
        echo '</tr>';

      echo '<tr>';

      $colstart = alt_row_class($colstart);

      $cellclass = $colstart;
    }

    $pet_seconds = $now - $profile_pet['birthday'];
    $pet_age = '';

    if($pet_seconds > (60 * 60 * 24 * 365))
    {
      $pet_years = floor($pet_seconds / (60 * 60 * 24 * 365));
      $pet_seconds -= $pet_years * (60 * 60 * 24 * 365);
      $pet_age .= "$pet_years year" . ($pet_years > 1 ? 's' : '') . ' ';
    }

    if($pet_seconds > (60 * 60 * 24 * (365 / 12)))
    {
      $pet_months = floor($pet_seconds / (60 * 60 * 24 * (365 / 12)));
      $pet_seconds -= $pet_months * (60 * 60 * 24 * (365 / 12));

      $pet_age .= "$pet_months month" . ($pet_months > 1 ? 's' : '') . ' ';
    }

    if($pet_seconds > (60 * 60 * 24 * 7))
    {
      $pet_weeks = floor($pet_seconds / (60 * 60 * 24 * 7));
      $pet_seconds -= $pet_weeks * (60 * 60 * 24 * 7);

      $pet_age .= $pet_weeks . ' week' . ($pet_weeks > 1 ? 's' : '') . ' ';
    }

    if($pet_seconds > (60 * 60 * 24))
    {
      $pet_days = floor($pet_seconds / (60 * 60 * 24));
      $pet_seconds -= $pet_days * (60 * 60 * 24);

      $pet_age .= $pet_days . ' day' . ($pet_days > 1 ? 's' : '') . ' ';
    }

    if($profile_pet['dead'] != 'no')
      $pet_age = 'dead';
    else if($pet_age == '')
      $pet_age = 'newborn';
    else
      $pet_age .= 'old';

    if($profile_pet['toolid'] > 0)
    {
      $tool = get_inventory_byid($profile_pet['toolid']);
      $toolitem = get_item_byname($tool['itemname']);
      $toolgraphic = item_display($toolitem, "onmouseover=\"Tip('<table><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), $tool["itemname"]) . "</td></tr></table>');\"");
    }
    else
      $toolgraphic = '';
?>
       <td valign="top" class="<?= $cellclass ?>" id="pet_<?= $profile_pet['idnum'] ?>">
        <table>
         <tr>
          <td align="center" width="32"><?= $toolgraphic ?></td>
          <td valign="top"><?= pet_graphic($profile_pet) ?></td>
         </tr>
         <tr>
          <td align="center"><?= gender_graphic($profile_pet['gender'], $profile_pet["prolific"]) ?><?php
    if($profile_pet['incarnation'] > 1)
      echo '<br /><img src="/gfx/ascend.png" width="16" height="16" alt="reincarnated" style="margin-top: 4px;" /> ' . ($profile_pet['incarnation'] - 1);
?></td>
          <td align="center" valign="top">Level <?= pet_level($profile_pet) ?></td>
         </tr>
        </table>
       </td>
       <td valign="top" class="<?= $cellclass ?>">
        <b><a href="/petprofile.php?petid=<?= $profile_pet['idnum'] ?>"><?= $profile_pet['petname'] ?></a></b><br />
        is <?= $pet_age ?>.<br />
<?php
    echo pregnancy_blurb($profile_pet);

    if(strpos($profile_pet['graphic'], '/') !== false)
      echo 'has a custom pet graphic.<br />';

    if($admin['clairvoyant'] == 'yes' && $profile_user['user'] != $user['user'])
    {
      if($profile_pet['protected'] == 'yes')
        echo 'is a protected pet.<br /><br />';
?>
<!--
        Energy: <?= $profile_pet['energy'] ?> / <?= max_energy($profile_pet) ?><br />
        Food: <?= $profile_pet['food'] ?> / <?= max_food($profile_pet) ?><br />
        Safety: <?= $profile_pet['safety'] ?> / <?= max_safety($profile_pet) ?><br />
        Love: <?= $profile_pet['love'] ?> / <?= max_love($profile_pet) ?><br />
        Esteem: <?= $profile_pet['esteem'] ?> / <?= max_esteem($profile_pet) ?><br />
-->
<?php
    }
    
    echo '</td>';

    $pet_count++;

    $cellclass = alt_row_class($cellclass);
  } // for each pet
  
  echo '</tr></table>';

  $pets = ob_get_contents();
  ob_end_clean();

  if($p_love_pets == 1)
    $command = 'Pet 1 pet';
  else
    $command = 'Pet ' . $p_love_pets . ' pets';

  if($profile_user['idnum'] == $user['idnum'])
    ;
  else if($p_love_options == '')
    echo '<p><button class="bigbutton" disabled="disabled">Pet 0 pets</button> <span class="dim">' . $profile_user['display'] . '\'s pets have already been pet quite recently.</span></p>';
  else
  {
    echo '
      <div id="petpets"><form action="/lovefriendspets.php" method="post" onsubmit="ajax_love(); return false;">
       ' . $p_love_options . '
       <input type="hidden" name="resident" value="' . urlencode($profile_user['display']) . '" />
       <p><input type="submit" value="' . $command . '" class="bigbutton" /></p>
      </form></div>
    ';
  }

  echo $pets;
}
else
  echo '<p>' . $profile_user['display'] . ' has no pets.</p>';

$footer_note = '<br />Took ' . round($treasure_time, 4) . 's fetching profile items, ' . round($pet_time, 4) . 's fetching pets, ' . round($virgin_post_time, 4) . 's fetching forum data.';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
