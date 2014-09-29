<?php
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/petlib.php';
require_once 'commons/moonphase.php';

function interest_rate()      { return  0.001; }
function sellback_rate()      { return  0.50; }
function alchemy_efficiency() { return  0.600; }
function sellers_fee()        { return  0.02;  }
function pet_sellers_fee()    { return  0.10;  }
function max_house_size()     { return 200000;  }

$FREE_STORAGE_DAYS = array(/*
  'April 9', 'April 10',
  'July 21', 'July 22',*/
);

function add_transaction($user, $now, $descript, $amount, $details = '')
{
  if($amount == 0)
    return;

  fetch_none('
    INSERT INTO monster_transactions
    (`user`, `timestamp`, `description`, `details`, `amount`)
    VALUES
    (
      ' . quote_smart($user) . ',
      ' . (int)$now . ',
      ' . quote_smart($descript) . ',
      ' . quote_smart($details) . ',
      ' . (int)$amount . '
    )
  ');
}

function clear_transactions($user)
{
  $command = 'DELETE FROM monster_transactions WHERE user=' . quote_smart($user);
  fetch_none($command, 'rpg functions > clear transaction history');
}

function dice_roll($d, $s)
{
  $total = 0;
  for($i = 0; $i < $d; ++$i)
  {
    $total += mt_rand(1, $s);
  }
//   echo " (rolled " . $total . ") ";
  return $total;
}

function successes($dice)
{
  if($dice <= 0)
    return (int)$dice;

  $total = 0;

  for($i = 0; $i < $dice; ++$i)
  {
    $r = mt_rand(1, 10);

    if($r >= 7)
      $total++;
  }

  return $total;
}

function success_roll($d, $s, $diff)
{
  $total = 0;
  for($i = 0; $i < $d; ++$i)
  {
    $r = mt_rand(1, $s);

    if($r >= $diff)
      $total++;
  }

  return $total;
}
?>
