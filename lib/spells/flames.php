<?php
if($yes_yes_that_is_fine !== true)
  exit();

require_once 'commons/fireplacelib.php';

$FINISHED_CASTING = true;

$fireplace = get_fireplace_byuser($user['idnum'], $user['locid']);

if($fireplace === false)
{
  echo '<p>There is a puff of foul-smelling smoke... and nothing more.</p>';
}
else
{
  $command = 'UPDATE psypets_fireplaces SET fire=fire+12 WHERE idnum=' . $fireplace['idnum'] . ' LIMIT 1';
  fetch_none($command, 'feeding fireplace');

  log_fireplace_event($now, $user['idnum'], 'You magic\'d your fireplace.');

  echo '<p>Your fireplace fire flares a little at the addition of... magics... &gt;_&gt;</p>';
}
?>
