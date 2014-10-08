<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$cave_quest = get_quest_value($user['idnum'], 'hidden cave quest');

if($cave_quest['value'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE itemname=\'Smoke Bombs\' AND user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\'';
  $data = $database->FetchSingle($command, 'fetching smoke bombs from home :P');

  if($_GET['step'] == 2 && $data['c'] > 0)
  {
    $cave_quest['value'] = 2;

    update_quest_value($cave_quest['idnum'], $cave_quest['value']);

    $message = 'You set down the bomb, then run away to a safe distance.  After the explosion goes off and the dust settles, you see that the crack has been replaced with a sizable opening...';

    $command = 'DELETE FROM monster_inventory WHERE itemname=\'Smoke Bombs\' AND user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' LIMIT 1';
    $database->FetchNone($command, 'deleting a smoke bomb');
  }
}

if($cave_quest['value'] == 1)
{
?>
<center><img src="gfx/cliff_edge.png" alt="" /></center></p>
<p>The map leads you along a path, to the face of a cliff wall.  It's scarred with an obvious-looking crack.</p>
<?php
  if($data['c'] > 0)
  {
?>
<table><tr><td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/bomb_smoke.png" alt="" /></td><td><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Set it up the bomb!</a></td><td>(<?= $data['c'] ?> in your house)</td></tr></table>
<?php
  }
  else
    echo '<p>Unfortunately, you can\'t think of anything you have back home that could help you further.';
}
else if($cave_quest['value'] == 2)
{
?>
<center><img src="gfx/cliff_edge_exploded.png" alt="" /></center></p>
<p><?php if($message) echo $message; else { ?>The map leads you along a path, to the face of a cliff wall.  A hole has been blasted into the side, revealing a tunnel that leads underground...<?php } ?></p>
<ul>
 <li><a href="mysteriousshop.php">Go inside</a></li>
</ul>
<?php
}
?>
