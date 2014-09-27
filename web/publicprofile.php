<?php
$require_petload = 'no';
$require_login = 'no';

if($_GET['resident'] == 'broadcasting')
{
  header('Location: ./broadcast.php');
  exit();
}
if($_GET['resident'] == 'psypets')
{
  header('Location: ./cityhall.php');
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

$profile_user = get_user_bydisplay($_GET['resident']);

if($profile_user === false)
{
  header('Location: ./directory.php');
  exit();
}

if($profile_user['is_npc'] == 'yes')
{
  header('Location: ./npcprofile.php?npc=' . link_safe($profile_user['display']));
  exit();
}

if(($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no') && $user['admin']['manageaccounts'] !== 'yes')
{
  header('Location: ./directory.php');
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

$pet_time = microtime(true) - $pet_time;

$command = 'SELECT * ' .
           'FROM monster_admins ' .
           'WHERE `user`=' . quote_smart($profile_user['user']) . ' LIMIT 1';
$profile_admin = $database->FetchSingle($command, 'userprofile.php');

$update_list = false;

$searchable_profile = get_user_profile($profile_user['idnum']);

$limit = ' LIMIT 141';

$mantle_items = array();

$display_time = microtime(true);

// get mantle items
$fireplace = get_fireplace_byuser($profile_user['idnum']);
if($fireplace !== false)
{
  $command = 'SELECT a.*,b.graphic,b.graphictype,a.message,a.message2 FROM monster_inventory AS a,monster_items AS b WHERE a.itemname=b.itemname AND a.user=' . quote_smart($profile_user['user']) . ' AND a.location=\'fireplace\'';
  $mantle_items = $database->FetchMultipleBy($command, 'idnum', 'userprofile.php');

  if(strlen($fireplace['mantle']) > 0)
    sort_items_by_mantle($mantle_items, explode(',', $fireplace['mantle']));
}

// get profile items from inventory
$display_items = get_display_items($profile_user, true);

$display_time = microtime(true) - $display_time;

if($profile_user['meteor'] == 'yes')
{
  $CONTENT_STYLE = 'background: url(\'gfx/walls/meteor.png\') no-repeat';
}
else if(strlen($profile_user['profile_wall']) > 0)
{
  $CONTENT_STYLE = 'background: url(\'gfx/' . $profile_user['profile_wall'] . '\')';

  if($profile_user['profile_wall_repeat'] == 'no')
    $CONTENT_STYLE .= ' no-repeat';
  else if($profile_user['profile_wall_repeat'] == 'horizontal')
    $CONTENT_STYLE .= ' repeat-x';
  else if($profile_user['profile_wall_repeat'] == 'vertical')
    $CONTENT_STYLE .= ' repeat-y';

  $CONTENT_STYLE .= ';';
}

$CONTENT_CLASS = 'profilepadded';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Profile</title>
<?php
include 'commons/head.php';
?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
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
    echo '<img src="gfx/badges/' . $badge . '.png" height="20" width="20" title="' . $BADGE_DESC[$badge] . '" /> ';
}
?></div>
       </td>
      </tr>
     </table>
     <ul>
      <li><a href="http://<?= $SETTINGS['wiki_domain'] ?>/User:<?= $profile_user['display'] ?>">View resident's PsyHelp entry</a></li>
     </ul>
     <table>
      <tr><td valign="top" style="padding-right: 1em;">
     <h5>Resident Information</h5>
     <table>
<?php
$friend_list = take_apart(',', $profile_user['friends']);

if(count($friend_list) > 0 && $profile_user['publicfriends'] == 'yes')
{
  $real_friend_list = array();

  foreach($friend_list as $idnum)
  {
    $friend = get_user_byid($idnum, 'display');
    if($friend !== false)
      $real_friend_list[strtolower($friend['display'])] = $friend;
  }

  ksort($real_friend_list);
?>
      <tr>
       <td><img src="gfx/friends.gif" width="16" height="16" alt="" /></td>
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
  if(count($real_friend_list) >= 9)
  {
    $columns = array_chunk($real_friend_list, ceil(count($real_friend_list) / 3), true);

    foreach($columns[0] as $friend)
      echo '<li><a href="publicprofile.php?resident=' . link_safe($friend['display']) . '">' . $friend['display'] . '</a></li>';

    echo '</ul></td><td style="padding-left:2em;" valign="top"><ul class="plainlist">';

    foreach($columns[1] as $friend)
      echo '<li><a href="publicprofile.php?resident=' . link_safe($friend['display']) . '">' . $friend['display'] . '</a></li>';

    echo '</ul></td><td style="padding-left:2em;" valign="top"><ul class="plainlist">';

    foreach($columns[2] as $friend)
      echo '<li><a href="publicprofile.php?resident=' . link_safe($friend['display']) . '">' . $friend['display'] . '</a></li>';
  }
  else
    foreach($real_friend_list as $friend)
      echo '<li><a href="publicprofile.php?resident=' . link_safe($friend['display']) . '">' . $friend['display'] . '</a></li>';
?>
        </ul>
          </td>
         </tr>
        </table>
       </td>
      </tr>
<?php
}

if(strlen($profile_user['groups'] > 0))
{
  require_once 'commons/grouplib.php';
?>
      <tr>
       <td><img src="gfx/friends.gif" width="16" height="16" alt="" /></td>
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
       <td valign="top"><img src="gfx/timer.gif" width="16" height="16" alt="" /></td>
       <td>Signed up <?= Duration($now - $profile_user['signupdate'], 2) ?> ago (on <?= local_date($profile_user['signupdate'], $user['timezone'], $user['daylightsavings']) ?>)</td>
      </tr>
<?php
if($profile_admin["admintag"] == "yes")
{
?>
      <tr>
       <td valign="top"><a href="admincontact.php"><img src="gfx/admintag.gif" width="16" height="16" border=0 alt="" /></a></td>
       <td><?= $SETTINGS['site_name'] ?> administrator</td>
      </tr>
<?php
}

echo '</table>';

$totem = get_totem_byuserid($profile_user['idnum']);

if(count($display_items) > 0 || count($mantle_items) > 0)
{
  echo '<h5 id="profileitems">Awards, Medals, Trophies and Treasures</h5>';

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

echo '</td>';

echo '</tr></table>';

$num_pets = count($profile_pets);

if($daycared_pets > 0)
  $extra = ' <a href="daycaredpets.php?resident=' . link_safe($profile_user['display']) . '">and ' . $daycared_pets . ' in Daycare</a>';

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
      $toolgraphic = item_display($toolitem, "onmouseover=\"Tip('<table border=0 cellspacing=0 cellpadding=2><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), $tool["itemname"]) . "</td></tr></table>');\"");
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
      echo '<br /><img src="gfx/ascend.png" width="16" height="16" alt="reincarnated" style="margin-top: 4px;" /> ' . ($profile_pet['incarnation'] - 1);
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

    echo '</td>';

    $pet_count++;

    $cellclass = alt_row_class($cellclass);
  } // for each pet
  
  echo '</tr></table>';

  $pets = ob_get_contents();
  ob_end_clean();

  echo $pets;
}
else
  echo '<p>' . $profile_user['display'] . ' has no pets.</p>';

$footer_note = '<br />Took ' . round($display_time, 4) . 's fetching profile items, ' . round($pet_time, 4) . 's fetching pets, ' . round($virgin_post_time, 4) . 's fetching forum data.';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
