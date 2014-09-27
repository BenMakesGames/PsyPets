<?php
require_once 'commons/userlib.php';

function get_warnings_byuserid($userid)
{
  $command = 'SELECT * FROM psypets_warninglog WHERE userid=' . $userid . ' ORDER BY idnum DESC';
  return fetch_multiple($command, 'fetching resident\'s warnings');
}

function add_warning($user_object, $adminnote)
{
  global $now;

  $command = 'INSERT INTO psypets_warninglog (userid, timestamp, adminnote) VALUES ' .
             '(' . $user_object['idnum'] . ', ' . $now . ', ' . quote_smart($adminnote) . ')';
  fetch_none($command, 'recording warning');
}
?>
