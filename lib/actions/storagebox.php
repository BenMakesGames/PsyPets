<?php
if($okay_to_be_here !== true)
  exit();

if(strlen($this_inventory["data"]) == 0)
  echo 'There\'s nothing inside!  Useless box!</p><p>Well... at least you can keep the Cardboard Box...';
else
{
  $list = $this_inventory['data'];

  $items = explode(',', $list);

  $item_count = array();

  foreach($items as $item)
  {
    $item = str_replace('&#2c;', ',', $item);
    add_inventory_cached($user['user'], '', $item, $this_inventory['message'], $this_inventory['location']);
    $item_count[$item]++;
  }

  echo 'Opening the ' . $this_inventory['itemname'] . ' reveals:</p><ul>';

  foreach($item_count as $name=>$count)
    echo '<li>' . $count . 'x ' . $name . '</li>';

  echo '</ul><p>Oh, and you can keep the Cardboard Box.';
}

add_inventory_cached($user['user'], '', 'Cardboard Box', $this_inventory['message'], $this_inventory['location']);

process_cached_inventory();

delete_inventory_byid($this_inventory['idnum']);
?>
