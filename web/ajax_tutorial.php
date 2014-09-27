<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/questlib.php';

$tutorial = (int)$_POST['tutorial'];

switch($tutorial)
{
  case 1: $name = 'tutorial: my house'; break;
  case 2: $name = 'tutorial: my store'; break;
  default: die();
}

add_quest_value($user['idnum'], $name, 1);