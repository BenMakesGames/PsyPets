<?php
function love_options($inventory)
{
  $items = array();

  $options[-1] = 'Pet';

  foreach($inventory as $this_item)
  {
    $item_details = get_item_byname($this_item['itemname']);

    if($this_item['health'] < $item_details['durability'] && ($this_item['itemname'] == '10cm Katamari' || $this_item['itemname'] == '50cm Katamari'))
      $options[$this_item['idnum']] = $item_details['playdesc'] . ' (' . durability($this_item['health'], $item_details['durability']) . ')';
    else if(!in_array($this_item['itemname'], $items))
    {
      if($item_details['playdesc'] != '')
        $options[$this_item['idnum']] = $item_details['playdesc'];

      $items[] = $this_item['itemname'];
    }
  }

  return $options;
}
?>
