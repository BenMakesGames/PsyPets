<?php
function consider_new_trade_flag($userid)
{
  $command = 'SELECT tradeid AS c FROM monster_trades WHERE ' .
             '(userid1=' . (int)$userid . ' AND step=2) ' .
             'OR ' .
             '(userid2=' . (int)$userid . ' AND step=1) ' .
             'LIMIT 1';
  $trade = $GLOBALS['database']->FetchSingle($command);

  if($trade === false)
    $command = 'UPDATE monster_users SET newtrade=\'no\' WHERE idnum=' . (int)$userid . ' LIMIT 1';
  else
    $command = 'UPDATE monster_users SET newtrade=\'yes\' WHERE idnum=' . (int)$userid . ' LIMIT 1';

  $GLOBALS['database']->FetchNone($command);
}

function set_new_trade_flag($userid)
{
  $GLOBALS['database']->FetchNone('UPDATE monster_users SET newtrade=\'yes\' WHERE idnum=' . (int)$userid . ' LIMIT 1');
}
?>
