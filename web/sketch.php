<?php
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';

$sketchid = (int)$_GET['id'];

$command = 'SELECT timestamp,data,datalength FROM psypets_store_portraits WHERE idnum=' . $sketchid . ' LIMIT 1';
$data = $database->FetchSingle($command, 'fetching data');

if($data === false)
{
  header('Location: ./gfx/shim.png');
  exit();
}

header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $data['timestamp']) . ' GMT');
header('Etag: ' . md5($data['data']));
header('Content-length: ' . $data['datalength']);
header('Content-type: image/png');
echo $data['data'];
?>
