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
     <h5>Crew (<?= $crew_count . '/' . $airship['seats'] ?>)</h5>
<?php if($crew_count > 0): ?>
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th>
      </tr>
    <?php
      $members = explode(',', $airship['crewids']);

      $rowclass = begin_row_class();
      $count = 0;

      foreach($members as $member)
      {
        $pet = get_pet_byid($member);
        $count++;

        if($count > $airship['seats'])
          $extra_class = ' dim';
    ?>
      <tr class="<?= $rowclass . $extra_class ?>">
       <td></td>
       <td><?= pet_graphic($pet) ?></td>
       <td><?= $pet['petname'] ?></td>
      </tr>
    <?php
        $rowclass = alt_row_class($rowclass);
      }
    ?>
     </table>
<?php endif; ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
