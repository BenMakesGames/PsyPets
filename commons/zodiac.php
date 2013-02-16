<?php
$CHINESE_ZODIAC_EN = array(
  0 => 'Monkey',
  1 => 'Rooster',
  2 => 'Dog',
  3 => 'Pig',
  4 => 'Rat',
  5 => 'Ox',
  6 => 'Tiger',
  7 => 'Rabbit',
  8 => 'Dragon',
  9 => 'Snake',
 10 => 'Horse',
 11 => 'Goat'
);

function get_chinese_compatability($zodiac1, $zodiac2)
{
  // dislikes a little
  if(($zodiac1 - 4) % 12 == $zodiac2 || ($zodiac1 + 4) % 12 == $zodiac2)
    return 0.875;
  
  // dislikes
  else if(($zodiac1 - 6) % 12 == $zodiac2)
    return 0.75;

  // likes
  else if(($zodiac1 - 5) % 12 == $zodiac2 || ($zodiac1 + 5) % 12 == $zodiac2)
    return 1.5;
  
  // neutral
  else
    return 1;
}

function get_wester_compatability($zodiac1, $zodiac2)
{
  $zodiac1--;
  $zodiac2--;

  // dislikes
  if(($zodiac1 - 3) % 12 == $zodiac2 || ($zodiac1 + 3) % 12 == $zodiac2)
    return 0.75;
  
  // likes
  else if(($zodiac1 - 2) % 12 == $zodiac2 || ($zodiac1 + 2) % 12 == $zodiac2 ||
    ($zodiac1 - 4) % 12 == $zodiac2 || ($zodiac1 + 4) % 12 == $zodiac2)
    return 1.5;
    
  // neutral
  else
    return 1;
}

function get_chinese_zodiac($timestamp)
{
  list($year, $month, $day) = explode(' ', date('Y n j', $timestamp));

  $dates = array(
    2004 => array('month' => 1, 'day' => 22, 'sign' => 0),
    2005 => array('month' => 2, 'day' => 9, 'sign' => 1),
    2006 => array('month' => 1, 'day' => 29, 'sign' => 2),
    2007 => array('month' => 2, 'day' => 18, 'sign' => 3),
    2008 => array('month' => 2, 'day' => 7, 'sign' => 4),
    2009 => array('month' => 1, 'day' => 26, 'sign' => 5),
    2010 => array('month' => 2, 'day' => 14, 'sign' => 6),
    2011 => array('month' => 2, 'day' => 3, 'sign' => 7),
    2012 => array('month' => 1, 'day' => 23, 'sign' => 8),
    2013 => array('month' => 2, 'day' => 10, 'sign' => 9),
    2014 => array('month' => 1, 'day' => 31, 'sign' => 10),
    2015 => array('month' => 2, 'day' => 19, 'sign' => 11),
    2016 => array('month' => 2, 'day' => 8, 'sign' => 0),
    2017 => array('month' => 1, 'day' => 28, 'sign' => 1),
    2018 => array('month' => 2, 'day' => 16, 'sign' => 2),
    2019 => array('month' => 2, 'day' => 5, 'sign' => 3),
    2020 => array('month' => 1, 'day' => 25, 'sign' => 4),
    2021 => array('month' => 2, 'day' => 12, 'sign' => 5),
    2022 => array('month' => 2, 'day' => 1, 'sign' => 6),
    2023 => array('month' => 1, 'day' => 22, 'sign' => 7),
  );

  if(!array_key_exists($year, $dates))
    return 'unknown';
  else
  {
    if($month > $dates[$year]['month'] || ($month == $dates[$year]['month'] && $day >= $dates[$year]['day']))
      return $dates[$year]['sign'];
    else if(array_key_exists($year - 1, $dates))
      return $dates[$year - 1]['sign'];
    else
      return 'unknown';
  }
}

$WESTERN_ZODIAC = array(
  1 => 'Aries',
  2 => 'Taurus',
  3 => 'Gemini',
  4 => 'Cancer',
  5 => 'Leo',
  6 => 'Virgo',
  7 => 'Libra',
  8 => 'Scorpio',
  9 => 'Sagittarius',
  10 => 'Capricorn',
  11 => 'Aquarius',
  12 => 'Pisces',
  13 => 'ERROR\'D',
);

function get_western_zodiac($time)
{
  $month = (int)date('n', $time);
  $day = (int)date('j', $time);
  
  if(($month == 3 && $day >= 21) || ($month == 4 && $day <= 19))
    return 1; // aires
  else if(($month == 4 && $day >= 20) || ($month == 5 && $day <= 20))
    return 2; // taurus
  else if(($month == 5 && $day >= 21) || ($month == 6 && $day <= 20))
    return 3; // gemini
  else if(($month == 6 && $day >= 21) || ($month == 7 && $day <= 22))
    return 4; // cancer
  else if(($month == 7 && $day >= 23) || ($month == 8 && $day <= 22))
    return 5; // leo
  else if(($month == 8 && $day >= 23) || ($month == 9 && $day <= 22))
    return 6; // virgo
  else if(($month == 9 && $day >= 23) || ($month == 10 && $day <= 22))
    return 7; // libra
  else if(($month == 10 && $day >= 23) || ($month == 11 && $day <= 21))
    return 8; // scorpio
  else if(($month == 11 && $day >= 22) || ($month == 12 && $day <= 21))
    return 9; // sagittarius
  else if(($month == 12 && $day >= 22) || ($month == 1 && $day <= 19))
    return 10; // capricorn
  else if(($month == 1 && $day >= 20) || ($month == 2 && $day <= 18))
    return 11; // aquarius
  else if(($month == 2 && $day >= 19) || ($month == 3 && $day <= 20))
    return 12; // pisces
  else
    return 13; // ERROR'D
}
?>
