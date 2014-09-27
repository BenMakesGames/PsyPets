<?php
$child_safe = false;
$require_petload = 'no';

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
require_once 'commons/userlib.php';
require_once 'commons/badges.php';
require_once 'commons/profiles.php';

$profile_user = get_user_bydisplay($_GET['resident']);

if($profile_user === false)
{
  header('Location: /directory.php');
  exit();
}

if($profile_user['is_npc'] == 'yes')
{
  header('Location: /npcprofile.php?npc=' . link_safe($profile_user['display']));
  exit();
}

if(($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no') && $user['admin']['manageaccounts'] !== 'yes')
{
  header('Location: /directory.php');
  exit();
}

$badges = get_badges_byuserid($profile_user['idnum']);

$display_time = microtime(true);

// get profile items from inventory
$display_items = get_display_items_as_hoard($profile_user);

$display_time = microtime(true) - $display_time;

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Treasure Hoard</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <table>
      <tr>
       <td valign="top"><img src="<?= user_avatar($profile_user) ?>" width="48" height="48" /></td>
       <td valign="top"><h5 style="margin: 2px 0;"><?= $profile_user['display'] ?>'s Treasure Hoard<?php
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
  echo ' (<a href="/admin/resident.php?user=' . $profile_user['user'] . '" title="lookup">' . $profile_user['user'] . '</a>, #' . $profile_user['idnum'] . $note;
  if($profile_user['childlockout'] == 'yes') echo ', child lock-out ENABLED';
  echo ')';
}
?></h4>
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
<?php
echo '<ul><li>' . resident_link($profile_user['display'], '\'s Profile') . '</li></ul>';

if(count($display_items) > 0)
{
  echo '<ul class="inventory items">';
  foreach($display_items as $item)
  {
    $messages = array($item['itemname']);

    $messages[0] .= '<i>';
    if(strlen($item['message']))
      $messages[] = $item['message'];
    if(strlen($item['message2']))
      $messages[] = $item['message2'];

    echo '<li class="centered">' . item_display($item, "onmouseover=\"Tip('<table class=\\'tip\\'><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), implode('<br />', $messages)) . "</i></td></tr></table>');\"") . '<br />' . $item['itemname'] . '</li>';
  }
  echo '</ul><div class="endinventory"></div>';
}
else
  echo '<p>This Resident has no profile items.</p>';

$footer_note = '<br />Took ' . round($display_time, 4) . 's fetching profile items.';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
