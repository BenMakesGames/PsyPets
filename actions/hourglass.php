<?php
if($okay_to_be_here !== true)
  exit();

$hours = 1;

$current_hours = floor(($now - $house['lasthour']) / (60 * 60));
$new_hours = $current_hours + $hours;

if($current_hours < 50)
{
  if($_GET['action'] == 'totally')
  {
    delete_inventory_byid($this_inventory['idnum']);

    $command = 'UPDATE monster_houses SET lasthour=lasthour-' . ($hours * 60 * 60) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'unleashing the sands of time');

    $AGAIN_WITH_ANOTHER = true;

    echo '<p><strong><em>It is done!</em></strong></p>';

    $command = 'UPDATE monster_inventory SET itemname=\'Small Blue Box\',message=\'Got closed in the wake of a space-time anomaly.  Odd!\' WHERE user=' . quote_smart($user['user']) . ' AND (location LIKE \'home%\' OR location=' . quote_smart($this_inventory['location']) . ') AND itemname IN (\'Monkey-in-a-Box\', \'Weasel-in-a-Box\')';
    $database->FetchNone($command, 'unwinding monkeys and weasels in boxes');
  }
  else
  {
    echo '<p>Using the ' . $this_inventory['itemname'] . ' will give you ' . $hours . ' more hours for your house';

    if($new_hours > 50)
      echo '.  Since you already have ' . $current_hours . ' hours waiting, and the maximum you can have is 50, you\'ll be wasting ' . ($newhours - 50) . ' hours!';
    else
      echo ' (bringing you up to ' . $new_hours . ').';

    echo '
      </p>
      <p>The ' . $this_inventory['itemname'] . ' will, of course, be consumed in the process.</p>
      <p>Knowing all this... <em>will you unleash <strong>the sands of time!?!?</strong></em></p>
      <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=totally">You sound so enthusiastic, how can I refuse?</a></li></ul>
    ';
  }
}
else
  echo '<p>You already have 50 hours waiting - the maximum allowed!  Using the ' . $this_inventory['itemname'] . ' now would only be a waste.</p>';
?>
