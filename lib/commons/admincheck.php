<?php
$allowed_ips = array(
  '192.168.245.1',
);

if(!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips))
{
  header('Location: /404/');
  exit();
}

$IGNORE_MAINTENANCE = true;
