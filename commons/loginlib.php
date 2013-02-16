<?php
function log_bad_login($ip, $user, $account_exists)
{
  $user_exists = ($account_exists ? 'yes' : 'no');

  $now = time();
  
  $command = 'INSERT INTO psypets_failedlogins (timestamp, ip, username, user_exists) VALUES ' .
             '(' . $now . ', ' . quote_smart($ip) . ', ' . quote_smart($user) . ', ' .
             quote_smart($user_exists) . ')';
  fetch_none($command, 'logging bad login attempt');
}
?>
