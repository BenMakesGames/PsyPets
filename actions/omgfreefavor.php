<?php
if($okay_to_be_here !== true)
  exit();

// use omgfreefavor_test.php to see information on average favor, etc.

if($_GET['action'] == 'use')
{
  require_once 'commons/favorlib.php';
  require_once 'commons/statlib.php';
  
  $craaazy_favor = mt_rand(10, mt_rand(38, mt_rand(83, 200)));
  
  $quips = array(
    'Is that a lot, or something?',
    'Were you surprised?',
    'Surprise!!',
    'My momma always said, "life was like a box of chocolates."',
    'Better than a prosthetic tentacle!',
  );

  if(substr($user['birthday'], 5) == date('m-d'))
    $equips[] = 'Happy birthday!';
  else
    $equips[] = 'A very merry unbirthday to you!';

  delete_inventory_byid($this_inventory['idnum']);
  
  echo '<p>You receive ', $craaazy_favor, ' Favor!  (', $quips[array_rand($quips)], ')</p>';

  credit_favor($user, $craaazy_favor, 'Received from a ' . $this_inventory['itemname']);
  record_stat($user['idnum'], 'Favor Received from Magic Vouchers', $craaazy_favor);
  
  $AGAIN_WITH_ANOTHER = true;
}
else
{
?>
<p>This magical ticket promises you Favor... but it doesn't say exactly how much.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&action=use">Use it!  Uuuuuse iiiiit!</a></li>
</ul>
<?php
}
?>
