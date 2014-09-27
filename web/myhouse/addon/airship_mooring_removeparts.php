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

if(strlen($airship['parts']) == 0)
{
  header('Location: /myhouse/addon/airship_mooring_edit.php?idnum=' . $shipid);
  exit();
}

$ship_parts = explode(',', $airship['parts']);

foreach($_POST as $key=>$value)
{
  if($key{0} == 'i' && ($value == 'on' || $value == 'yes'))
  {
    $i = (int)substr($key, 1);

    if(array_key_exists($i, $ship_parts))
    {
      $refund[] = $ship_parts[$i];
      unset($ship_parts[$i]);
    }
  }
}

if(count($refund) == 0)
{
  header('Location: /myhouse/addon/airship_mooring_edit.php?idnum=' . $shipid);
  exit();
}
/*
echo '<h3>refund</h3>';
print_r($refund);
echo '<h3>new part list</h3>';
print_r($ship_parts);
*/
$details = get_item_byname($airship['chassis']);

$newairship['power'] = 0;
$newairship['mana'] = 0;
$newairship['seats'] = $chassis[$airship['chassis']]['seats'];
/*
$newairship['attack'] = $chassis[$airship['chassis']]['attack'];
$newairship['defense'] = $chassis[$airship['chassis']]['defense'];
$newairship['special'] = $chassis[$airship['chassis']]['special'];
*/
$newairship['weight'] = $details['weight'];
$newairship['bulk'] = 0;
$newairship['maxbulk'] = blimp_size($details['bulk']) * 10;
//$newairship['propulsion'] = $chassis[$airship['chassis']]['propulsion'];

foreach($ship_parts as $this_part)
{
  if(array_key_exists($this_part, $parts))
  {
    $details = get_item_byname($this_part);

    foreach($parts[$this_part] as $stat=>$bonus)
      $newairship[$stat] += $bonus;

    $newairship['bulk'] += $details['bulk'];
    $newairship['weight'] += $details['weight'];
  }
}

if($newairship['power'] < 0 || $newairship['mana'] < 0)
{
  header('Location: /myhouse/addon/airship_mooring_edit.php?idnum=' . $shipid . '&msg=132');
  exit();
}
/*
echo '<h3>new ship details</h3><pre>';
print_r($newairship);
echo '</pre>';
*/
foreach($newairship as $stat=>$value)
  $sets[] = '`' . $stat . '`=' . $value;

$sets[] = 'parts=' . quote_smart(implode(',', $ship_parts));

$command = 'UPDATE psypets_airships SET ' . implode(', ', $sets) . ' WHERE idnum=' . $airship['idnum'] . ' LIMIT 1';
fetch_none($command, 'updating airship specs');

foreach($refund as $itemname)
  add_inventory($user['user'], '', $itemname, 'Recovered from an Airship', 'home');

header('Location: /myhouse/addon/airship_mooring_edit.php?idnum=' . $shipid . '&msg=131');
?>