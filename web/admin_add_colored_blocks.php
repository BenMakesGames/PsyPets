<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

	$items = array(
    'Blue D Block', 'Blue F Block', 'Blue J Block', 'Blue G Block', 'Blue O Block',
    'Blue P Block', 'Blue Q Block', 'Blue R Block', 'Blue S Block', 'Blue X Block',

    'Green B Block', 'Green G Block', 'Green H Block', 'Green K Block', 'Green N Block',
    'Green U Block', 'Green V Block', 'Green Y Block', 'Green Z Block',

    'Orange D Block',

    'Purple A Block', 'Purple B Block', 'Purple C Block', 'Purple E Block', 'Purple I Block',
    'Purple J Block', 'Purple M Block', 'Purple N Block', 'Purple O Block', 'Purple T Block',
    'Purple W Block', 'Purple X Block',

    'Red A Block', 'Red C Block', 'Red H Block', 'Red I Block', 'Red L Block', 'Red P Block',
    'Red R Block', 'Red S Block', 'Red Z Block',

    'Yellow F Block', 'Yellow K Block', 'Yellow L Block', 'Yellow M Block', 'Yellow Q Block',
    'Yellow T Block', 'Yellow U Block', 'Yellow V Block', 'Yellow Y Block',
  );

foreach($items as $itemname)
{
  list($color, $letter, $block) = explode(' ', $itemname);
  
  $graphic = 'letters/block_' . strtolower($letter) . '_' . strtolower($color) . '.png';

  $command = 'SELECT idnum FROM monster_items WHERE itemname=' . quote_smart($itemname) . ' LIMIT 1';
  $existing = $database->FetchSingle($command);
  
  if($existing === false)
  {

    $command = '
      INSERT INTO monster_items (itemname, itemtype, custom, bulk, weight,
      graphic, value, rare, permanent, nosellback, admin_notes) VALUES
      (' . quote_smart($itemname) . ', \'craft/sculpture/wood\', \'recurring\', 1, 1,
      ' . quote_smart($graphic) . ', 1, \'yes\', \'yes\', \'yes\', \'recurring February item\')
    ';
    $database->FetchNone($command, 'adding lettered block "' . $itemname . '"');

    echo 'Added "' . $itemname . '"<br />';
  }
  else
    echo '"' . $itemname . '" already exists<br />';
}
?>
