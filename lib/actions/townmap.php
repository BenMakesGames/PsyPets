<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/utility.php';
require_once 'commons/grouplib.php';
require_once 'commons/tiles.php';

$info = take_apart(';', $this_inventory['data']);
$groupid = $info[0];
$timestamp = $info[1];
$data = $info[2];

$group = get_group_byid($groupid);

echo $group['name'] . ' Town Map, copied on ' . local_time($timestamp, $user['timezone'], $user['daylightsavings']) . '</p>';

echo _map_from_data($groupid, false, $data);
?>
