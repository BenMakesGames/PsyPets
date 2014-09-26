<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$money = rand(10, 1000);

if(rand(1, 10) == 0)
  $money *= rand(7, 15);

give_money($user, $money, 'Found in a Moneys Pouch');

$AGAIN_WITH_ANOTHER = true;
?>
<p>The pouch contains <?= $money ?> worth of moneys.</p>
