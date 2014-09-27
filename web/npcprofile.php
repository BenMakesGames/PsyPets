<?php
$child_safe = false;
$require_petload = 'no';
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/badges.php';
require_once 'commons/profiles.php';

$profile_user = get_user_bydisplay($_GET['npc']);

$npc_redirect = array('graveyard' => 'graveyard.php', 'ark' => 'ark.php');

if(array_key_exists($profile_user['user'], $npc_redirect))
{
  header('Location: /' . $npc_redirect[$profile_user['user']]);
  exit();
}

if($profile_user['is_npc'] != 'yes')
{
  header('Location: /directory.php');
  exit();
}

$searchable_profile = get_user_profile($profile_user['idnum']);

$profile_text = get_user_profile_text($profile_user['idnum']);

$friend_list = take_apart(',', $profile_user['friends']);

if(strlen($profile_user['profile_wall']) > 0)
{
  $CONTENT_STYLE = 'background: url(\'gfx/' . $profile_user['profile_wall'] . '\')';

  if($profile_user['profile_wall_repeat'] == 'no')
    $CONTENT_STYLE .= ' no-repeat';

  $CONTENT_STYLE .= ';';
}

if($profile_user['user'] == 'klittrell')
  $command = 'SELECT * ' .
             'FROM monster_pets ' .
             'WHERE `user`=\'psypets\' AND last_check<' . $now . ' ORDER BY last_check ASC LIMIT 50';
else
  $command = 'SELECT * ' .
             'FROM monster_pets ' .
             'WHERE `user`=' . quote_smart($profile_user['user']) . ' ORDER BY orderid,idnum ASC';

$profile_pets = $database->FetchMultiple($command, 'userprofile.php');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Profile</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($profile_user['cobwebs'] == 'yes')
  echo '<img src="/gfx/cobweb.png" width="256" height="192" style="position:absolute; right:0px;" />';
?>
     <table border="0" style="padding-top:8px; margin-bottom: 0;">
      <tr>
       <td><img src="<?= user_avatar($profile_user) ?>" width="48" height="48" align="left" /></td>
       <td valign="top"><h5 style="margin: 2px 0;"><?= $profile_user['display'] ?>'s Profile<?php
if($admin['clairvoyant'] == 'yes')
  echo ' (' . $profile_user['user'] . ', #' . $profile_user['idnum'] . ')';
?></h4>
       </td>
      </tr>
     </table>
     <ul>
      <li><a href="http://<?= $SETTINGS['wiki_domain'] ?>/<?= $profile_user['display'] ?>">View resident's PsyHelp entry</a></li>
     </ul>
     <h5>Resident Profile</h5>
     <table>
<?php
if($profile_text != false && $profile_text['text'] != '')
{
  $profile_xhtml = format_text($profile_text['text']);
?>
      <tr>
       <td valign="top"><img src="gfx/speak.gif" width="16" height="16" alt="" /></td>
       <td>
        <div class="userformatting">
         <?= $profile_xhtml ?>
        </div>
       </td>
      </tr>
<?php
}

if(strlen($searchable_profile['url']) > 0)
{
?>
      <tr>
       <td><img src="gfx/worldicon.png" width="16" height="16" alt="web site" /></td>
       <td><a href="<?= '//' . $searchable_profile['url'] ?>"><?= $searchable_profile['url'] ?></a></td>
      </tr>
<?php
}
?>
     </table>
     <h5>Resident Information</h5>
     <table>
<?php
if(count($friend_list) > 0)
{
?>
      <tr>
       <td><img src="gfx/friends.gif" width="16" height="16" alt="" /></td>
       <td>Has Friends</td>
      </tr>
      <tr>
       <td></td>
       <td>
        <ul class="plainlist">
<?php
  foreach($friend_list as $idnum)
  {
    $friend = get_user_byid($idnum);
    if($friend === false)
    {
?>
         <li><i style="color:#888888">Departed #<?= $idnum ?></i></li>
<?php
    }
    else
    {
      $name = $friend['display'];
?>
         <li><a href="residentprofile.php?resident=<?= link_safe($name) ?>"><?= $name ?></a></li>
<?php
    }
  }
?>
        </ul>
       </td>
      </tr>
<?php
}
?>
      <tr>
       <td valign="top"><img src="gfx/timer.gif" width="16" height="16" alt="" /></td>
       <td>
<!--        Signed up on <?= local_time($profile_user['signupdate'], $user['timezone'], $user['daylightsavings']) ?><br /> -->
        Last active on <?= local_time($now, $user['timezone'], $user['daylightsavings']) ?><br />
       </td>
      </tr>
     </table>
     <h5>Pet Information</h5>
