<?php
function transmutation_available($alchemy)
{
  global $now, $now_year, $now_month, $now_day;
  
  $sign_num = get_western_zodiac($now);

  if($alchemy['month'] == 0)
    return true;
  else if($alchemy['month'] >= 1 && $alchemy['month'] <= 12)
    return($sign_num == $alchemy['month']);
  else if($alchemy['month'] == 13) // February 29th
    return(date('md') == '0229');
  else if($alchemy['month'] == 14) // new moon
    return is_new_moon();
  else if($alchemy['month'] == 15) // full moon
    return is_full_moon();
  else if($alchemy['month'] == 16) // May, July, and September
    return($now_month == 5 || $now_month == 7 || $now_month == 9);
  else if($alchemy['month'] == 17) // Venus-Jupiter Conjunction
  {
    $jv[2012][3][15] = true;
    $jv[2013][5][28] = true;
    $jv[2014][8][18] = true;
    $jv[2015][7][1] = true;
    $jv[2015][7][31] = true;
    $jv[2015][10][26] = true;
    $jv[2016][8][27] = true;
    $jv[2017][11][13] = true;
    $jv[2019][1][22] = true;
    $jv[2019][11][24] = true;
    
    return($jv[$now_year][$now_month][$now_day] === true);
  }
  else
    return false;
}

function clear_tower_monkey($userid)
{
  fetch_none('
    UPDATE psypets_towers
    SET monkeyname=\'\',monkeydesc=\'\',nextsearch=0
    WHERE userid=' . $userid . '
    LIMIT 1
  ');
}

function get_tower_byuser($userid)
{
  $command = "SELECT * FROM psypets_towers WHERE userid=$userid LIMIT 1";
  $tower = fetch_single($command, 'towerlib.php/get_tower_byuser()');

  return $tower;
}

function create_tower($userid)
{
  $command = "INSERT INTO psypets_towers (userid) VALUES ($userid)";
  fetch_none($command, 'creating tower');
}

function add_monkey_log($userid, $monkey_name, $food, $prize)
{
  global $now;

  $command = 'INSERT INTO psypets_monkeylog (userid, timestamp, monkeyname, food, prize) VALUES ' .
             '(' . $userid . ', ' . $now . ', ' . quote_smart($monkey_name) . ', ' . quote_smart($food) . ', ' . quote_smart($prize) . ')';
  fetch_none($command, 'logging monkey activity');
}

function get_monkey_logs($userid)
{
  $command = 'SELECT * FROM psypets_monkeylog WHERE userid=' . $userid . ' ORDER BY idnum DESC LIMIT 20';
  return fetch_multiple($command, 'fetching monkey logs');
}

function attach_bell($userid)
{
  $command = 'UPDATE psypets_towers SET bell=\'yes\' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'attaching bell');
}
?>
