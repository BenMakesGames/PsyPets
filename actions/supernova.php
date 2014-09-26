<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$n = rand(4, 8);

for($i = 0; $i < $n; ++$i)
{
  if(mt_rand(1, 100) == 1)
    $itemname = 'Platinum';
  else if(mt_rand(1, 50) == 1)
    $itemname = 'Radioactive Material';
  else if(mt_rand(1, 10) == 1)
    $itemname = 'Dark Matter';
  else if(mt_rand(1, 2) == 1)
    $itemname = 'Rubble';
  else if(mt_rand(1, 2) == 1)
    $itemname = 'Small Rock';
  else if(mt_rand(1, 2) == 1)
    $itemname = 'Large Rock';
  else
    $itemname = 'Really Enormously Tremendous Rock';

  $itemlist[] = $itemname;
  add_inventory($user['user'], '', $itemname, 'Recovered from a ' . $this_item['itemname'], $this_inventory['location']);
}

delete_inventory_byid($this_inventory['idnum']);
?>
<i><b style="font-size: 4em;"><?php
$word = 'KASPLODE!!';

for($x = 0; $x < strlen($word); ++$x)
  echo '<span style="color:' . sprintf('#%02X%02X%02X', mt_rand(96, 224), mt_rand(96, 224), mt_rand(96, 224)) . ';">' . $word{$x} . '</span>';
?></b></i>
