<?php
$user['user'] = 'telkoth';
$auction_owner['user'] = 'yigh12';

$match_name = '/' . preg_replace('/[0-9]+/', '[0-9]*', $user['user']) . '/';

echo $match_name . ' <-- eh?<br />';

if(preg_match($match_name, $auction_owner['user']) > 0)
  echo 'MATCH!!';

?>
