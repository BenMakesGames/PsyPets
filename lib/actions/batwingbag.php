<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
?>
Opening the bag reveals 5 Bat Wings!
<?php
  add_inventory_quantity($user['user'], '', 'Bat Wing', 'Found inside a Bat Wing Bag', $this_inventory['location'], 5);
  delete_inventory_byid($this_inventory['idnum']);
}
else
{
?>
Do you want to open this bag, and claim the 5 Bat Wings inside? (Doing so will destroy the bag.)</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;step=2">Yes!</a></li>
</ul>
<?php
}
?>
