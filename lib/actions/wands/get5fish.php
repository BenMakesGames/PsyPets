<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/statlib.php';
  
if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  delete_inventory_byid($this_inventory['idnum']);

  $fish = mt_rand(4, mt_rand(4, 8));
  $aquite = mt_rand(2, 3);
  
  echo '<p>The wand turns into water as you hold it, and pours onto the floor!</p>';
  echo '<p>It pours a bit more than you\'d think possible, carrying with it ' . $fish . ' Fish, which flop on the floor as the last of the water washes away...</p>';

  add_inventory_quantity($user['user'], '', 'Aquite', 'Poured out of a ' . $this_inventory['itemname'], $this_inventory['location'], $aquite);
  add_inventory_quantity($user['user'], '', 'Fish', 'Carried in on a torrent of water', $this_inventory['location'], $fish);

  if(mt_rand(1, 200) == 1)
  {
    echo '<p>Though the wand is no more, you feel something solid in your hand... it\'s an Aquaphobia!</p>';
    add_inventory($user['user'], '', 'Aquaphobia', 'Left over from a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
  else if(mt_rand(1, 10000) == 1)
  {
    echo '<p>Though the wand is no more, you feel something solid in your hand... it\'s a Carafe of Water!</p>';
    add_inventory($user['user'], '', 'Carafe of Water', 'Left over from a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
}
else
{
  echo '
    <p>Will you use the ' . $this_inventory['itemname'] . '?</p>
    <ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;step=2">Believe it!</a></li></ul>
  ';
}
?>
