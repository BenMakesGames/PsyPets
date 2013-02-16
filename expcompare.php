<?php
require_once 'commons/petlib.php';

$exp = 0;
for($x = 0; $x < 20; ++$x)
{
  $exp += level_exp($x);
}
?>
<p>One stat from 0 to 23: <?= $exp ?></p>
<?php

$exp = 0;
for($x = 0; $x < 6; ++$x)
{
  $exp += level_stat_exp($x) * 3;
}
?>
<p>Three stats from 0 to 7: <?= $exp ?></p>