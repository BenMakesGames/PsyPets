<?php
if($okay_to_be_here !== true)
  exit();

$command = 'SELECT COUNT(idnum) AS qty FROM monster_inventory WHERE itemname=\'Null Device\' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']);
$data = $database->FetchSingle($command, 'fetching quantity of null devices');
$devices = $data['qty'];

if($_GET['step'] == 2)
{
  if($devices > 1)
  {
    delete_inventory_byid($this_inventory['idnum']);
    $command = 'DELETE FROM monster_inventory WHERE itemname=\'Null Device\' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' LIMIT 1';
    $database->FetchNone($command, 'deleting other item');
    
    $items = array('Staff', 'Black Dye', 'Raw Milk', 'Plastic', 'Rubber', 'Baking Chocolate', 'Simple Circuit', 'Tin', 'Zinc', 'Gold', 'Null Device', 'Null Device');
    $prefix = array('Staff' => 'a ', 'Simple Circuit' => 'a ', 'Null Device' => 'a ');
    $colors = array('blue', 'green', 'white');

    $item = $items[array_rand($items)];
    $color = $colors[array_rand($colors)];
    
    echo '
      <p>As you put the two Null Devices near to each other, they begin to glow ', $color, ' and spark!  Undeterred, you place them in such a way that you could imagine a cube being formed using the devices as opposite sides...</p>
      <p>... and that is exactly what happens!  The ', $color, ' glow bends, taking the form of a cube!</p>
      <p>You carefully reach your hand inside, through one of the glowing walls, and a strange energy washes over your arm, causing its hairs to stand on end.</p>
      <p>Finally you feel something, and begin pulling it out.  The cube resists initially, but you force through it, finding yourself with ', $prefix[$item], $item, '.</p>
      <p>The cube, its work apparently done, collapses in on itself, and the final ray of ', $color, ' light vanishes from the room.</p>
    ';

    add_inventory($user['user'], 'u:' . $user['idnum'], $item, 'Extracted from a glowing ' . $color . ' cube.', $this_inventory['location']);

    if($item == 'Null Device')
    {
      echo '<p>(What a rip-off!)</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Got Ripped-off By a Glowing ' . ucfirst($color) . ' Cube', 1);
    }
    
    $AGAIN_WITH_ANOTHER = true;
    
    $did_it = true;
  }
}

if(!$did_it)
{
  echo
    '<p>There ', ($devices == 1 ? 'is 1 Null Device' : 'are ' . $devices . ' Null Devices'), ' in this room.</p>',
    '<p>You\'ve heard you can do something interesting if you put two of them together.</p>'
  ;

  if($devices > 1)
    echo
      '<p>Want to try it out?</p>',
      '<ul><li><a href="itemaction.php?idnum=', $this_inventory['idnum'], '&step=2">Yeah-yeah!</a></li></ul>'
    ;
}
?>