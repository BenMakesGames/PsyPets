<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$name = trim($_POST['name']);

if(strlen($name) > 0)
{
  $item_count = get_user_museum_count($user['idnum']);

  if($item_count >= 100)
  {
    $command = 'SELECT idnum,name FROM psypets_museum_displays WHERE userid=' . $user['idnum'];
    $displays = fetch_multiple_by($command, 'name', 'fetching displays');

    if(count($displays) < 20)
    {
      if(!array_key_exists($name, $displays))
      {
        $command = 'INSERT INTO psypets_museum_displays (userid, name) VALUES ' .
          '(' . $user['idnum'] . ', ' . quote_smart($name) . ')';
        fetch_none($command, 'creating new museum display');
      }
    }
  }
}

header('Location: /museum/displayeditor.php');
exit();
?>
