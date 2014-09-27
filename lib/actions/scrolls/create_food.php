<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

$num_items = mt_rand(3, mt_rand(4, 6));

$itemdata = $database->FetchMultiple('
  SELECT itemname
  FROM monster_items
  WHERE
    is_grocery=\'yes\'
    AND rare=\'no\'
  ORDER BY RAND()
  LIMIT ' . $num_items . '
');

$item_list = array();

foreach($itemdata as $i)
{
  add_inventory($user['user'], 'u:' . $user['idnum'], $i['itemname'], $user['display'] . ' summoned this', $this_inventory['location']);
  $item_list[] = $i['itemname'];
}  
?>
<p>The scroll melts in your hands, revealing <?= list_nice($item_list) ?> as it drips to the floor.</p>
