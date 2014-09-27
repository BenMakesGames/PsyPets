<?php
function build_title($items, $links = true, $separator = ' &gt; ')
{
  $titles = array();

  foreach($items as $item)
  {
    if($links && $item[0] !== false)
      $titles[] = '<a href="' . $item[0] . '">' . $item[1] . '</a>';
    else
      $titles[] = $item[1];
  }
  
  return implode($separator, $titles);
}

function array_search_reverse($needle, $haystack, $strict = false)
{
  $keys = array_keys($haystack, $needle, $strict);
  
  if(!is_array($keys) || count($keys) == 0)
    return false;
  else
    return array_pop($keys);
}

function take_apart($delim, $string)
{
  if(strlen($string) == 0)
    return array();
  else
    return explode($delim, $string);
}

function list_nice($items, $last_word = ' and ')
{
  $i = 1;
  foreach($items as $item)
  {
    $string .= $item;
  
    if($i < count($items) - 1)
      $string .= ', ';
    else if($i < count($items))
      $string .= $last_word;
  
    $i++;
  }

  return $string;
}

function duration($t)
{
  if(func_num_args() == 2)
  {
    $param = func_get_arg(1);
    $details = max(1, (int)$param);
  }
  else
    $details = 1;

  $minute = 60;
  $hour = 60 * $minute;
  $day = 24 * $hour;
  $week = 7 * $day;
  $month = $day * 30.5;
  $year = $day * 365;

  $years = 0;
  $months = 0;
  $weeks = 0;
  $days = 0;
  $hours = 0;
  $minutes = 0;

  if($t >= $year && $details > 0)
  {
    while($t >= $year)
    {
      $t -= $year;
      $years++;
    }

    $durations[] = $years . ' ' . ($years == 1 ? 'year' : 'years');
    $details--;
  }

  if($t >= $month && $details > 0)
  {
    while($t >= $month)
    {
      $t -= $month;
      $months++;
    }

    $durations[] = $months . ' ' . ($months == 1 ? 'month' : 'months');
    $details--;
  }

  if($t >= $week && $details > 0)
  {
    while($t >= $week)
    {
      $t -= $week;
      $weeks++;
    }

    $durations[] = $weeks . ' ' . ($weeks == 1 ? 'week' : 'weeks');
    $details--;
  }

  if($t >= $day && $details > 0)
  {
    while($t >= $day)
    {
      $t -= $day;
      $days++;
    }

    $durations[] = $days . ' ' . ($days == 1 ? 'day' : 'days');
    $details--;
  }

  if($t >= $hour && $details > 0)
  {
    while($t >= $hour)
    {
      $t -= $hour;
      $hours++;
    }

    $durations[] = $hours . ' ' . ($hours == 1 ? 'hour' : 'hours');
    $details--;
  }

  if($t >= $minute && $details > 0)
  {
    while($t >= $minute)
    {
      $t -= $minute;
      $minutes++;
    }

    $durations[] =  $minutes . ' ' . ($minutes == 1 ? 'minute' : 'minutes');
    $details--;
  }

  if(count($durations) == 0)
    $durations[] = 'less than a minute';

  return implode(', ', $durations);
}

function article($text)
{
  $fl = $text{0};

  if($fl == 'a' || $fl == 'e' || $fl == 'i' || $fl == 'o' || $fl == 'u')
    return 'an';
  else if($fl == '8')
    return 'an';
  else if($text == 'hour' || $text == 'herb' || $text == '18')
    return 'an';
  else
    return 'a';
}
?>
