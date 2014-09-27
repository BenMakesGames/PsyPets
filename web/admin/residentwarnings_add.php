<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/warninglib.php';

if($admin['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$resident = get_user_bydisplay($_GET['resident']);

if($resident === false)
{
  header('Location: /admin/resident.php');
  exit();
}

if($resident['is_npc'] == 'yes')
{
  header('Location: /admin/resident.php');
  exit();
}

add_warning($resident, $_POST['adminnote']);

header('Location: /admin/residentwarnings.php?resident=' . link_safe($resident['display']));
?>
