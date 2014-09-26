<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$quest = get_quest_value($user['idnum'], 'totem quest');

if($quest['value'] == 1)
  update_quest_value($quest['idnum'], 2);

echo '
  <p><img src="/gfx/books/silly-totem-markings.png" width="64" height="48" alt="" /></p>
';
?>
