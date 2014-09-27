<?php
if($okay_to_be_here !== true)
  exit();

srand($this_inventory["idnum"]);
$fortunes = file("valentines.txt");
$fortune = trim($fortunes[array_rand($fortunes)]);
?>
<p>"<?= $fortune ?>"</p>
