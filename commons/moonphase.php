<?php
// adapted from http://home.att.net/~srschmitt/script_moon_phase.html#contents
// which was adapted from a BASIC program in the Astronomical Computing column of Sky & Telescope, April 1994.

function is_new_moon()
{
  return(moon_phase(time()) == 'new moon');
}

function is_full_moon()
{
  global $now;
  
  return(moon_phase(time()) == 'full moon');
}

function moon_graphic()
{
	global $SETTINGS;

  $graphics = array(
    'new moon' => 'new',
    'waxing crescent' => 'wax_cres',
    'first quarter' => 'first_quarter',
    'waxing gibbous' => 'wax_gib',
    'full moon' => 'full',
    'waning gibbous' => 'wan_gib',
    'last quarter' => 'last_quarter',
    'waning crescent' => 'wan_cres'
  );

  $phase = moon_phase(time());

  return '<img src="//' . $SETTINGS['static_domain'] . '/gfx/moon/' . $graphics[$phase] . '.png" width="16" height="14" alt="(' . $phase . ')" style="vertical-align:text-top;" />';
}

function moon_phase_power($time)
{
  $year  = (int)date('Y', $time);
  $month = (int)date('n', $time);
  $day   = (int)date('j', $time);

  $YY = $year - floor((12 - $month) / 10);

  $MM = $month + 9;
  if($MM >= 12)
    $MM -= 12;

  $K1 = floor(365.25 * ($YY + 4712));
  $K2 = floor(30.6 * $MM + .5);
  $K3 = floor(floor(($YY / 100) + 49) * .75) - 38;
  
  $JD = $K1 + $K2 + $day + 59;
  if($JD > 2299160)
    $JD -= $K3;

  $IP = ($JD - 2451550.1) / 29.530588853;

  // normalize IP
  $IP = $IP - floor($IP);
  if($IP < 0)
    $IP++;

  $AG = $IP * 29.53;

  if($AG < 1.84566)
    return -2; // new
  else if($AG < 5.53699)
    return -1;
  else if($AG < 9.22831)
    return 0;
  else if($AG < 12.91963)
    return 1;
  else if($AG < 16.61096)
    return 2; // full
  else if($AG < 20.30228)
    return 1;
  else if($AG < 23.99361)
    return 0;
  else if($AG < 27.68493)
    return -1;
  else
    return -2; // new
}

function moon_phase($time)
{
  $year  = (int)date('Y', $time);
  $month = (int)date('n', $time);
  $day   = (int)date('j', $time);

  $YY = $year - floor((12 - $month) / 10);

  $MM = $month + 9;
  if($MM >= 12)
    $MM -= 12;

  $K1 = floor(365.25 * ($YY + 4712));
  $K2 = floor(30.6 * $MM + .5);
  $K3 = floor(floor(($YY / 100) + 49) * .75) - 38;
  
  $JD = $K1 + $K2 + $day + 59;
  if($JD > 2299160)
    $JD -= $K3;

  $IP = ($JD - 2451550.1) / 29.530588853;

  // normalize IP
  $IP = $IP - floor($IP);
  if($IP < 0)
    $IP++;

  $AG = $IP * 29.53;
  
  if($AG < 1.84566)
    return 'new moon';
  else if($AG < 5.53699)
    return 'waxing crescent';
  else if($AG < 9.22831)
    return 'first quarter';
  else if($AG < 12.91963)
    return 'waxing gibbous';
  else if($AG < 16.61096)
    return 'full moon';
  else if($AG < 20.30228)
    return 'waning gibbous';
  else if($AG < 23.99361)
    return 'last quarter';
  else if($AG < 27.68493)
    return 'waning crescent';
  else
    return 'new moon';
}
?>
