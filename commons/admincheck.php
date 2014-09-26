<?php
$allowed_ips = array(
  '184.6.30.60',
);

if(!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips))
{
  header('Location: /404/');
  exit();
}

$_GET['maintenance'] = 'no';
