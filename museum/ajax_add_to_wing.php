<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$display = fetch_single('
  SELECT idnum,name,items
  FROM psypets_museum_displays
  WHERE
    userid=' . (int)$user['idnum'] . '
    AND idnum=' . (int)$_POST['display'] . '
  LIMIT 1
');

if($display === false)
  die('<p class="failure">That wing does not exist!?  Try refreshing the page and trying again.</p>');

$old_item_list = take_apart(';', $display['items']);

foreach($_POST['item_id'] as $itemid)
  $item_ids[] = (int)$itemid;

if(count($item_ids) == 0)
  die('<p class="failure">No items were selected.</p>');

$item_ids = array_unique($item_ids);

// get all items donated
$items_donated = fetch_multiple('
  SELECT itemid
  FROM psypets_museum
  WHERE
    userid=' . (int)$user['idnum'] . '
    AND itemid IN (' . implode(',', $item_ids) . ')
  LIMIT ' . count($item_ids) . '
');

foreach($items_donated as $item)
  $donated_ids[] = $item['itemid'];

// you may only add items which you have actually donated!
$item_ids = array_intersect($item_ids, $donated_ids);

if(count($item_ids) == 0)
  die('<p class="failure">No items were selected.</p>');

$details = fetch_multiple('
  SELECT itemname
  FROM monster_items
  WHERE idnum IN (' . implode(',', $item_ids) . ')
  LIMIT ' . count($item_ids) . '
');

foreach($details as $item)
  $new_item_list[] = $item['itemname'];

$new_item_list = array_unique(array_merge($new_item_list, $old_item_list));
sort($new_item_list);

$items_added_count = count($new_item_list) - count($old_item_list);

if($items_added_count > 0)
{
  fetch_none('
    UPDATE psypets_museum_displays
    SET
      num_items=' . count($new_item_list) . ',
      items=' . quote_smart(implode(';', $new_item_list)) . '
    WHERE idnum=' . $display['idnum'] . '
    LIMIT 1
  ');

  if($items_added_count == 1)
    die('<p class="success">Added 1 item to <a href="/museum/editdisplay.php?id=' . $display['idnum'] . '">your ' . $display['name'] . ' display</a>.</p>');
  else
    die('<p class="succces">Added ' . $items_added_count . ' items to <a href="/museum/editdisplay.php?id=' . $display['idnum'] . '">your ' . $display['name'] . ' display</a>.</p>');
}
else
  die('<p class="failure">No items were added to <a href="/museum/editdisplay.php?id=' . $display['idnum'] . '">your ' . $display['name'] . ' display</a>.  (They must all already be present in that display!)</p>');
?>
