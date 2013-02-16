<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/timezones.php';

if($_POST['action'] == 'apply')
{
  if(array_key_exists($_POST['theme_layout'], $SITE_LAYOUTS))
    $user['style_layout'] = $_POST['theme_layout'];

  if(array_key_exists($_POST['theme_colors'], $SITE_COLORS))
    $user['style_color'] = $_POST['theme_colors'];

  if(array_key_exists($_POST['timezone'], $timezones))
    $user['timezone'] = $_POST['timezone'];

  if($_POST['dst'] == 'on' || $_POST['dst'] == 'yes')
    $user['daylightsavings'] = 'yes';
  else
    $user['daylightsavings'] = 'no';

  if($_POST['backlight'] == 'on' || $_POST['backlight'] == 'yes')
    $user['backlightnew'] = 'yes';
  else
    $user['backlightnew'] = 'no';

  if((int)$_POST['lightseconds'] == (float)$_POST['lightseconds'])
  {
    if($_POST['lightseconds'] >= 30 && $_POST['lightseconds'] <= 300)
      $user['backlighttime'] = $_POST['lightseconds'];
  }

  if($_POST['iconhover'] == 'on' || $_POST['iconhover'] == 'yes')
    $user['iconhoverbox'] = 'yes';
  else
    $user['iconhoverbox'] = 'no';

  if($_POST['actionall'] == 'on' || $_POST['actionall'] == 'yes')
    $user['showmimic'] = 'yes';
  else
    $user['showmimic'] = 'no';

  if($_POST['worndisplay'] == 'color'
    || $_POST['worndisplay'] == 'text'
    || $_POST['worndisplay'] == 'none'
  )
  {
    if($house['worn_indicator'] != $_POST['worndisplay'])
    {
      $command = 'UPDATE monster_houses SET worn_indicator=\'' . $_POST['worndisplay'] . '\' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
      fetch_none($command, 'updating house worn indicator');

      $house['worn_indicator'] = $_POST['worndisplay'];
    }
  }

  $command = 'UPDATE monster_users SET ' .
             'style_layout=' . quote_smart($user['style_layout']) . ', ' .
             'style_color=' . quote_smart($user['style_color']) . ', ' .
             'timezone=' . quote_smart($user['timezone']) . ', ' .
             'daylightsavings=' . quote_smart($user['daylightsavings']) . ', ' .
             'backlightnew=' . quote_smart($user['backlightnew']) . ', ' .
             'backlighttime=' . quote_smart($user['backlighttime']) . ', ' .
             'iconhoverbox=' . quote_smart($user['iconhoverbox']) . ', ' .
             'showmimic=' . quote_smart($user['showmimic']) . ' ' .
             'WHERE idnum=' . (int)$user['idnum'] . ' LIMIT 1';

  fetch_none($command, '/myaccount/display.php');

  $general_message = 'Display settings applied.';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Display Settings</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Display Settings</h4>
     <ul class="tabbed">
<?php
if($user['childlockout'] == 'no')
{
?>
      <li><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
<?php
}
?>
      <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li class="activetab"><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/display.php">Content&nbsp;Control</a></li>
     </ul>
<?php
 if($general_message)
   echo "     <p style=\"color:blue;\">" . $general_message . "</p>\n";
?>
  <table>
   <form method="post">
   <tr class="titlerow">
    <td colspan=2 align="center"><h4>Site Display</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Layout:</td>
    <td>
     <select name="theme_layout">
<?php
foreach($SITE_LAYOUTS as $value=>$name)
  echo '      <option value="' . $value  . '"' . ($user['style_layout'] == $value ? ' selected' : '') . '>' . $name . '</option>' . "\n";
?>
     </select>
    </td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Colors:</td>
    <td>
     <select name="theme_colors">
<?php
foreach($SITE_COLORS as $value=>$name)
  echo '      <option value="' . $value  . '"' . ($user['style_color'] == $value ? ' selected' : '') . '>' . $name . '</option>' . "\n";
?>
     </select>
    </td>
    <td></td>
   </tr>
   <tr>
    <td colspan="3"></td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" align="center"><h4>Time Zone Settings</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Your&nbsp;Time&nbsp;Zone:</td>
    <td colspan="2">
     <select name="timezone">
<?php
 foreach($timezones as $value=>$name)
 {
?>
      <option value="<?= $value ?>"<?= $user["timezone"] == $value ? " selected" : "" ?>><?= $name ?></option>
<?php
 }
?>
     </select>
    </td>
   </tr>
   <tr>
    <td class="leftbar">Daylight&nbsp;Savings:</td>
    <td><input type="checkbox" name="dst"<?= $user["daylightsavings"] == "yes" ? " checked" : "" ?> /></td>
    <td>Check this box if you are currently saving daylight (adds an hour).</td>
   </tr>
   <tr>
    <td colspan="3"></td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" align="center"><h4>Pet Display</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Action&nbsp;Arrows:</td>
    <td><input type="checkbox" name="actionall"<?= $user['showmimic'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>Shows an arrow next to each pet's action drop down, which can be clicked to make all pets do the same thing.  (Only shows if you have more than one pet.)</td>
   </tr>
   <tr>
    <td class="leftbar">"Worn" Equipment:</td>
    <td><select name="worndisplay">
     <option value="color"<?= $house['worn_indicator'] == 'color' ? ' selected' : '' ?>>Show a red-orange background color</option>
     <option value="text"<?= $house['worn_indicator'] == 'text' ? ' selected' : '' ?>>Show the text "worn" beneath the item</option>
     <option value="none"<?= $house['worn_indicator'] == 'none' ? ' selected' : '' ?>>Make no indication</option>
    </select></td>
    <td></td>
   </tr>
   <tr>
    <td colspan="3"></td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" align="center"><h4>Inventory Display</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Backlight&nbsp;New&nbsp;Items:</td>
    <td>
     <input type="checkbox" name="backlight"<?= $user['backlightnew'] == 'yes' ? ' checked' : '' ?> />
     <input type="number" name="lightseconds" value="<?= $user['backlighttime'] ?>" size="3" style="width: 48px;" autocomplete="off" min="30" max="300" /> seconds
    </td>
    <td>Tints the row (in details view) or cell (in icon view) of an item which has been recently moved.  Number of seconds must be between 30 and 300 (5 minutes).</td>
   </tr>
   <tr>
    <td class="leftbar">Hover&nbsp;Information&nbsp;Box:</td>
    <td><input type="checkbox" name="iconhover"<?= $user["iconhoverbox"] == "yes" ? " checked" : "" ?> /></td>
    <td>Applies to Icon display only.  Shows additional information about an item when you hold the mouse over it.  Disabling this may make pages load faster.</td>
   </tr>
   <tr>
    <td colspan="3"></td>
   </tr>

   <tr>
    <td></td>
    <td colspan="2"><input type="hidden" name="action" value="apply" /><input type="submit" value="Apply Settings" class="bigbutton" /></td>
   </tr>

   </form>
  </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
