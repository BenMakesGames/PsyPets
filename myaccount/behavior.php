<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';

$now = time();

if($_POST['action'] == 'apply')
{
  if($_POST['enclink'] == 'on' || $_POST['enclink'] == 'yes')
    $user['inventorylink'] = 'yes';
  else
    $user['inventorylink'] = 'no';

  if($_POST['inventorycontext'] == 'on' || $_POST['inventorycontext'] == 'yes')
    $user['inventory_context_menu'] = 'yes';
  else
    $user['inventory_context_menu'] = 'no';

  if($_POST['encpopup'] == 'on' || $_POST['encpopup'] == 'yes')
    $user['encyclopedia_popup'] = 'yes';
  else
    $user['encyclopedia_popup'] = 'no';

  if($_POST['showtips'] == 'on' || $_POST['showtips'] == 'yes')
    $user['tips_enabled'] = 'yes';
  else
    $user['tips_enabled'] = 'no';

  if($_POST['multi_login'] == 'on' || $_POST['multi_login'] == 'yes')
    $user['multi_login'] = 'yes';
  else
    $user['multi_login'] = 'no';

  if($_POST['incomingto'] == 'storage')
    $user['incomingto'] = 'storage';
  else if($_POST['incomingto'] == 'incoming')
    $user['incomingto'] = 'storage/incoming';

  if($_POST['psymailpersonal'] == 'on' || $_POST['psymailpersonal'] == 'yes')
    $user['email_personal'] = 'yes';
  else
    $user['email_personal'] = 'no';

  $login_duration = (int)$_POST['rememberme'];
  $login_scale = $_POST['rememberme_scale'];

  if($login_scale == 'minutes')
    $login_duration = $login_duration * 60;
  else if($login_scale == 'hours')
    $login_duration = $login_duration * 60 * 60;
  else if($login_scale == 'days')
    $login_duration = $login_duration * 60 * 60 * 24;
    
  if($login_duration < 30 * 60)
    $login_duration = 30 * 60;
   else if($login_duration > 30 * 60 * 60 * 24)
    $login_duration = 30 * 60 * 60 * 24;

  $hour_max = (int)$_POST['hourmax'];
  if($hour_max < 0)
    $hour_max = 0;
  else if($hour_max > 48)
    $hour_max = 48;

  $user['auto_spend_hours'] = $hour_max;

  if($_POST['hoveringmenu'] == 'on' || $_POST['hoveringmenu'] == 'yes')
    $user['menu_floating'] = 'yes';
  else
    $user['menu_floating'] = 'no';

  $popup_menu = $_POST['popup_menu'];
  if($popup_menu == 'click' || $popup_menu == 'mouseover')
    $user['menu_popup_setting'] = $popup_menu;

  if($_POST['skipconfirm'] == 'on' || $_POST['skipconfirm'] == 'yes')
    $user['confirm_skip'] = 'yes';
  else
    $user['confirm_skip'] = 'no';

  setcookie($SETTINGS['cookie_name'], $user['user'] . ';' . $user['pass'], $now + $login_duration, $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);

  $command = '
    UPDATE monster_users SET
      inventorylink=' . quote_smart($user['inventorylink']) . ',
      encyclopedia_popup=' . quote_smart($user['encyclopedia_popup']) . ',
      incomingto=' . quote_smart($user['incomingto']) . ',
      tips_enabled=' . quote_smart($user['tips_enabled']) . ',
      multi_login=' . quote_smart($user['multi_login']) . ',
      login_persist=' . (int)$login_duration . ',
      email_personal=' . quote_smart($user['email_personal']) . ',
      auto_spend_hours=' . $user['auto_spend_hours'] . ',
      inventory_context_menu=' . quote_smart($user['inventory_context_menu']) . ',
      menu_floating=' . quote_smart($user['menu_floating']) . ',
      menu_popup_setting=' . quote_smart($user['menu_popup_setting']) . ',
      confirm_skip=' . quote_smart($user['confirm_skip']) . '
    WHERE idnum=' . (int)$user['idnum'] . ' LIMIT 1
  ';

  $user['login_persist'] = (int)$login_duration;

  fetch_none($command, '/myaccount/display.php');

  $general_message = 'Behavior settings applied.';
}

