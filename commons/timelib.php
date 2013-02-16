<?php
// from http://michaelthompson.org/technikos/holidays.php

// day_type: Sun => 0, Mon => 1, Tue => 2, ...
// instance: 1 = 1st of day_type, 2 = 2nd of day_type
// returns the number of the day of the month of this holiday
function calculate_holiday($month, $day_type, $instance)
{
  $nTargetday = $day_type;
  $nMonth = $month;
  $nYear = (int)date('Y');

  $nEarlistDate = 1 + 7 * ($instance - 1);

  $nWeekday = date('w', mktime(0, 0, 0, $nMonth, $nEarlistDate, $nYear));

  if($nTargetday == $nWeekday)
    $nOffset = 0;
  else
  {
    if($nTargetday < $nWeekday)
      $nOffset = $nTargetday + (7 - $nWeekday);
    else
      $nOffset = ($nTargetday + (7 - $nWeekday)) - 7;
  }

  return $nEarlistDate + $nOffset;
}

function is_thanksgiving()
{
  global $now_month, $now_day;
  
  if($now_month == 11)
    return($now_day == calculate_holiday(11, 4, 4));
  else
    return false;
}

function psypets_easter($time = false)
{
  if($time === false)
    $time = time();

  if(is_easter($time + 0 * 24 * 60 * 60))
    return 5;
  else if(is_easter($time + 1 * 24 * 60 * 60))
    return 4;
  else if(is_easter($time + 2 * 24 * 60 * 60))
    return 3;
  else if(is_easter($time + 3 * 24 * 60 * 60))
    return 2;
  else if(is_easter($time + 4 * 24 * 60 * 60))
    return 1;
  else
    return 0;
}

function mod($n, $b)
{
  return $n - $b * floor($n / $b);
}

function div($n, $b)
{
  return floor($n / $b);
}

function is_easter($time = false)
{
  if($time === false)
    $time = time();

  global $easter_month;
  global $easter_day;

  list($year, $month, $day) = explode(' ', date('Y n j', $time));

  $golden_number = ($year % 19) + 1;

  $solar_correction = div(($year - 1600), 100) - div(($year - 1600), 400);
  $lunar_correction = div((div(($year - 1400), 100) * 8), 25);

  $uncorrected_paschal_full_moon = mod(3 - 11 * $golden_number + $solar_correction - $lunar_correction, 30);

  if($uncorrected_paschal_full_moon == 29 || ($uncorrected_paschal_full_moon == 28 && $golden_number > 11))
    $paschal_full_moon = $uncorrected_paschal_full_moon - 1;
  else
    $paschal_full_moon = $uncorrected_paschal_full_moon;

  $dominical_number = mod($year + div($year, 4) - div($year, 100) + div($year, 400), 7);

  $days_after_march_21 = $paschal_full_moon + 1 + mod(4 - $dominical_number - $paschal_full_moon, 7);

  if($days_after_march_21 < 11)
  {
    $easter_month = 3;
    $easter_day = $days_after_march_21 + 21;
  }
  else
  {
    $easter_month = 4;
    $easter_day = $days_after_march_21 - 10;
  }
  
  return($month == $easter_month && $day == $easter_day);
}

function is_hanukkah()
{
  global $now_year, $now_month, $now_day;

  $jdCurrent = gregoriantojd($now_month, $now_day, $now_year);

  $TISHRI = 1;
  $HESHVAN = 2;
  $KISLEV = 3;
  $TEVET = 4;
  $SHEVAT = 5;
  $ADAR = 6;
  $ADAR_I = 6;
  $ADAR_II = 7;
  $NISAN = 8;
  $IYAR = 9;
  $SIVAN = 10;
  $TAMMUZ = 11;
  $AV = 12;
  $ELUL = 13;

  $SUNDAY = 0;
  $MONDAY = 1;
  $TUESDAY = 2;
  $WEDNESDAY = 3;
  $THURSDAY = 4;
  $FRIDAY = 5;
  $SATURDAY = 6;

  $jewishDate = jdtojewish($jdCurrent);
  list($jewishMonth, $jewishDay, $jewishYear) = split('/', $jewishDate);

  // Holidays in Kislev/Tevet
  $hanukkahStart = jewishtojd($KISLEV, 25, $jewishYear);
  $hanukkahNo = (int) ($jdCurrent-$hanukkahStart+1);

  return($hanukkahNo >= 1 && $hanukkahNo <= 8);
}
?>
