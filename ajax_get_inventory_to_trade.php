<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/inventory_ajax.php';
require_once 'commons/formatting.php';

if($user['license'] != 'yes')
{
  die('<p class="failure">You do not have a License to Commerce!</p>');
}

echo '<h5>Items to Trade</h5>';
select_multiple_from_storage($user['user']);
?>
