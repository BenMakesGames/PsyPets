<?php
function add_dreidel_log($userid, $roll, $amount)
{
  $command = 'INSERT INTO psypets_dreidel_logs (timestamp, userid, result, potchange) VALUES ' .
    '(' . time() . ', ' . $userid . ', \'' . $roll . '\', ' . $amount . ')';
  fetch_none($command, 'logging dreidel roll');
}

function get_dreidel_logs($page)
{
  $command = 'SELECT * FROM psypets_dreidel_logs ORDER BY timestamp DESC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching dreidel logs');
}

function do_dreidel_join($userid)
{
  $command = 'UPDATE psypets_questvalues SET value=value+1 WHERE userid=0 AND name=\'dreidel\' LIMIT 1';
  fetch_none($command, 'adding 1 money to dreidel pot');

  add_dreidel_log($userid, '(joined game)', 1);
}

function do_dreidel_shin($userid)
{
  $command = 'UPDATE psypets_questvalues SET value=value+1 WHERE userid=0 AND name=\'dreidel\' LIMIT 1';
  fetch_none($command, 'adding 1 money to dreidel pot');

  add_dreidel_log($userid, 'Shin', 1);
}

function do_dreidel_nun($userid)
{
  add_dreidel_log($userid, 'Nun', 0);
}

function do_dreidel_gimel($userid, $amount)
{
  $command = 'UPDATE psypets_questvalues SET value=value-' . $amount . ' WHERE userid=0 AND name=\'dreidel\' LIMIT 1';
  fetch_none($command, 'removing all money from the dreidel pot');

  add_dreidel_log($userid, 'Gimel', -$amount);
}

function do_dreidel_hay($userid, $amount)
{
  $command = 'UPDATE psypets_questvalues SET value=value-' . $amount . ' WHERE userid=0 AND name=\'dreidel\' LIMIT 1';
  fetch_none($command, 'removing half of the money from the dreidel pot');

  add_dreidel_log($userid, 'Hay', -$amount);
}
?>
