<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

if(rand(1, 9) == 1)
{
  echo "You unthread the Skull as carefully as you can, but somehow manage to slip up anyway, sending it to the hard floor where it makes an awful cracking noise";

  if(count($userpets) > 0)
  {
    echo " that bothers even your pet" . (count($userpets) != 1 ? "s" : "");

    for($j = 0; $j < count($userpets); ++$j)
    {
      lose_stat($userpets[$j], 'love', dice_roll(1, 3));
      lose_stat($userpets[$j], 'safety', dice_roll(1, 4));
      save_pet($userpets[$j], array('love', 'safety'));
    }
  }

  echo ".";
}
else
{
  add_inventory($user['user'], '', 'Skull', 'Recovered from a Skull Amulet', $this_inventory['location']);
  echo 'You carefully unthread the Skull... success!';
}

add_inventory($user['user'], '', 'Stringy Rope', 'Recovered from a Skull Amulet', $this_inventory['location']);
?>
