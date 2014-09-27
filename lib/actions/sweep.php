<?php
if($okay_to_be_here !== true)
  exit();

if($user['cornergraphic'] == 'cobweb.png')
{
  if($_GET['step'] == 2)
  {
    $command = 'UPDATE monster_users SET cornergraphic=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);

    echo 'You sweep up the house.  Especially that cobweb in the corner.';
    
    add_inventory($user['user'], '', 'Cobweb', '', $this_inventory['location']);
  }
  else
  {
?>
Warning!  You'll really give your house a good cleaning with this <?= $this_inventory['itemname'] ?>!  If there were, like, cobwebs on your profile that for some reason you wanted to keep (just as an example), maybe this is not really the best time to do some serious cleaning...</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">It's dusty in here!  Let's clean like crazy!</a></li>
</ul>
<?php
  }
}
else
  echo 'You sweep up the house.</p><p>*cough*</p><p>Dust.';
?>
