<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/newslib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';

$favor_cost = 200;

if($_POST['action'] == 'rename' && $user['favor'] >= $favor_cost)
{
  $_POST['newname'] = trim($_POST['newname']);

  $other_user = get_user_bydisplay($_POST['newname']);

  if(strlen($_POST['newname']) < 2 || strlen($_POST['newname']) > 24)
    $error_message = 'Your resident name must be between 2 and 24 characters.';
  else if(preg_match("/[^a-zA-Z0-9Ç-¦_ .!?~'-]/", $_POST['newname']))
    $error_message = 'Please only use alphanumeric characters (or some punctuation)';
  else if(preg_match("/[^a-zA-Z]/", $_POST['newname']{0}))
    $error_message = 'Your resident name must start with a letter (from the Roman alphabet).';
  else if($_POST['newname'] == $user['display'])
    $error_message = 'That <em>is</em> your name...';
  else if($other_user['idnum'] > 0)
    $error_message = 'There is already a resident named ' . $other_user['display'] . '.';
  else
  {
    spend_favor($user, $favor_cost, 'resident rename - "' . $user['display'] . '" to "' . $_POST['newname'] . '"');

    $database->FetchNone('
      UPDATE monster_users
      SET
        display=' . quote_smart($_POST['newname']) . ',
        display_normalized=' . quote_smart(normalized_display_name($_POST['newname'])) . '
      WHERE idnum=' . $user['idnum'] . '
      LIMIT 1
    ');

    $old_name = $user['display'];
    $user['display'] = $_POST['newname'];

    $command = 'UPDATE monster_profiles SET name=' . quote_smart($_POST['newname']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating searchable profile to match');

    $author = get_user_byuser('psypets');
    news_post($author['idnum'], 'important', 'A resident has changed their name.', 'The resident known as ' . $old_name . ' is now called ' . resident_link($_POST['newname']) . '.  PsyMail, plaza posts, etc all now use this new name.');

    header('Location: ./cityhall.php');
    exit();
  }
}

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; City Hall &gt; Name Change Application</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; Name Change Application</h4>
     <ul class="tabbed">
      <li><a href="cityhall.php">Bulletin Board</a></li>
      <li><a href="/help/">Help Desk</a></li>
      <li><a href="cityhall_106.php">Room 106</a></li>
<?php
if($quest_totem['value'] >= 4)
  echo '<li><a href="cityhall_210.php">Room 210</a></li>';
?>
      <li class="activetab"><a href="af_resrename2.php">Name Change Application</a></li>
      <li><a href="af_movepet2.php">Pet Exchange</a></li>
     </ul>
<img src="gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php
include 'commons/dialog_open.php';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if($message)
  echo '<p class="success">' . $message . '</p>';
else
{
?>
     <p>Occasionally a Resident is not satisified with their name, and would like to change it.  Of course, they <em>could</em> delete their account and sign up again, but this is not always a desirable solution for obvious reasons.</p>
     <p>We therefore offer a name changing service.  While this does keep your account intact, it also costs <strong><?= $favor_cost ?> Favor</strong> to do.  This fee is to make sure that Residents only change their name when they <em>really</em> want to, since frequent name changes would be very confusing for the rest of the community.</p>
<?php
}

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.  Changing your name costs <?= $favor_cost ?>.</p>
<?php

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($user['favor'] >= $favor_cost)
{
?>
     <form action="af_resrename2.php" method="post">
     <table>
      <tr>
       <td>New resident name:</td>
       <td><input name="newname" value="<?= $_POST['newname'] ?>" /></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="rename" /><input type="submit" value="Rename Me" class="bigbutton" /></p>
     </form>
<?php
}
?>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
