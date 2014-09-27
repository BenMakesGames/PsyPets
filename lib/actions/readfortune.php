<?php
if($okay_to_be_here !== true)
  exit();

$fortunes = file("fortunes.txt");
$fortune = trim($fortunes[$this_inventory['idnum'] % count($fortunes)]);
?>
<p>"<?= $fortune ?>"</p>
<?php
if($this_inventory['idnum'] % 31 == 1)
  echo '<p>... in bed.</p>';
?>