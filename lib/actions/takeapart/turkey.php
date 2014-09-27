<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

add_inventory_quantity($user['user'], '', 'Turkey', '', $this_inventory['location'], 5);

echo '<p>You cut apart the Whole Turkey, yielding five pieces of Turkey!</p>';

if(mt_rand(1, 20) == 1)
{
  if(mt_rand(1, 2) == 1)
  {
    $item = 'Duck Plushy';
    $item_descript = 'Duck Plushy';
  }
  else
  {
    $item = 'Duck Plushy Covered in Coconut Juice';
    $item_descript = 'Duck Plushy... Covered in Coconut Juice';
  }

  add_inventory($user['user'], '', $item, '', $this_inventory['location']);

  echo '
    <p>And wait!  What\'s this!?  There was a Duck inside!</p>
    <p>OMG, IT IS ACTUALLY A TURDUCKEN!!</p>
    <p>Oh, no, sorry, false alarm: it\'s just a ' . $item_descript . '.</p>
    <p>(Wait, isn\'t that even weirder...? &gt;_&gt;)</p>
  ';
}
?>
