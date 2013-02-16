<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$invisible = 'yes';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';

$npc = array(
  'graphic' => 'npcs/receptionist.png',
  'width' => 350,
  'height' => 275,
  'name' => 'Claire Silloway',
);

$npc['dialog'] = '
  <p>The requested page was not found!</p>
  <ul>
   <li><p>If a link within ' . $SETTINGS['site_name'] . ' lead you to this page, that\'s almost certainly an error!  <a href="/writemail.php?sendto=' . $SETTINGS['author_resident_name'] . '">Let ' . $SETTINGS['author_resident_name'] . ' know, so he can fix it up</a> :)</p></li>
   <li><p>If a link from somewhere else entirely lead you here, it might be nice to let that site\'s administrator or editor know that their links are incorrect or out of date.</p></li>
  </ul>
';

$redirect_url = $_SERVER['SCRIPT_URL'] ? $_SERVER['SCRIPT_URL'] : $_SERVER['REQUEST_URI'];

$database->FetchNone('
  UPDATE psypets_404_log
  SET count=count+1,lastlog=' . time() . '
  WHERE url=' . quote_smart($redirect_url) . '
  LIMIT 1
');

if($database->AffectedRows() == 0)
{
  $database->FetchNone('
    INSERT INTO psypets_404_log
    (url, count, lastlog)
    VALUES
    (' . quote_smart($redirect_url) . ', 1, ' . time() . ')
  ');
}

//include 'commons/html.php';
?>
<!DOCTYPE html>
<html>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza &gt; Search</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<h4>Four-oh-four!</h4>
<?php
require WEB_ROOT . '/views/_template/npc.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
