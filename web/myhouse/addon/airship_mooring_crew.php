<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Airship Mooring';
$THIS_ROOM = 'Airship Mooring';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if(!addon_exists($house, 'Airship Mooring'))
{
  header('Location: /myhouse.php');
  exit();
}

$shipid = (int)$_GET['idnum'];
$airship = get_airship_by_id($shipid);

if($airship === false || $airship['ownerid'] != $user['idnum'])
{
  header('Location: /myhouse/addon/airship_mooring.php');
  exit();
}

$crew_count = count_crew($airship);

$bonuses = airship_crew_linear_bonus($airship, $user['user']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Airship Mooring &gt; <?= $airship['name'] ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/airship_mooring.php">Airship Mooring</a> &gt; <?= $airship['name'] ?></h4>
<?php
room_display($house);
?>
     <ul class="tabbed">
      <li><a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>">Parts</a></li>
      <li class="activetab"><a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $shipid ?>">Crew</a></li>
     </ul>
     <h5>Summary</h5>
     <p>Space remaining: <?= $airship['maxbulk'] - $airship['bulk'] ?></p>
     <table>
      <tr><th>Seats</th><td><?= $airship['seats'] ?></td></tr>
      <tr><th>Weight</th><td><?= ($airship['weight'] / 10) ?></td></tr>
     </table>
     <h5>Crew (<?= $crew_count . '/' . $airship['seats'] ?>)</h5>
<?php
if($airship['seats'] == 0)
  echo '<p>This airship has no Seats.  To add Seats, add chairs or couches to the airship.</p>';
else if($airship['returntime'] > $now)
  echo '     <ul><li class="dim">Change crew (cannot change crew while ship is out)</li></ul>';
else
  echo '     <ul><li><a href="/myhouse/addon/airship_mooring_addcrew.php?idnum=' . $shipid . '">Change crew</a></li></ul>';

if($crew_count > 0)
{
?>
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th><th>Bonuses</th>
      </tr>
<?php
  $members = explode(',', $airship['crewids']);

  $rowclass = begin_row_class();
  $count = 0;

  foreach($members as $member)
  {
    $pet = get_pet_byid($member);
    $count++;

    $bonuses = airship_pet_bonus_direct($pet, $user['user']);
    
    if($count > $airship['seats'])
      $extra_class = ' dim';
?>
      <tr class="<?= $rowclass . $extra_class ?>">
       <td></td>
       <td><?= pet_graphic($pet) ?></td>
       <td><?= $pet['petname'] ?></td>
       <td>&lt;undefined&gt;</td>
      </tr>
<?php
    $rowclass = alt_row_class();
  }
?>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
