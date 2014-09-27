<?php
require_once 'commons/utility.php';

$new_method_time = microtime(true);

for($x = 0; $x < 3600; ++$x)
{
  $tries = mt_rand(1, 2);
  
  $todo['nothing'] = mt_rand(10, 50);
  $todo['smithing'] = mt_rand(10, 50);
  $todo['eat'] = mt_rand(10, 50);
  $todo['hacking'] = mt_rand(10, 50);

  $keys = array_keys($todo, max($todo));
  $action = $keys[array_rand($keys)];
}

$new_method_time = microtime(true) - $new_method_time;

$old_method_time = microtime(true);

for($x = 0; $x < 3600; ++$x)
{
  $todo['nothing'] = mt_rand(10, 50);
  $todo['smithing'] = mt_rand(10, 50);
  $todo['eat'] = mt_rand(10, 50);
  $todo['hacking'] = mt_rand(10, 50);

  $todo = ashuffle($todo);
  arsort($todo);

  reset($todo);
  $action = key($todo);
}

$old_method_time = microtime(true) - $old_method_time;
?>
<p>Old method: <?= round($old_method_time, 4) ?>s</p>
<p>New method: <?= round($new_method_time, 4) ?>s</p>
