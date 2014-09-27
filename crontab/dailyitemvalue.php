<?php
/* Calculates item values.
*/

$require_petload = 'no';

$IGNORE_MAINTENANCE = true;

//ini_set('include_path', '/your/web/root');

$now = time();

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/utility.php';
require_once 'commons/globals.php';
require_once 'commons/itemlib.php';
require_once 'commons/economylib.php';
require_once 'commons/userlib.php';

if(date('j') == 25)
  psymail_user('telkoth', 'psypets', 'alchemist potion exchange reminder', 'Don\'t forget to evaluate the potion exchanges, and keep them up to date with the latest items and in-game quantities.');

$items = $database->FetchMultiple('SELECT idnum,itemname,value,recycle_for,itemtype,recycle_fraction,is_edible,ediblecaffeine,edibleenergy,ediblefood,ediblesafety,ediblelove,edibleesteem FROM monster_items ORDER BY idnum ASC'); // <-- something can only recycle into a lower-idnum item... MOSTLY >_>

$updated_item_count = 0;

foreach($items as $item)
{
  if($item['is_edible'] == 'yes')
  {
    if($item['recycle_for'] == '')
      $newvalue = $item['ediblecaffeine'] + $item['edibleenergy'] * 2 + $item['ediblefood'] * 3 + $item['ediblesafety'] * 2 + $item['ediblelove'] *2 + $item['edibleesteem'] * 2;
    else
      $newvalue = recycle_value($item);
  }
  else
  {
    if($item['recycle_for'] == '')
      $newvalue = $item['value'];
    else
      $newvalue = recycle_value($item);
  }

  if($newvalue < 1)
    $newvalue = 1;

  $command = 'UPDATE monster_items SET ' .
             "value=$newvalue " .
             'WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating item value');

  if($newvalue != $item['value'])
  {
    echo '* ', $item['itemname'], '\'s value changes from ', $item['value'], ' to ', $newvalue, ".\r\n";
    $updated_items[] = array('itemname' => $item['itemname'], 'idnum' => $item['idnum']);
    $updated_item_count++;
  }
}

echo '* Item values adjusted: ', $updated_item_count, "\r\n";

if($updated_item_count > 0)
{
  foreach($updated_items as $item_info)
  {
    get_item_byname($item_info['itemname'], true);
    get_item_byid($item_info['idnum'], true);
  }

  echo '* Forced cache to get new data for those items from the DB', "\r\n";
}

echo 'Finished market update.';
?>
