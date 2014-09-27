<?php
require_once 'commons/dbconnect.php';

$command = 'SELECT COUNT(idnum) AS qty,mazeloc FROM `monster_users` WHERE mazeloc>0 GROUP BY mazeloc';
$data = $database->FetchMultiple($command, 'fetching maze locations of players');

foreach($data as $count)
{
  $command = 'UPDATE psypets_maze SET players=' . $count['qty'] . ' WHERE idnum=' . $count['mazeloc'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating count');
}
?>
Done!
