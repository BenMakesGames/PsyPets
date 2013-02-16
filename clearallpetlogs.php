<?php
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/petlib.php';
require_once 'commons/rpgfunctions.php';

require_once 'libraries/db_messages.php';

if(count($userpets) > 0)
{
  foreach($userpets as $pet)
    clear_logged_events_byuser_bypet($user['idnum'], $pet['idnum']);
}

add_db_message($user['idnum'], FLASH_MESSAGE_META_GAME, '<span class="success">All pet logs have been cleared.</span>');

header('Location: /myhouse.php');
?>
