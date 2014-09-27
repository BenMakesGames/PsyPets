<?php
function get_back($materials)
{
  global $_POST, $_GET, $FINISHED_CASTING, $user;

  $success = false;

  if($_POST['submit'] == 'Reclaim')
  {
    $itemname = $_POST['item'];

    $command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=' . quote_smart($itemname) . ' LIMIT 1';
    $item = fetch_single($command, 'fetching inventory item to "recycle"');

    if($item !== false)
    {
      $details = get_item_byname($itemname);

      if($details['can_recycle'] == 'yes')
      {
        delete_inventory_byid($item['idnum']);


        $components = explode(',', $details['recycle_for']);
        $reclaimed = array();

        foreach($components as $component)
        {
          if(in_array($component, $materials))
          {
            add_inventory($user['user'], '', $component, 'Reclaimed at ' . $user['display'] . '\'s Shrine', $user['incomingto']);
            $reclaimed[] = $component;
          }
        }

        $listtext = '';
        $num_items = 0;
        
        if(count($reclaimed) > 0)
        {
          foreach($reclaimed as $component)
          {
            $num_items++;

            if($num_items > 1)
              $listtext .= ($num_items == count($reclaimed) ? ' and ' : ', ');

            $listtext .= $component;
          }
        }
        
        $success = true;
      }
      else
        $messages[] = '<span class="failure">That item cannot be recycled.</span>';
    }
    else
      $messages[] = '<span class="failure">That item does not exist, or is not in storage.</span>';
  }

  if($success)
  {
    $FINISHED_CASTING = true;

    if($num_items == 0)
      echo '<p>In a puff of magic, the ' . $item['itemname'] . ' is gone... apparently it didn\'t have any ' . implode(' or ', $materials) . ' in it...  Darn.</p>';
    else
      echo '<p>In a puff of magic, the ' . $item['itemname'] . ' is gone.  ' . $listtext . ' ' . ($num_items == 1 ? 'has' : 'have') . ' taken its place.  (Find any items recovered in ' . $user['incomingto'] . '.)</p>';
  }
  else
  {
    if(count($messages) > 0)
      echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

    $command = 'SELECT a.idnum,a.itemname,b.graphic,b.graphictype,COUNT(a.itemname) AS c FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\' AND b.can_recycle=\'yes\' GROUP BY (a.itemname)';
    $inventory = fetch_multiple($command);

    if(count($inventory) == 0)
      echo '<p>You have no recyclable items in your Storage.</p>';
    else
    {
      echo '<p>Choose an item to reclaim the ' . implode(' and ', $materials) . ' from (any other materials will be lost).  Only recyclable items in your Storage are listed here.</p><p>Be warned!  If you choose an item which does not contain ' . implode(' or ', $materials) . ', the item will still be destroyed, and you will receive nothing!</p>' .
           '<p><i>(The quantity listed here is the quantity currently in your storage, not the number of items you will reclaim the ' . implode(' and ', $materials) . ' from.  You will only reclaim the ' . implode(' and ', $materials) . ' from one item per casting of this spell.)</i></p>' .
           '<form action="/myhouse/addon/shrine_spell.php?spell=' . $_GET['spell'] . '" method="post">' .
           '<table><tr class="titlerow"><th></th><th></th><th>Item</th><th>Quantity</th></tr>';

      $rowclass = begin_row_class();

      foreach($inventory as $item)
      {
        echo '<tr class="' . $rowclass . '"><td><input type="radio" name="item" value="' . $item['itemname'] . '" /></td>' .
             '<td class="centered">' . item_display($item, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . $item['c'] . '</td></tr>';

        $rowclass = alt_row_class($rowclass);
      }

      echo '</table><p><input type="submit" name="submit" value="Reclaim" /></p></form>';
    }
  }
}
?>
