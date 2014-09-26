<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_inventory SET itemname='Cauldron', message='', message2='' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1");

$money_gained = (rand() % 6 + 7) * 100;
give_money($user, $money_gained, "Looted from a Pot of Gold");
echo '<p>You empty the pot on the floor and begin to count.  There was ' . $money_gained . '<span class="money">m</span> inside!</p>';
?>
