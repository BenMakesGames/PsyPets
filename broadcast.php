<?php
$whereat = 'broadcasting';
$wiki = 'Advertising';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/economylib.php';

if($user['license'] == 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

$command = 'SELECT * FROM psypets_advertising WHERE userid=' . $user['idnum'] . ' LIMIT 1';
$this_ad = $database->FetchSingle($command, 'fetching current ad');

if($this_ad !== false)
{
/*
  if($admin['seedebug'] == 'yes')
  {
    echo '<p>';
    print_r($this_ad);
    echo '</p>';
  }
*/
  if($now >= $this_ad['expirytime'] || $_POST['action'] == 'cancel')
  {
    $command = 'DELETE FROM psypets_advertising WHERE idnum=' . $this_ad['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'deleting expired ad');

    $this_ad = false;

    if($now >= $this_ad['expirytime'])
    {
      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Let an Advertising Ad Expire', 1);
    }
    else
    {
      $message = 'Ad canceled.';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Canceled an Advertising Ad', 1);
    }
  }
}

if(strtolower($_POST['submit']) == 'preview')
{
  $message = '';
}
else if($_POST['action'] == 'air' && is_numeric($_POST['airtime']) && $_POST['airtime'] > 0 && $TIME_IS_FUCKED !== true)
{
  $airtime = (int)$_POST["airtime"];
  $aircost = value_with_inflation($airtime * 20);
  $message = trim($_POST["message"]);

  $errored = false;

  if($this_ad !== false)
  {
    $airtime_error = "You're already running an ad.";
    $errored = true;
  }

  if($airtime < 1 || $airtime > 5)
  {
    $airtime_error = 'Choose an airtime between 1 and 5 days.';
    $errored = true;
  }

  if(strlen(stripslashes($message)) < 4)
  {
    $message_error = "Please type a longer message.";
    $errored = true;
  }
  else if(strlen($message) > 1000)
  {
    $message_error = 'Please type a shorter message (1000 characters max).';
    $errored = true;
  }

  if($user['money'] < $aircost)
  {
    $airtime_error = 'You cannot afford to run a broadcast for that long.';
    $errored = true;
  }

  if($errored === false)
  {
    $message = quote_smart($message);

    $command = 'INSERT INTO psypets_advertising (userid, expirytime, ad) ' .
               'VALUES (' . $user['idnum'] . ', ' . ($now + $airtime * 60 * 60 * 24) . ', ' . $message . ')';
    $database->FetchNone($command, 'adding advertisement');

    take_money($user, $aircost, 'Advertising fee');

    $message = 'Advertisment submitted.  Thank you for your service.';
       
    $command = 'SELECT * FROM psypets_advertising WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    $this_ad = $database->FetchSingle($command, 'fetching new ad');

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Posted an Advertising Ad', 1);
  }
  else
    $_POST["message"] = stripslashes($_POST["message"]);
}
else if($_POST['action'] == 'extend' && $this_ad !== false && is_numeric($_POST['airtime']) && $_POST["airtime"] > 0 && $TIME_IS_FUCKED !== true)
{
  $extension = (int)$_POST['airtime'];
  
  $days_left = ceil(($this_ad['expirytime'] - $now) / (60 * 60 * 24));
  
  if($days_left + $extension <= 5)
  {
    $aircost = value_with_inflation($extension * 20);

    if($user["money"] < $aircost)
    {
      $airtime_error = "You cannot afford to run a broadcast for that long.";
    }
    else
    {
      take_money($user, $aircost, 'Advertising fee');

      $command = 'UPDATE psypets_advertising SET expirytime=expirytime+' . ($extension * 60 * 60 * 24) . ' WHERE idnum=' . $this_ad['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating ad time');

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Extended an Advertising Ad', 1);

      header('Location: ./broadcast.php');
      exit();
    }
  }
}
else
  $_POST['airtime'] = 2;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Advertising</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript">
		$(function() {
			init_textarea_editor();
		});
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Advertising</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="broadcast.php">Post Ad</a></li>
      <li><a href="broadcast_ads.php">Running Ads</a></li>
     </ul>
<?php
echo '<a href="npcprofile.php?npc=Maya Wirt"><img src="gfx/npcs/advertising.png" align="right" width="350" height="380" alt="(Maya Wirt, advertising receptionist)" /></a>';
include 'commons/dialog_open.php';
if($message)
  echo "     <p class=\"success\">$message</p>\n";

if($TIME_IS_FUCKED === true)
{
  echo '<p>Unfortunately, Advertising has been disabled for the moment.  An explanation should have been posted in the <a href="/cityhall.php">City Hall</a>...</p>';
  include 'commons/dialog_close.php';
}
else
{
  if($this_ad === false)
  {
?>
     <p>Type a message you want to appear on the site, and choose how long you'd like it to be aired.  Ads will be displayed in <a href="/park.php">The Park</a> and <a href="/fleamarket/">Flea Market</a>.</p>
     <p>Remember that ads are moderated by the <?= $SETTINGS['site_name'] ?> community as a whole.  If your ad is found to be terribly offensive (some would argue that extreme chat speak is offensive) it may be removed without refund!</p>
<?php
include 'commons/dialog_close.php';
?>
     <ul>
      <li><a href="/help/advertising.php">Find out more about ad moderation</a></li>
     </ul>
     <form method="post">
<?php
    if($airtime_error)
      echo "     <p class=\"failure\">$airtime_error</p>\n";
?>
     <table>
      <tr class="titlerow">
       <th>&nbsp;</th>
       <th>Airtime</th>
       <th>Cost</th>
      </tr>
      <tr class="row">
       <td><input type="radio" name="airtime" value="1"<?= $_POST["airtime"] == 1 ? " checked" : ""?> /></td>
       <td>One&nbsp;day</td>
       <td align="center"><?= value_with_inflation(1 * 20) ?><span class="money">m</span></td>
      </tr>
      <tr class="altrow">
       <td><input type="radio" name="airtime" value="2"<?= $_POST["airtime"] == 2 ? " checked" : ""?> /></td>
       <td>Two&nbsp;days</td>
       <td align="center"><?= value_with_inflation(2 * 20) ?><span class="money">m</span></td>
      </tr>
      <tr class="row">
       <td><input type="radio" name="airtime" value="3"<?= $_POST["airtime"] == 3 ? " checked" : ""?> /></td>
       <td>Three&nbsp;days</td>
       <td align="center"><?= value_with_inflation(3 * 20) ?><span class="money">m</span></td>
      </tr>
      <tr class="altrow">
       <td><input type="radio" name="airtime" value="4"<?= $_POST["airtime"] == 4 ? " checked" : ""?> /></td>
       <td>Four&nbsp;days</td>
       <td align="center"><?= value_with_inflation(4 * 20) ?><span class="money">m</span></td>
      </tr>
      <tr class="row">
       <td><input type="radio" name="airtime" value="5"<?= $_POST["airtime"] == 5 ? " checked" : ""?> /></td>
       <td>Five&nbsp;days</td>
       <td align="center"><?= value_with_inflation(5 * 20) ?><span class="money">m</span></td>
      </tr>
     </table>
<?php
    if($_POST["submit"] == "Preview")
    {
      $preview = $_POST["message"];
?>
     <h5>Preview</h5>
     <div id="ingamead" style="float: none;">
      <?= format_text($preview, false) ?>
     </div>
<?php
    }

    if($message_error)
      echo "     <p class=\"failure\">$message_error</p>\n";
?>
     <h5>Message</h5>
     <table>
      <tr>
       <td>
			  <ul data-target="message-body" class="textarea-editor"></ul>
        <textarea id="message-body" name="message" cols="40" rows="10" style="width:350px;"><?= $_POST["message"] ?></textarea>
       </td>
      </tr>
      <tr>
       <td align="right">
        <input type="hidden" name="action" value="air"><input type="submit" name="submit" value="Preview" />&nbsp;<input type="submit" value="Broadcast Message" class="bigbutton" />
       </td>
      </tr>
     </table>
     </form>
     <h5>Formatting Help</h5>
     <?= formatting_help(); ?>
<?php
  }
  else
  {
?>
<p>You're already running an ad at this time, due to expire in <?= Duration($this_ad["expirytime"] - $now, 2) ?>.</p>
<p>I can cancel the ad for you, if you like, or extend the ad up to a maximum air time of 5 days.</p>
<?php
include 'commons/dialog_close.php';
?>
<h5>Your Current Ad</h5>
<div id="ingamead" style="float: none;">
 <?= nl2br(format_text($this_ad['ad'])) ?>
</div>
<h5>Cancel Ad</h5>
<form action="broadcast.php" method="post">
<p>You may cancel this ad now, but you will not be refunded.</p>
<p><input type="hidden" name="action" value="cancel" /><input type="submit" value="Cancel Ad" /></p>
</form>
<h5>Extend Ad</h5>
<p>You may extend the life of this ad, up to a maximum of 5 days from now (if your ad is already set to expire in 5 days, you must wait at least one day to extend it).</p>
<?php
    $days_left = ceil(($this_ad["expirytime"] + 1 - $now) / (60 * 60 * 24));

    if($days_left < 5)
    {
?>
<form action="broadcast.php" method="post">
<table>
 <tr class="titlerow">
  <th>&nbsp;</th>
  <th>Extension</th>
  <th>Cost</th>
 </tr>
 <tr class="row">
  <td><input type="radio" name="airtime" value="1" checked /></td>
  <td>One&nbsp;day</td>
  <td align="center"><?= value_with_inflation(1 * 20) ?><span class="money">m</span></td>
 </tr>
<?php
      if($days_left < 4)
      {
?>
 <tr class="altrow">
  <td><input type="radio" name="airtime" value="2" /></td>
  <td>Two&nbsp;days</td>
  <td align="center"><?= value_with_inflation(2 * 20) ?><span class="money">m</span></td>
 </tr>
<?php
        if($days_left < 3)
        {
?>
 <tr class="row">
  <td><input type="radio" name="airtime" value="3" /></td>
  <td>Three&nbsp;days</td>
  <td align="center"><?= value_with_inflation(3 * 20) ?><span class="money">m</span></td>
 </tr>
<?php
          if($days_left < 2)
          {
?>
 <tr class="altrow">
  <td><input type="radio" name="airtime" value="4" /></td>
  <td>Four&nbsp;days</td>
  <td align="center"><?= value_with_inflation(4 * 20) ?><span class="money">m</span></td>
 </tr>
<?php
          }
        }
      }
?>
</table>
<p><input type="hidden" name="action" value="extend" /><input type="submit" value="Extend" /></p>
</form>
<?php
    }
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
