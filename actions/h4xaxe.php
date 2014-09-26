<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

$items = array(
  'Logic Board', 'Complex Circuit', 'Simple Circuit', 'CPU', 'Copper',
);

$yields = array();
$c = mt_rand(3, mt_rand(3, 5));

for($i = 0; $i < $c; ++$i)
  $yield[] = $items[array_rand($items)];

echo 'Lewt: ';

foreach($yield as $item)
{
  $item_list[] = $item;
  add_inventory($user['user'], '', $item, 'Mad skillz!', $this_inventory['location']);
}

sort($item_list);

echo implode(', ', $item_list);

require_once 'commons/statlib.php';
$got_badge = record_stat_with_badge($user['idnum'], 'h4x Axe Pwnage', 1, 1337, 'rush');

if($got_badge)
  echo '<p class="success"><i>(You received the ZERG RUSH KEKEKE Badge!)</i></p>';
?>
