<?php
// thingamadig
if($okay_to_be_here !== true)
  exit();

$command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Heart Key\' AND location=' . quote_smart($this_inventory['location']) . ' LIMIT 1';
$key = $database->FetchSingle($command, 'fetching key');

$ready = true;

if($_GET['action'] == 'open')
{
  if($key !== false)
  {
    delete_inventory_byid($key['idnum']);
    delete_inventory_byid($this_inventory['idnum']);

    $rewards = array('Confetti', 'Figurine #E', 'Turkey Plushy', 'Rainbow-Dyed Egg');

    for($x = 0; $x < 2; ++$x)
    {
      $item = $rewards[array_rand($rewards)];
      $items[] = $item;

      add_inventory($user['user'], $this_inventory['creator'], $item, 'Found inside a ' . $this_inventory['itemname'], $this_inventory['location']);
    }

    echo '
      <p>The ' . $this_inventory['itemname'] . ' falls to pieces, revealing ' . $items[0] . ' and ' . $items[1] . '!</p>
      <p>(I guess that means it worked!)</p>
    ';

    $AGAIN_WITH_ANOTHER = true;
    $ready = false;
  }
}

if($ready)
{
  echo '<p>Examining the ' . $this_inventory['itemname'] . ' reveals a small, heart-shaped key hole...</p>';

  if($key === false)
    echo '<p>You don\'t have anything around that would fit, unfortunately.</p>';
  else
    echo '<p>Oh!  This Heart Key would probably fit!</p><ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=open">Let\'s give it a try, then!</a></li></ul>';
}
?>
