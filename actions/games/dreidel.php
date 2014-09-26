<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';
require_once 'commons/dreidellib.php';

$dreidel_funds = get_quest_value(0, 'dreidel');
$pot = $dreidel_funds['value'];

$user_dreidel = get_quest_value($user['idnum'], 'dreidel spun');
$user_wallet = get_quest_value($user['idnum'], 'dreidel wallet');

if($user_dreidel === false)
{
  if($_GET['action'] == 'join' && $user['money'] >= 20)
  {
    take_money($user, 20, 'Joined the Dreidel game');
    $user['money'] -= 20;

    do_dreidel_join($user['idnum']);
    $pot++;

    add_quest_value($user['idnum'], 'dreidel spun', 0);
    $user_dreidel = get_quest_value($user['idnum'], 'dreidel spun');

    add_quest_value($user['idnum'], 'dreidel wallet', 19);
    $user_wallet = get_quest_value($user['idnum'], 'dreidel wallet');
  }
  else
  {
    echo '<p>To join the Dreidel game, you must set aside 20<span class="money">m</span> to play with, one of which will be placed directly into the pot.</p>' .
         '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=join">Join (20<span class="money">m</span>)</a></li></ul>';
  }
}

if($user_dreidel !== false && $user_wallet['value'] == 0)
{
  if($_GET['action'] == 'join' && $user['money'] >= 20)
  {
    take_money($user, 20, 'Joined the Dreidel game again');
    $user['money'] -= 20;

    do_dreidel_join($user['idnum']);
    $pot++;

    update_quest_value($user_wallet['idnum'], 19);
    $user_wallet['value'] = 19;
  }
}

if($user_dreidel !== false)
{
  $today = floor($now / (4 * 60 * 60));

  if($_POST['action'] == 'Spin!' && $today != $user_dreidel['value'] && $user_wallet['value'] > 0)
  {
    $results = array('Nun', 'Gimel', 'Hay', 'Shin');
    $spin = $results[array_rand($results)];

    if($spin == 'Shin')
    {
      $user_wallet['value']--;
      update_quest_value($user_wallet['idnum'], $user_wallet['value']);

      do_dreidel_shin($user['idnum']);
      $pot++;

      echo '<p class="failure">You got "Shin", and paid 1<span class="money">m</span> to the pot.</p>';
    }
    else if($spin == 'Nun')
    {
      do_dreidel_nun($user['idnum']);

      echo '<p class="failure">You got "Nun".  (Nothing happens.)</p>';
    }
    else if($spin == 'Gimel')
    {
      $amount = $dreidel_funds['value'];

      if($amount > 0)
      {
        give_money($user, $amount, 'Got "Gimel" in Dreidel');
        $user['money'] += $amount;

        do_dreidel_gimel($user['idnum'], $amount);
        $pot -= $amount;
      }

      echo '<p class="success">You got "Hay", and received the pot: ' . $amount . '<span class="money">m</span>!</p>';

      if($amount == 0)
      {
        if(mt_rand(1, 4) == 1)
        {
          give_money($user, 1, 'Got "Hay" in Dreidel');
          $user['money']++;
          echo '<p class="success">(Since the pot was empty, you had a 25% chance for the game to pay you 1<span class="money">m</span>, and you got it!)</p>';
        }
        else
          echo '<p class="failure">(Since the pot was empty, you had a 25% chance for the game to pay you 1<span class="money">m</span>, however you did not win it this time.)</p>';
      }
    }
    else if($spin == 'Hay')
    {
      $amount = ceil($dreidel_funds['value'] / 2);

      if($amount > 0)
      {
        give_money($user, $amount, 'Got "Hay" in Dreidel');
        $user['money'] += $amount;

        do_dreidel_hay($user['idnum'], $amount);
        $pot -= $amount;
      }

      echo '<p class="success">You got "Hay", and received half of the pot: ' . $amount . '<span class="money">m</span>!</p>';

      if($amount == 0)
      {
        if(mt_rand(1, 4) != 1)
        {
          give_money($user, 1, 'Got "Hay" in Dreidel');
          $user['money']++;
          echo '<p class="success">(Since the pot was empty, you had a 75% chance for the game to pay you 1<span class="money">m</span>, and you got it!)</p>';
        }
        else
          echo '<p class="failure">(Since the pot was empty, you had a 75% chance for the game to pay you 1<span class="money">m</span>, however you did not win it this time.)</p>';
      }
    }
    
    update_quest_value($user_dreidel['idnum'], $today);
    $user_dreidel['value'] = $today;
  }
?>
<h4>Spin the Draidel?</h4>
<p><b>The pot:</b> <?= $pot ?><span class="money">m</span></p>
<p><b>Your funds:</b> <?= (int)$user_wallet['value'] ?><span class="money">m</span></p>
<?php
  if($user_wallet['value'] == 0)
    echo '<p>Your Draidel money has been exhausted!  If you want to play again, you must set aside 20<span class="money">m</span> to play with, one of which will be placed directly into the pot.</p>' .
         '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=join">Join (20<span class="money">m</span>)</a></li></ul>';
  else if($today == $user_dreidel['value'])
    echo '<p>You may only spin the Draidel once per 4 hours.  You\'ll have to wait to spin again.</p>';
  else if($user['money'] < 1)
    echo '<p>You must have at least 1<span class="money">m</span> on hand to play Dreidel.</p>';
  else
  {
    echo '<p>Spinning the Draidel does not cost moneys, however if you spin a "Shin", you must pay 1<span class="money">m</span> to the pot.</p>';

    if($pot == 0)
      echo '<p>If you win when the pot is empty, you will have a 25% chance of receiving 1<span class="money">m</span>.</p>';

    echo '<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post"><p><input type="submit" name="action" value="Spin!" /></p></form>';
  }

  $logs = get_dreidel_logs(1);
?>
<h4>Dreidel Logs</h4>
<p>Only the most recent 20 spins are shown.</p>
<table>
 <thead>
  <tr class="titlerow"><th>Player</th><th>Spin</th><th>Action</th></tr>
 </thead>
 <tbody>
<?php
$rowclass = begin_row_class();

foreach($logs as $log)
{
  $spinner = get_user_byid($log['userid'], 'display');
?>
  <tr class="<?= $rowclass ?>">
   <td><?= resident_link($spinner['display']) ?></td>
   <td><?= $log['result'] ?></td>
   <td><?php
  if($log['result'] == 'Shin' || $log['result'] == '(joined game)')
    echo 'paid 1<span class="money">m</span> to the pot';
  else if($log['result'] == 'Gimel' || $log['result'] == 'Hay')
    echo 'received ' . (-$log['potchange']) . '<span class="money">m</span> from the pot';
  else
    echo 'nothing';
?></td>
  </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
 </tbody>
</table>
<?php
}
?>