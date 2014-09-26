<?php
if($okay_to_be_here !== true)
  exit();

$deleted = false;

if($user['meteor'] == 'yes')
{
  if($_GET['action'] == 'dig' || $this_item['custom'] != 'no')
  {
    $command = 'UPDATE monster_users SET meteor=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'fixing up profile');

    if($this_item['custom'] != 'no')
    {
      echo '<p>All cleaned up!</p>';
    }
    else
    {
      $break_chance = 60;

      if($this_inventory['itemname'] == 'Ghostly Shovel')
        $reward = 'Ghostly Moldavite';
      else
        $reward = 'Moldavite';

      if($this_inventory['itemname'] == 'Prospector\'s Shovel')
        $break_chance = 20;

      echo '<p>All cleaned up!</p><p>And look what was found lying around: ' . $reward . '!</p>';

      add_inventory($user['user'], '', $reward, 'Unearthed by a meteor shower', $this_inventory['location']);

      if(mt_rand(1, 100) <= $break_chance)
      {
        echo '<p>Aw, nuts, the ' . $this_inventory['itemname'] . ' broke!</p>';
        delete_inventory_byid($this_inventory['idnum']);
      }
    }
  }
  else
  {
?>
<p>Your profile's messed up from a meteor shower.  Do you want to use this <?= $this_inventory['itemname'] ?> to fix things up?  You'll almost certainly break it in the process.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&action=dig">It's worth the risk! (Besides, you might find something cool while digging around.)</a></li>
</ul>
<?php
  }
}
else
  echo '<p>There isn\'t really anything you could do with it right now...</p>';
?>
