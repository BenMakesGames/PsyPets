<?php
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

$command = 'SELECT * FROM psypets_airships';
$airships = $database->FetchMultiple($command, 'fetching all airships...');

foreach($airships as $airship)
{
  $shipid = $airship['idnum'];

  $ship_parts = explode(',', $airship['parts']);

  $details = get_item_byname($airship['chassis']);

  $newairship['power'] = 0;
  $newairship['mana'] = 0;
  $newairship['seats'] = $chassis[$airship['chassis']]['seats'];
  $newairship['attack'] = $chassis[$airship['chassis']]['attack'];
  $newairship['defense'] = $chassis[$airship['chassis']]['defense'];
  $newairship['special'] = $chassis[$airship['chassis']]['special'];
  $newairship['weight'] = $details['weight'];
  $newairship['bulk'] = 0;
  $newairship['maxbulk'] = blimp_size($details['bulk']);
  $newairship['propulsion'] = $chassis[$airship['chassis']]['propulsion'];
  
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
  
  foreach($newairship as $stat=>$value)
    $sets[] = '`' . $stat . '`=' . $value;
  
  $sets[] = 'parts=' . quote_smart(implode(',', $ship_parts));
  
  $command = 'UPDATE psypets_airships SET ' . implode(', ', $sets) . ' WHERE idnum=' . $airship['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating airship specs');
}

echo count($airships) . ' airships were recalculated.';
?>