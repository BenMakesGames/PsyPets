<?php
$username = 'hello_there\'s';

$username = htmlentities(strtolower($username), ENT_QUOTES, 'UTF-8');
$username = str_replace('&#039;', '\\\'', $username); // Allow apostrophes (Escape them though)

echo 'username: ' . $username;
?>
