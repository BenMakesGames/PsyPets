<?php
if($_GET['password'] == 'wh33')
{
  require_once 'commons/dbconnect.php';

  $database->FetchNone('UPDATE monster_users SET sessionid=0');

  echo '<p>Everyone\'s been logged out!</p>';
}
?>
