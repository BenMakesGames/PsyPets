<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$command = 'SELECT residentid FROM psypets_group_invites';
$invites = $database->FetchMultiple($command, 'invites');

$idnums = array();

foreach($invites as $invite)
  $idnums[] = $invite['residentid'];

if(count($idnums) == 0)
  die('No invites!');

$command = 'UPDATE monster_users SET newgroupinvite=\'yes\' WHERE idnum IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums);
$database->FetchNone($command, 'updating users');

echo $database->AffectedRows() . ' residents were updated';
?>
