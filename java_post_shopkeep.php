<?php
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';

$userid = (int)$_POST['userid'];
$sessionid = (int)$_POST['sessionid'];

$command = 'SELECT idnum FROM monster_users WHERE idnum=' . $userid . ' AND sessionid=' . $sessionid . ' LIMIT 1';
$user = $database->FetchSingle($command, 'fetching user');

if($user === false)
  die('You are no longer logged in!  Please reload the page and log in.');
else
{
  $filename = $_FILES['shopkeep']['tmp_name'];
  $filesize = $_FILES['shopkeep']['size'];

  $file = fopen($filename, 'r');
  $content = addslashes(fread($file, $filesize));
  fclose($file);

  $header = substr($content, 0, 8);

  if(IsPNG($header))
  {
    $command = 'DELETE FROM psypets_store_portraits WHERE userid=' . $userid . ' AND use_for_store=\'yes\' LIMIT 1';
    $database->FetchNone($command, 'deleting, if any');
  
    $command = '
      INSERT INTO psypets_store_portraits
      (userid, use_for_store, timestamp, datalength, data)
      VALUES
      (
        ' . $userid . ',
        \'yes\',
        ' . time() . ',
        ' . $filesize . ',
        \'' . $content . '\'
      )
    ';
    $database->FetchNone($command, 'creating');

    echo 'SUCCESS' . "\n" . $filesize . "\n" . $database->InsertID();
  }
  else
    echo 'FAILED' . "\n" . 'Not a PNG.';
}

function IsPNG($header)
{
  return(
    ord($header{0}) == 0x89 &&
    ord($header{1}) == 0x50 &&
    ord($header{2}) == 0x4e &&
    ord($header{3}) == 0x47 &&
    ord($header{4}) == 0x0d &&
    ord($header{5}) == 0x0a &&
    ord($header{6}) == 0x1a &&
    ord($header{7}) == 0x0a
  );
}
?>