<?php
$live_pets = false;

if(count($profile_pets) > 0)
{
  $pet_count = 1;
  $cellclass = begin_row_class();
?>
     <table>
      <tr>
<?php
  foreach($profile_pets as $profile_pet)
  {
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

      $pet_age .= "$pet_months month" . ($pet_months > 1 ? "s" : "") . " ";
    }

    if($pet_seconds > (60 * 60 * 24 * 7))
    {
      $pet_weeks = floor($pet_seconds / (60 * 60 * 24 * 7));
      $pet_seconds -= $pet_weeks * (60 * 60 * 24 * 7);

      $pet_age .= "$pet_weeks week" . ($pet_weeks > 1 ? "s" : "") . " ";
    }

    if($pet_seconds > (60 * 60 * 24))
    {
      $pet_days = floor($pet_seconds / (60 * 60 * 24));
      $pet_seconds -= $pet_days * (60 * 60 * 24);

      $pet_age .= "$pet_days day" . ($pet_days > 1 ? 's' : '') . ' ';
    }

    if($pet_age == '')
      $pet_age = 'newborn';

    if($profile_pet['toolid'] > 0)
    {
      $tool = get_inventory_byid($profile_pet['toolid']);
      $toolitem = get_item_byname($tool['itemname']);
      $toolgraphic = item_display($toolitem, "onmouseover=\"Tip('<table border=0 cellspacing=0 cellpadding=2><tr><td>" . str_replace(array("'", "\""), array("\'", "\\"), $tool["itemname"]) . "</td></tr></table>');\"");
    }
    else
      $toolgraphic = "";

    $cellclass = alt_row_class($cellclass);

    if(($pet_count - 1) % 3 == 0)
      echo '</tr><tr>';

    $pet_count++;
?>
       <td valign="top" class="<?= $cellclass ?>">
        <table>
         <tr>
          <td align="center" width="32"><?= $toolgraphic ?></td>
          <td valign="top"><a href="/petprofile.php?petid=<?= $profile_pet['idnum'] ?>"><?php
    if($profile_pet['dead'] == 'no')
    {
      $live_pets = true;
      if($profile_pet['changed'] == 'yes')
        echo '<img src="gfx/pets/were/form_' . ($profile_pet['idnum'] % 2 + 1) . '.png" alt="Werecreature!" border="0" />';
      else
        echo '<img src="gfx/pets/' . $profile_pet['graphic'] . '" width="48" height="48" alt="" border="0" />';
    }
    else
    {
      $i = $profile_pet['idnum'] % 4 + 1;
      if($i < 10) $i = "0$i";
      echo '   <img src="gfx/pets/dead/tombstone_' . $i . '.png" width="48" height="48" alt="Dead" border="0" />';
    }
?></a></td>
         </tr>
         <tr>
          <td align="center"><?= gender_graphic($profile_pet['gender'], $profile_pet["prolific"]) ?></td>
          <td align="center">Level <?= pet_level($profile_pet) ?></td>
         </tr>
        </table>
       </td>
       <td valign="top" class="<?= $cellclass ?>">
        <a name="<?= $profile_pet['idnum'] ?>"></a>Name: <?= $profile_pet['petname'] ?><br />
        Gender: <?= $profile_pet['gender'] ?><?= ($profile_pet['prolific'] == 'no' ? ' (' . ($profile_pet['gender'] == 'male' ? 'neutered' : 'spayed') . ')' : '') ?><br />
        Age: <?= $pet_age ?><br />
        <br />
<?php
    if(strpos($profile_pet['graphic'], '/') !== false)
      echo "      Has a custom pet graphic.<br /><br />\n";

    if($admin["clairvoyant"] == "yes" && $profile_user["user"] != $user["user"])
    {
      if($profile_pet["protected"] == "yes")
        echo "      Is a protected pet.<br /><br />\n";
?>
        Food: <?= $profile_pet["food"] ?> / <?= max_food($profile_pet) ?><br />
        Safety: <?= $profile_pet["safety"] ?> / <?= max_safety($profile_pet) ?><br />
        Love: <?= $profile_pet["love"] ?> / <?= max_love($profile_pet) ?><br />
        Esteem: <?= $profile_pet["esteem"] ?> / <?= max_esteem($profile_pet) ?><br />
        <br />
<?php
    }
?>
       </td>
<?php
  } // for each pet
?>
      </tr>
     </table>
<?php
}
else
  echo '<p>' . $profile_user['display'] . ' has no pets.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
