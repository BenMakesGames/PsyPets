<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/adventurelib.php';

$adventure = get_adventure($user['idnum']);

if($adventure !== false && $adventure['progress'] >= $adventure['difficulty'])
{
  if($adventure['prize'] != '')
	{
		if(check_adventure_scramble($adventure, trim($_POST['word'])))
		{
			claim_prize($adventure);
		}
		else
		{
			header('Location: /adventure/?dialog=wrongword');
			exit();
		}
	}
}

header('Location: /adventure/');
?>