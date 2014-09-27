<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/petlib.php';
require_once 'commons/favorlib.php';

if($now_year > 2010 || ($now_year == 2010 && $now_month >= 4))
  $favor_cost = 500;
else
  $favor_cost = 500;

$max_revives = floor($user['favor'] / $favor_cost);

if($_POST['action'] == 'revive' && $max_revives > 0)
{
  $pets = array();
  $this_pet = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 4) == 'pet_')
    {
      $i = substr($key, 4);
      $pets[] = (int)$i;
    }
  }

  foreach($pets as $petid)
  {
    $get_pet = get_pet_byid($petid);
    $this_pet[] = $get_pet;
    if($get_pet['dead'] == 'no')
    {
      $errored = true;
      $error_message = 'One or more of the selected pets is not dead.';
      break;
    }
    else if($get_pet['user'] != $user['user'])
    {
      $errored = true;
      $error_message = 'One or more of the selected pets does not belong to you.';
      break;
    }
  }

  if(count($this_pet) > $max_revives)
  {
    $errored = true;
    $error_message = 'You cannot afford to resurrect more than ' . $max_revives . ' pets.';
  }

  if($errored == false)
  {
    $idnums = array();
    foreach($this_pet as $pet)
      $idnums[] = $pet['idnum'];

    foreach($userpets as $localid=>$pet)
    {
      if(in_array($pet['idnum'], $idnums))
        $userpets[$localid]['dead'] = "no";
    }

    $match = implode(',', $idnums);
    $command = "UPDATE monster_pets SET dead='no',energy=12,food=12,love=12,safety=12,esteem=12 WHERE idnum IN ($match) LIMIT " . count($idnums);
    $database->FetchNone($command, 'reviving pets');

    $favor = 'pet resurrection';
    if(count($idnums) > 1)
      $favor .= ' &times;' . count($idnums);

    $message = count($idnums) . ' pet' . (count($idnums) > 1 ? 's were' : ' was') . ' resurrected.';

    spend_favor($user, $favor_cost * count($idnums), $favor);

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Someone Paid to Resurrect a Dead Pet', count($idnums));
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Temple &gt; Resurrections</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="autofavor.php">The Temple</a> &gt; Resurrections</h4>
     <ul class="tabbed">
      <li><a href="temple.php">Donations</a></li>
      <li><a href="temple_exchange.php">Exchanges</a></li>
      <li class="activetab"><a href="af_revive2.php">Resurrections</a></li>
      <li><a href="af_respec.php">Proselytism's Broth</a></li>
     </ul>
<?php
echo '<img src="gfx/npcs/monk.png" align="right" width="350" height="535" alt="(Lance the Monk)" />';

include 'commons/dialog_open.php';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if($message)
  echo '<p class="success">' . $message . '</p>';
else
{
?>
<p>The death of a pet can be hard for you as well as your other pets.  Fortunately, it isn't permanent.</p>
<p>Are you familiar with the drink called Death's Elixir?  It's created by gathering the dew that collects on the statue of Rizi Vizi on the mornings after a full moon.  We - myself and a couple of the other monks - gather and save it so that we may provide it to those in need.</p>
<p>It is not, however, easy to get: a single draught takes almost a year to collect!  Therefore, we must ask for <strong>500 Favor</strong> for each pet you wish to resurrect.</p>
<?php

if($max_revives < 1)
  echo '<p>It looks like you don\'t have enough Favor, and unfortunately I can make no exceptions.  But please come back once you\'ve <a href="buyfavors.php">bought some Favor</a>.</p>';
}

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.  Each pet you wish to revive will cost 500.</p>
<?php
if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($max_revives >= 1)
{
  $dead_pets = 0;

  foreach($userpets as $num=>$pet)
  {
    if($pet['dead'] != 'no')
      $dead_pets++;
  }

  if($dead_pets > 0)
  {
?>
     <form action="af_revive2.php" method="post">
     <table>
      <tr class="titlerow"><th></th><th></th><th>Pet</th></tr>
<?php
    $rowclass = begin_row_class();

    foreach($userpets as $num=>$pet)
    {
      if($pet['dead'] != 'no')
      {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="pet_<?= $pet["idnum"] ?>" /></td>
       <td><img src="gfx/pets/<?= $pet['graphic'] ?>" /></td>
       <td><?= $pet['petname'] ?></td>
      </tr>
<?php
        $rowclass = alt_row_class($rowclass);
      }
    }
?>
     </table>
     <p><input type="hidden" name="action" value="revive" /><input type="submit" value="Revive" /></p>
     </form>
<?php
  }
  else
  {
?>
     <p><i>(None of your pets are dead.)</i></p>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
