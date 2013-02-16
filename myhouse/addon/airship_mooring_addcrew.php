<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

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

if($airship === false || $airship['ownerid'] != $user['idnum'] || $airship['returntime'] > $now)
{
  header('Location: /myhouse/addon/airship_mooring.php');
  exit();
}

if($airship['seats'] == 0)
{
  header('Location: /myhouse/addon/airship_mooring_crew.php?idnum=' . $shipid);
  exit();
}

if($_POST['submit'] == 'Change')
{
  $petids = array();

  foreach($_POST as $key=>$value)
  {
    if($value == 'on' || $value == 'yes')
      $petids[] = (int)$key;
  }
  
  if(count($petids) > $airship['seats'])
    $messages[] = '<span class="failure">This airship can hold a maximum of ' . $airship['seats'] . ' pet' . ($airship['seats'] != 1 ? 's' : '') . ', but you selected ' . count($petids) . '.';
  else
  {
    set_airship_crew($airship, $petids);
    header('Location: /myhouse/addon/airship_mooring_crew.php?idnum=' . $shipid);
  }
}

$crew_count = count_crew($airship);

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
      <li><a href="/myhouse/addon/airship_mooring_edit.php?idnum=<?= $shipid ?>">Parts</a></li>
      <li class="activetab"><a href="/myhouse/addon/airship_mooring_crew.php?idnum=<?= $shipid ?>">Crew</a></li>
     </ul>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <h5>Summary</h5>
     <p>Space remaining: <?= ($airship['maxbulk'] - $airship['bulk']) / 10 ?></p>
     <table>
      <tr><th>Seats</th><td><?= $airship['seats'] ?></td></tr>
      <tr><th>Weight</th><td><?= $airship['weight'] ?></td></tr>
     </table>
     <h5>Pets</h5>
     <p>This airship has seating for <?= $airship['seats'] . ' pet' . ($airship['seats'] != 1 ? 's' : '') ?>.</p> 
<?php
if(count($userpets) > 0)
{
  $crew = take_apart(',', $airship['crewids']);
?>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Pet</th><th>Bonuses</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($userpets as $pet)
  {
    if(in_array($pet['idnum'], $crew))
      $checked = ' checked';
    else
      $checked = '';

    $bonuses = airship_pet_bonus_direct($pet, $user['user']);
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="<?= $pet['idnum'] ?>"<?= $checked ?> /></td>
       <td><?= pet_graphic($pet) ?></td>
       <td><?= $pet['petname'] ?></td>
       <td>&lt;undefined&gt;</td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="submit" name="submit" value="Change" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
