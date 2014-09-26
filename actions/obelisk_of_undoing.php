<?php
if($okay_to_be_here !== true)
  exit();

$hours = (int)$action_info[2];

$current_hours = floor(($now - $house['lasthour']) / (60 * 60));

if($current_hours > 0)
{
  $step = 1;

  if($_POST['action'] == 'Rewind!')
  {
    $hours = (int)$_POST['hours'];
    
    if($hours < 1)
      echo '<p class="failure">Er... how many hours?</p>';
    else if($hours > $current_hours)
      echo '<p class="failure">You don\'t have that many hours to undo!</p>';
    else
    {
      delete_inventory_byid($this_inventory['idnum']);

      $command = 'UPDATE monster_houses SET lasthour=lasthour+' . ($hours * 60 * 60) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'unleashing the sands of time');
    
      $AGAIN_WITH_ANOTHER = true;

      require_once 'commons/statlib.php';

      record_stat($user['idnum'], 'Undid ' . $hours . ' House Hours', 1);

      $step = 2;
    }
  }

  if($step == 1)
  {
    echo
      '<p>Using the ' . $this_inventory['itemname'] . ' allows you to <em>remove</em> hours from your house!</p>',
      '<p>You currently have ' . $current_hours . ' hour' . ($current_hours != 1 ? 's' : '') . ' waiting.  How many would you like to undo?</p>',
      '<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">',
      '<p><input type="text" name="hours" size="2" maxlength="' . strlen($current_hours) . '" /> <input type="submit" name="action" value="Rewind!" /></p>',
      '</form>'
    ;
  }
  else
    echo '<p><strong><em>It is done!</em></strong></p>';

}
else
  echo '<p>Your house does not have any hours to undo!</p>';
?>