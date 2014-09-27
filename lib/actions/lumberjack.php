<?php
if($okay_to_be_here !== true)
  exit();

$coal = $database->FetchSingle('
	SELECT idnum
	FROM monster_inventory
	WHERE
		user=' . $database->Quote($user['user']) . '
		AND itemname=\'Coal\'
		AND location=' . $database->Quote($this_inventory['location']) . '
');

if($coal !== false)
{
  if($_GET['step'] == 2)
  {
    delete_inventory_byid($coal['idnum']);
  
    $wood = 0;

    $logs = $database->FetchMultiple('
			SELECT idnum
			FROM monster_inventory
			WHERE
				user=' . $database->Quote($user['user']) . '
				AND itemname=\'Log\'
				AND location=' . $database->Quote($this_inventory['location']) . '
		');

    $num_logs = count($logs);

    if($num_logs == 0)
      echo '<p>The ' . $this_inventory['itemname'] . ' zips around the room for a while, whirring and sputtering, but finding no Logs, shuts down.</p>';
    else
    {
      $wood = 0;
    
      foreach($logs as $log)
      {
        $idnum = $log['idnum'];

        delete_inventory_byid($log['idnum']);

        $wood += mt_rand(1, 4);
      }

      for($x = 0; $x < $wood; ++$x)
        add_inventory_cached($user['user'], '', 'Wood', 'Chopped by the ' . $this_inventory['itemname'], $this_inventory['location']);

      echo '<p>The ' . $this_inventory['itemname'] . ' zips around the room, whirring, sputtering and flailing its axes.  When it\'s finally done ' . $num_logs . ' log' . ($num_logs != 1 ? 's' : '') . ' have been chopped into ' . $wood . ' plank' . ($wood != 0 ? 's' : '') . ' of Wood.</p>';

      while($num_logs > 0)
      {
        if(mt_rand(1, 100) < min(95, $num_logs))
          $amber++;
      
        $num_logs -= 100;
      }

      if($amber > 0)
      {
        for($x = 0; $x < $amber; ++$x)
          add_inventory_cached($user['user'], '', 'Amber', 'Revealed by the ' . $this_inventory['itemname'], $this_inventory['location']);

        echo '<p>Oh, and look!  ' . $amber . ' piece' . ($amber != 1 ? 's' : '') . ' of Amber!</p>';
      }
      
      process_cached_inventory();
      $RECOUNT_INVENTORY = true;
    }
  }
  else
  {
?>
<p>The <?= $this_inventory['itemname'] ?> needs Coal to run.</p>
<ul><li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Give it a piece of Coal</a></li></ul>
<?php
  }
}
else
  echo '<p>The ' . $this_inventory['itemname'] . ' needs Coal to run, but there isn\'t any available in this room.</p>';
?>