$rememberme = $user['login_persist'];
if($rememberme % (24 * 60 * 60) == 0)
{
  $rememberme /= (24 * 60 * 60);
  $rememberme_days = ' selected';
}
else if($rememberme % (60 * 60) == 0)
{
  $rememberme /= (60 * 60);
  $rememberme_hours = ' selected';
}
else
{
  $rememberme /= 60;
  $rememberme_minutes = ' selected'; 
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Behavior Settings</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Behavior Settings</h4>
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
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li class="activetab"><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($general_message)
  echo '     <p style="color:blue;">' . $general_message . '</p>';
?>
  <table>

   <form method="post">
   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>My House</h4></td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar">Automatic Hour Use:</td>
    <td><input type="number" name="hourmax" min="0" max="48" size="2" value="<?= $user['auto_spend_hours'] ?>" autocomplete="off" /></td>
    <td>When you visit your house, automatically run the hours if there are this many, or fewer.  (If set to "0", house hours will never be run automatically.)</td>
   </tr>
   <tr>
    <td class="leftbar"><nobr>Confirm Skipping Hours:</nobr></td>
    <td><input type="checkbox" name="skipconfirm"<?= $user['confirm_skip'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>If checked, you will receive a confirmation dialog when you skip hours at home.</td>
   </tr>

   <tr>
    <td colspan="3">&nbsp;</td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>Item Management</h4></td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar"><nobr>Encyclopedia Link:</nobr></td>
    <td><input type="checkbox" name="enclink"<?= $user['inventorylink'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>Link inventory images to the encyclopedia (if this is not checked, other places, such as the Flea Market, will still link in this way)</td>
   </tr>

   <tr>
    <td class="leftbar"><nobr>In-page Encyclopedia Popups:</nobr></td>
    <td><input type="checkbox" name="encpopup"<?= $user['encyclopedia_popup'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>If disabled, clicking on an item will take you to a separate page with the item's information, instead of displaying the information pop-up.</td>
   </tr>

   <tr>
    <td class="leftbar"><nobr>Inventory Context Menu:</nobr></td>
    <td><input type="checkbox" name="inventorycontext"<?= $user['inventory_context_menu'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>Allows you to right-click on your inventory at home to show a menu with Feed, Move, Prepare, and Throw Out options.</td>
   </tr>

   <tr>
    <td class="leftbar">Put&nbsp;Incoming&nbsp;Items&nbsp;In:</td>
    <td><select name="incomingto">
     <option value="incoming"<?= $user['incomingto'] == 'storage/incoming' ? ' selected' : '' ?>">Incoming</option>
     <option value="storage"<?= $user['incomingto'] == 'storage' ? ' selected' : '' ?>">Storage</option>
    </select></td>
    <td>Choose where items from Recycling, Flea Market, Smithery, and other places go.</td>
   </tr>

   <tr>
    <td colspan="3">&nbsp;</td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>Login and Session</h4></td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar">Show&nbsp;Tips:</td>
    <td><input type="checkbox" name="showtips"<?= $user['tips_enabled'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>If checked, you will see a random tip every time you log in.</td>
   </tr>
   <tr>
    <td class="leftbar"><nobr>Keep Me Logged In:</nobr></td>
    <td><nobr><input name="rememberme" value="<?= $rememberme ?>" maxlength="2" size="2" autocomplete="off" /> <select name="rememberme_scale">
     <option value="minutes"<?= $rememberme_minutes ?>>minutes</option>
     <option value="hours"<?= $rememberme_hours ?>>hours</option>
     <option value="days"<?= $rememberme_days ?>>days</option>
    </select></nobr></td>
    <td>Keeps you logged in for the specified amount of time of inactivity.  Must be between 30 minutes and 30 days.</td>
   </tr>
   <tr>
    <td valign="top" class="leftbar">Allow&nbsp;Multiple&nbsp;Logins:</td>
    <td valign="top"><input type="checkbox" name="multi_login"<?= $user['multi_login'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>Allows multiple people to be simultaneously logged in to this account.  Be aware that this can be potentially confusing, for example if one person tried to prepare an item, and another tried to feed the same item to a pet.  One of the two would get an error message about the item no longer existing, since whoever clicks first will consume the item.</td>
   </tr>

   <tr>
    <td colspan="3">&nbsp;</td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>Menu</h4></td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar">Floating Menu:</td>
    <td><input type="checkbox" name="hoveringmenu"<?= $user['menu_floating'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>When enabled, the menu follows you as you scroll down the page.</td>
   </tr>
   <tr>
    <td class="leftbar">Open Menus When:</td>
    <td>
     <select name="popup_menu">
      <option value="mouseover"<?= $user['menu_popup_setting'] == 'mouseover' ? ' selected' : '' ?>>I mouse-over them</option>
      <option value="click"<?= $user['menu_popup_setting'] == 'click' ? ' selected' : '' ?>>I click on them</option>
<!--      <option value="mouseenter"<?= $user['menu_popup_setting'] == 'mouseenter' ? ' selected' : '' ?>>I 'mouse-enter' them?</option>-->
     </select>
    </td>
   </tr>

   <tr>
    <td colspan="3">&nbsp;</td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>E-Mail Notifications</h4></td>
    <td>&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar">Personal PsyMail:</td>
    <td><input type="checkbox" name="psymailpersonal"<?= $user['email_personal'] == 'yes' ? ' checked' : '' ?> /></td>
    <td>Check to receive an e-mail whenever someone PsyMails you.</td>
   </tr>

   <tr>
    <td colspan="3">&nbsp;</td>
   </tr>

   <tr>
    <td>&nbsp;</td>
    <td colspan="2"><input type="hidden" name="action" value="apply" /><input type="submit" value="Apply Settings" class="bigbutton" /></td>
   </tr>

   </form>
  </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
