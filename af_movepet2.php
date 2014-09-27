<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';

if($now_year > 2010 || ($now_year == 2010 && $now_month >= 4))
  $favor_cost = 200;
else
  $favor_cost = 500;

if($_POST['action'] == 'movepet' && $user['favor'] >= $favor_cost)
{
  $target_user = get_user_bydisplay($_POST["residentname"]);
  $get_pet = get_pet_byid((int)$_POST["petid"]);

  if($target_user === false)
  {
    $errored = true;
    $error_message = 'I have no record of a Resident by that name.  Please double-check your spelling.';
  }
  else if($get_pet === false || $get_pet['location'] != 'home')
  {
    $errored = true;
    $error_message = 'You forgot to select the pet to transfer...';
  }
  else if($get_pet['user'] != $user['user'])
  {
    $errored = true;
    $error_message = 'The selected pet does not belong to you.';
  }
  else if($get_pet['dead'] != 'no')
  {
    $errored = true;
    $error_message = 'You cannot move a dead pet.';
  }
  else if($target_user['display'] != $_POST['residentname'] || (int)$target_user['idnum'] == 0)
  {
    $errored = true;
    $error_message = "The login name given does not match the resident name.  Check the information and try again.";
  }

  if($errored == false)
  {
    $command = 'DELETE FROM psypets_pet_market WHERE petid=' . $get_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'unlisting pet from pet market');

    $command = 'UPDATE monster_pets SET user=' . quote_smart($target_user['user']) . ' WHERE idnum=' . $get_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'transfering pet');

    $descript = 'pet transfer - ' . $get_pet['petname'] . ' (#' . $get_pet['idnum'] . ') to ' . $target_user['display'] . ' (#' . $target_user['idnum'] . ')';

    spend_favor($user, $favor_cost, $descript);

    $body = '{r ' . $user['display'] . '} has transfered their pet, ' . $get_pet['petname'] . ', to you.<br /><br />' .
            'Hopefully {r ' . $user['display'] . '} informed you about this move ahead of time.  If not, we apologize for the inconvenience.  Either way, we wish the best of luck to both you and ' . $get_pet['petname'] . '.<br /><br />';
    psymail_user($target_user['user'], 'csilloway', 'A pet has been transfered to you!', $body);

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('A Paid Pet Exchange Was Made', 1);

    $message = 'You want to give ' . $get_pet['petname'] . ' to ' . $target_user['display'] . '...  I see.  Well, everything looks in order.  We\'ll process the move immediately.';
    $_POST = array();
  }
}

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; City Hall &gt; Pet Exchange</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/cityhall.php">City Hall</a> &gt; Pet Exchange</h4>
     <ul class="tabbed">
      <li><a href="/cityhall.php">Bulletin Board</a></li>
      <li><a href="/help/">Help Desk</a></li>
      <li><a href="/cityhall_106.php">Room 106</a></li>
<?php
if($quest_totem['value'] >= 4)
  echo '<li><a href="/cityhall_210.php">Room 210</a></li>';
?>
      <li><a href="/af_resrename2.php">Name Change Application</a></li>
      <li class="activetab"><a href="/af_movepet2.php">Pet Exchange</a></li>
     </ul>
<img src="//saffron.psypets.net/gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php
include 'commons/dialog_open.php';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if($message)
  echo '<p class="success">' . $message . '</p>';
else
{
  echo '<p>Occasionally a Resident wants to transfer ownership of a pet.  We\'d like to see this happen as little as possible - besides being a bit of paper work, Eve says it ruins some of the experimental data - but we also understand that it\'s sometimes necessary.</p>' .
       '<p>We\'ve therefore set a price of <b>' . $favor_cost . ' Favor per pet transfer</b>.  If you\'d like to go ahead with the process, please select the pet you\'d like to transfer, and provide the name of the Resident you\'d like to send that pet to.</p>' .
       '<p>Please be sure the other Resident is expecting the transfer!</p>';

  if($user['breeder'] == 'yes')
    echo '<p>If you transfer a pet which is listed in the Pet Market, it will be unlisted from the Pet Market.</p>';
}

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.  Moving a pet will cost <?= $favor_cost ?>.</p>
<?php
if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($user['favor'] >= $favor_cost)
{
  $dead_pets = 0;

  foreach($userpets as $num=>$pet)
  {
    if($pet['dead'] != 'no')
      $dead_pets++;
  }

  if($dead_pets < count($userpets))
  {
?>
     <form action="af_movepet2.php" method="post">
     <h5>Pet to Transfer</h5>
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th>
      </tr>
<?php
    $row = begin_row_class();

    foreach($userpets as $num=>$pet)
    {
      if($pet['dead'] == 'no')
      {
?>
      <tr class="<?= $row ?>">
       <td><input type="radio" name="petid" value="<?= $pet["idnum"] ?>"<?= ($_POST['petid'] == $pet["idnum"] ? " checked" : "") ?> /></td>
       <td><img src="gfx/pets/<?= $pet['graphic'] ?>" height="48" width="48" alt="" /></td>
       <td><?= $pet['petname'] ?></td>
      </tr>
<?php
        $row = alt_row_class($row);
      }
    }
?>
     </table>
     <h5>Resident to Receive Pet</h5>
     <p>
      <input name="residentname" maxlength="24" value="<?= $_POST['residentname'] ?>" />&nbsp;<span class="size13">&larr;</span>&nbsp;<select name="buddylist" style="width:200px;" onchange="residentname.value=buddylist.value;">
       <option value=""></option>
<?php
if(strlen($user['friends']) > 0)
{
  $friend_list = explode(',', $user['friends']);

  foreach($friend_list as $idnum)
  {
    $friend = get_user_byid($idnum, 'display');
    if($friend !== false)
      $names[] = $friend['display'];
  }

  sort($names);

  foreach($names as $name)
    echo '<option value="' . $name . '">' . $name . '</option>';
}
?>
      </select>
     </p>
     <p><input type="hidden" name="action" value="movepet" /><input type="submit" onclick="return confirmrequest();" value="Move Pet" /></p>
     </form>
<?php
  }
  else
  {
?>
     <p>You do not have any pets which can be moved at this time (or those pets you do have are dead).</p>
<?php
    if($dead_pets > 0)
      echo '<p>If you would like to revive a dead pet, visit <a href="temple.php">The Temple</a> for <a href="af_revive2.php">Resurrections</a>.</p>';
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
