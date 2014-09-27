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

$pet_time = microtime(true);

$command = 'SELECT * ' .
           'FROM monster_pets ' .
           'WHERE `user`=' . quote_smart($profile_user['user']) . ' AND location=\'shelter\' ORDER BY orderid,idnum ASC';
$profile_pets = $database->FetchMultiple($command, 'fetching pets at home');

$num_pets = count($profile_pets);

$command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=' . quote_smart($profile_user['user']) . ' AND location=\'home\'';
$data = $database->FetchSingle($command, 'fetching daycared pets count');

$pets_at_home = (int)$data['c'];

$pet_time = microtime(true) - $pet_time;

$command = 'SELECT * ' .
           'FROM monster_admins ' .
           'WHERE `user`=' . quote_smart($profile_user['user']) . ' LIMIT 1';
$profile_admin = $database->FetchSingle($command, 'userprofile.php');

$update_list = false;

$searchable_profile = get_user_profile($profile_user['idnum']);

if($user['idnum'] > 0)
{
  if($_POST['action'] == 'addbuddy')
  {
    $messages[] = add_friend($user, $profile_user);
    remove_enemy($user, $profile_user, '');
    remove_enemy($user, $profile_user, '_chat');
    remove_enemy($user, $profile_user, '_psymail');
    remove_enemy($user, $profile_user, '_tv');
  }
  else if($_POST['action'] == 'rembuddy')
  {
    $messages[] = remove_friend($user, $profile_user);
  }
  else if($_POST['action'] == 'addignore')
  {
    add_enemy($user, $profile_user, '');
    add_enemy($user, $profile_user, '_chat');
    add_enemy($user, $profile_user, '_psymail');
    add_enemy($user, $profile_user, '_tv');
    $messages[] = remove_friend($user, $profile_user);
  }
  else if($_POST['action'] == 'remignore')
  {
    remove_enemy($user, $profile_user, '');
    remove_enemy($user, $profile_user, '_chat');
    remove_enemy($user, $profile_user, '_psymail');
    remove_enemy($user, $profile_user, '_tv');
  }
}

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

if($user['idnum'] > 0)
{
  if($badges['worstideaever'] == 'no' && $user['idnum'] != $profile_user['idnum'])
  {
    // if the browsing account is older than the profile's account by one year, or the browsing account was created
    // before July 4th 2004 (3 months after PsyPets' creation), then the browsing account may give the profile's
    // account the 'worstideaever' badge.
    if($user['signupdate'] <= $profile_user['signupdate'] - 365 * 24 * 60 * 60 || $user['signupdate'] <= 1088930100)
      $give_worst_idea_ever_badge = 'yes';
    else
      $give_worst_idea_ever_badge = 'no';
  }
  else
    $give_worst_idea_ever_badge = false;
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Profile</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($profile_user['cornergraphic'] != '')
  echo '<div id="cobweb"><img src="//' . $SETTINGS['site_domain'] . '/gfx/' . $profile_user['cornergraphic'] . '" width="256" height="192" /></div>';
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
  echo '<li><a href="/admin/tracktrades.php?resident=' . link_safe($profile_user['display']) . '">View trade history</a></li>' .
       '<li><a href="/loginhistory.php?as=' . $profile_user['idnum'] . '">View login history</a></li>' .
       '<li><a href="/admin/residentplazause.php?userid=' . $profile_user['idnum'] . '">View Plaza post counts</a></li>';
}
?>
      <li><a href="http://<?= $SETTINGS['wiki_domain'] ?>/User:<?= $profile_user['display'] ?>">View resident's PsyHelp entry</a></li>
     </ul>
<?php
if($user['idnum'] > 0 && $profile_user['idnum'] != $user['idnum'])
{
  include 'commons/residentprofile_actions.php';
}

if($pets_at_home > 0)
{
  if($user['idnum'] > 0)
    $extra = ' <a href="residentprofile.php?resident=' . link_safe($profile_user['display']) . '">and ' . $pets_at_home . ' at home</a>';
  else
    $extra = ' <a href="publicprofile.php?resident=' . link_safe($profile_user['display']) . '">and ' . $pets_at_home . ' at home</a>';
}

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
?>
     <table>
<?php
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

    if($pet_age == '')
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
          <td valign="top"><a href="/petprofile.php?petid=<?= $profile_pet['idnum'] ?>"><?php
    if($profile_pet['dead'] == 'no')
    {
      $live_pets = true;
      echo pet_graphic($profile_pet);
    }
    else
    {
      $i = $profile_pet['idnum'] % 4 + 1;
      if($i < 10) $i = "0$i";
      echo '   <img src="/gfx/pets/dead/tombstone_' . $i . '.png" width="48" height="48" alt="Dead" border="0" />';
    }
?></a></td>
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
        <br />
-->
<?php
    }
?>
       </td>
<?php
    $pet_count++;

    $cellclass = alt_row_class($cellclass);
  } // for each pet
?>
      </tr>
     </table>
<?php
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
