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

$bonuses = airship_crew_linear_bonus($airship, $user['user']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Airship Mooring &gt; <?= $airship['name'] ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/airship_mooring.php">Airship Mooring</a> &gt; <?= $airship['name'] ?></h4>
<?php
room_display($house);
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>">Parts</a></li>
      <li><a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $shipid ?>">Crew</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $message_text = form_message(explode(',', $_GET['msg']));

echo '<p>' . $message_text . '</p>';
?>
     <h5>Summary</h5>
     <p>Space remaining: <?= ($airship['maxbulk'] - $airship['bulk']) / 10 ?></p>
     <table>
      <tr><th>Seats</th><td><?= $airship['seats'] ?></td></tr>
      <tr><th>Weight</th><td><?= ($airship['weight'] / 10) ?></td></tr>
     </table>
     <h5>Parts</h5>
     <ul>
<?php
echo '<li><a href="/myhouse/addon/airship_mooring_recycle.php?idnum=' . $shipid . '" onclick="return confirm(\'Really retire this Airship?  All of its parts, as well as the chassis, will be returned to your Incoming, and all record of this ship will be removed - FOREVER.\');">Retire this Airship</a></li>';
?>
     </ul>
<?php
$speed = airship_speed($airship);

if($airship['power'] == 0 && $airship['mana'] == 0)
  $hints[] = 'Your Airship has no remaining Power or Mana.  Many weapons and propulsion systems require Power and/or Mana.  To add some, get an engine, or some mana-generating artifact.</p>';
if($airship['seats'] == 0)
  $hints[] = 'Your Airship has no Seats.  You need a Seat for every pet you want to fly in the Airship.  To get more Seats, add couches or chairs.</p>';
if($speed == 'none')
  $hints[] = 'Your Airship cannot move!  To increase its Speed, add propulsion devices such as Balloons or Propellers.  Remember that more weight means less speed, so when possible, try to add lighter-weight parts.</p>';
if($airship['maxbulk'] - $airship['bulk'] < 5)
  $hints[] = 'Your Airship has little or no Space remaining.  Once an Airship has been completely loaded with Parts, no more can be added.  There is no way to add Space to an Airship, however some chassis offer more or less Space than others.</p>';

if(count($hints) > 0)
  echo '<ul><li>' . implode('</li><li>', $hints) . '</li></ul>';

$rowclass = begin_row_class();
?>
     <form action="/myhouse/addon/airship_mooring_removeparts.php?idnum=<?= $shipid ?>" method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Part</th><th>Weight</th><th>Bulk</th><th>Details</th>
      </tr>
<?php
$details = get_item_byname($airship['chassis']);
$effects = $chassis[$airship['chassis']];
?>
      <tr class="<?= $rowclass ?>">
       <td></td>
       <td class="centered"><?= item_display($details, '') ?></td>
       <td><?= $airship['chassis'] ?></td>
       <td class="centered"><?= ($details['weight'] / 10) ?></td>
       <td class="centered">&nbsp;</td>
       <td><ul class="plainlist"><?= render_airship_bonuses_as_list_xhtml($effects) ?></ul></td>
      </tr>
<?php
if(strlen($airship['parts']) > 0)
{
  $ship_parts = explode(',', $airship['parts']);

  $rowclass = alt_row_class($rowclass);

  foreach($ship_parts as $i=>$part)
  {
    $details = get_item_byname($part);
    $effects = $parts[$part];
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="i<?= $i ?>" /></td>
       <td class="centered"><?= item_display($details, '') ?></td>
       <td><?= $part ?></td>
       <td class="centered"><?= ($details['weight'] / 10) ?></td>
       <td class="centered"><?= ($details['bulk'] / 10) ?></td>
       <td><ul class="plainlist"><?= render_airship_bonuses_as_list_xhtml($effects) ?></ul></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
}
?>
     </table>
<?php
if(strlen($airship['parts']) > 0)
{
  if($airship['returntime'] > $now)
    echo '<p>You cannot remove parts from your Airship while it is out.</p>';
  else
    echo '<p><input type="submit" value="Remove" /></p>';
}
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
