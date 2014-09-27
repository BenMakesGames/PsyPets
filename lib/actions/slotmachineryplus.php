<?php
if($okay_to_be_here !== true)
  exit();

$graphics = array('gfx/slots/berries.png', 'gfx/slots/sourlime.png', 'gfx/slots/ghost.png',
  'gfx/slots/desikh.png', 'gfx/slots/giamond.png');

$payout = .9;

if($_GET['step'] == 2)
{
  $bid = (int)$_POST['bid'];
  if($bid < 10 || $bid > $user['money'])
    $step = 1;
  else
    $step = 2;
}
else
  $step = 1;

echo '<img src="gfx/lights.gif" /><img src="gfx/lights.gif" /><img src="gfx/lights.gif" /><br />';

if($step == 2)
{
  $prize_chances = array(0 => .16, 1 => .11, 2 => .08, 3 => .04, 4 => .01);
  $prize_payouts = array(0, 0, 0, 0, 0);

  $k = count($prize_chances);

  foreach($prize_chances as $index=>$chance)
  {
    $prize = ($payout / $chance) / count($prize_chances);
    $prize_payouts[$index] = $prize;
  }

  $prize_chances_100 = array(0, 0, 0, 0, 0);

  $chance_100 = 0;
  foreach($prize_chances as $index=>$chance)
  {
    $chance_100 += $chance * 100;
    if($chance_100 > 100)
      die('Winning chances total above 100%.');

    $prize_chances_100[$index] = $chance_100;
  }

  $money_adjust = -$bid;

  $result = mt_rand(1, 100);
  $winnings = 0;

  foreach($prize_chances_100 as $index=>$chance_100)
  {
    if($result <= $chance_100)
    {
      $winnings = floor($prize_payouts[$index] * $bid);
      $graphic = $graphics[$index];
      break;
    }
  }

  echo '<img src="gfx/slots/roller_' . mt_rand(1, 4) . '.gif" id="graphic1" /><img src="gfx/slots/roller_' . mt_rand(1, 4) . '.gif" id="graphic2" /><img src="gfx/slots/roller_' . mt_rand(1, 4) . '.gif" id="graphic3" /><br />' .
       '<img src="gfx/lights_r.gif" /><img src="gfx/lights_r.gif" /><img src="gfx/lights_r.gif" /></p>' .
       '<div id="message">Good luck!</div>';

  if($winnings > 0)
  {
    $message = 'You won ' . $winnings . ' moneys!';
    $money_adjust += $winnings;
    $graphic1 = $graphic;
    $graphic2 = $graphic;
    $graphic3 = $graphic;
  }
  else
  {
    do
    {
      $graphic1 = $graphics[array_rand($graphics)];
      $graphic2 = $graphics[array_rand($graphics)];
      $graphic3 = $graphics[array_rand($graphics)];
    } while($graphic1 == $graphic2 && $graphic2 == $graphic3);

    // near-miss programming :)
    if($graphic1 != $graphic2 && $graphic1 != $graphic3 && $graphic2 != $graphic3 && mt_rand(1, 3) == 1)
      $graphic2 = $graphic1;

    $message = 'You did not win anything.';
  }

  $user['money'] += $money_adjust;
  if($money_adjust > 0)
    $money_adjust = '+' . $money_adjust;

  $command = 'UPDATE monster_users SET money=money' . $money_adjust . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating player money');

  $command = 'UPDATE psypets_slots SET money=money+' . floor((1 - $payout) * $bid);
  $database->FetchNone($command, 'updating slot machine money');
?>
<script type="text/javascript">
document.getElementById('moneysonhand').innerHTML='<?= $user['money'] ?>';

setTimeout('stop_roller_1();', 1000);
setTimeout('stop_roller_2();', 2000);
setTimeout('stop_roller_3();', 4000);

function stop_roller_1()
{
  document.getElementById('graphic1').src = '<?= $graphic1 ?>';
}

function stop_roller_2()
{
  document.getElementById('graphic2').src = '<?= $graphic2 ?>';
}

function stop_roller_3()
{
  document.getElementById('graphic3').src = '<?= $graphic3 ?>';
  document.getElementById('message').innerHTML = '<?= $message ?>';
}
</script>
<?php

  echo '<hr />';


}
else
{
  $graphic1 = $graphics[array_rand($graphics)];
  $graphic2 = $graphics[array_rand($graphics)];
  $graphic3 = $graphics[array_rand($graphics)];
    echo '<img src="' . $graphic1 . '" /><img src="' . $graphic2 . '" /><img src="' . $graphic3 . '" /><br />' .
         '<img src="gfx/lights_r.gif" /><img src="gfx/lights_r.gif" /><img src="gfx/lights_r.gif" /></p>' .
         '<p>Hello!<hr />';
}
?>
<i>How much money will you bet?  (Minimum of 10 moneys.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2" method="POST">
<p><input name="bid" value="<?= $bid ?>" /> <input type="submit" value="Pull!" /></p>
</form>
