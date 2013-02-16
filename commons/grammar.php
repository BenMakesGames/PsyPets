<?php
function say_number($num)
{
  if($num == 1)
    return 'one';
  else if($num == 2)
    return 'two';
  else if($num == 3)
    return 'three';
  else if($num == 4)
    return 'four';
  else if($num == 5)
    return 'five';
  else if($num == 6)
    return 'six';
  else if($num == 7)
    return 'seven';
  else if($num == 8)
    return 'eight';
  else if($num == 9)
    return 'nine';
  else if($num == 10)
    return 'ten';
  else
    return $num;
}

function durability_group($max)
{
  if($max == 0)
    return 'Indestructible';
  else if($max <= 24)
    return 'Smoke-like';
  else if($max <= 100)
    return 'Paper-like';
  else if($max <= 200)
    return 'Cardboard-like';
  else if($max <= 300)
    return 'Wood-like';
  else if($max <= 400)
    return 'Metal-like';
  else if($max <= 500)
    return 'Hypertech/Magic';
  else if($max <= 600)
    return 'High magic';
  else if($max <= 800)
    return 'Legendary';
  else
    return 'Insaaaaaaane!';
}

function durability($cur, $max)
{
  if($max == 0)
    return 'Indestructible';

  $percent = $cur / $max;

  if($cur == $max)
    return 'Brand-new';
  else if($percent > 2 / 3)
    return 'New';
  else if($percent > 1 / 3)
    return 'Used';
  else if($cur > 0)
    return 'Worn';
  else
    return 'Broken';
}

function durability_sort($cur, $max)
{
  if($max == 0)
    return 'f';

  $percent = $cur / $max;

  if($cur == $max)
    return 'e';
  else if($percent > 2 / 3)
    return 'd';
  else if($percent > 1 / 3)
    return 'c';
  else if($cur > 0)
    return 'b';
  else
    return 'a';
}

function numeric_place($n)
{
  $n .= '';
  $last = substr($n, strlen($n) - 1);

  if($last == '0' || $last >= 4 || ($n > 10 && $n < 20))
    return $n . 'th';
  else if($last == '1')
    return $n . 'st';
  else if($last == '2')
    return $n . 'nd';
  else if($last == '3')
    return $n . 'rd';
  else
    return $n . '?';
}

function random_name($gender)
{
  $all_names = array(
    'male' => array(
      'Aaron',
      'Abrahil',
      'Aelfric',
      'Alain',
      'Batu',
      'Bezzhen',
      'Blicze',
      'Ceslinus',
      'Christien',
      'Clement',
      'Czestobor',
      'Disideri',
      'Enim',
      'Erasmus',
      'Felix',
      'Gennoveus',
      'Idzi',
      'Kain',
      'Kint',
      'Kirik',
      'Kryspin',
      'Levi',
      'Leodhild',
      'Leon',
      'Lucass',
      'Maccos',
      'Maeldoi',
      'Malik',
      'Masayasu',
      'Mathias',
      'Mold',
      'Montgomery',
      'Morys',
      'Maurifius',
      'Nicholina',
      'Nilus',
      'Noe',
      'Oswyn',
      'Paperclip',
      'Pesczek',
      'Rocatos',
      'Rum',
      'Ryd',
      'Saewine',
      'Sandivoi',
      'Skenfrith',
      'Sulimir',
      'Talan',
      'Tede',
      'Trenewydd',
      'Usk',
      'Vitseslav',
      'Wrexham',
      'Zygmunt',
    ),
    'female' => array(
      'Aalina',
      'Aedoc',
      'Alda',
      'Alienora',
      'Aliette',
      'Artaca',
      'Aureliana',
      'Belka',
      'Biedeluue',
      'Ceinguled',
      'Ceri',
      'Cyra',
      'Dagena',
      'Denyw',
      'Eileve',
      'Emilija',
      'Enynny',
      'Eve',
      'Fiora',
      'Fluri',
      'Frotlildis',
      'Galine',
      'Genoveva',
      'Giliana',
      'Godelive',
      'Gubin',
      'Jehanne',
      'Jadviga',
      'Kaija',
      'Kima',
      'Klara',
      'Lowri',
      'Ludmila',
      'Magdalena',
      'Makrina',
      'Margaret',
      'Marsley',
      'Mateline',
      'Meduil',
      'Melita',
      'Meoure',
      'Merewen',
      'Milesent',
      'Milian',
      'Paperclip',
      'Perkhta',
      'Regina',
      'Reina',
      'Rimoete',
      'Rozalia',
      'Rum',
      'Runne',
      'Sybil',
      'Tede',
      'Tephaine',
      'Tetris',
      'Tiecia',
      'Toregene',
      'Vasilii',
      'Vivka',
      'Ysabeau',
      'Ystradewel',
      'Zofija',
    )
  );

  $name = $all_names[$gender][array_rand($all_names[$gender])];

  return $name;
}

function random_adjective($type)
{
  $adjectives = array();

  if($type < 0)
  {
    $adjectives = array('distasteful', 'repugnant', 'awful', 'terrible', 'unsightly');
  }
  else if($type > 0)
  {
    $adjectives = array('fantastic', 'outstanding', 'beautiful', 'redemptive', 'superb');
  }
  else
  {
    $adjectives = array('passable', 'adequate', 'sufficient', 'ordinary', 'permissible');
  }

  return $adjectives[array_rand($adjectives)];
}

function him_her($g) { return t_pronoun($g); }

function t_pronoun($g)
{
  if($g == 'male')
    return 'him';
  else
    return 'her';
}

function he_she($g) { return pronoun($g); }

function pronoun($g)
{
  if($g == 'male')
    return 'he';
  else
    return 'she';
}

function his_her($g) { return p_pronoun($g); }

function p_pronoun($g)
{
  if($g == 'male')
    return 'his';
  else
    return 'her';
}

function his_hers($g) { return possessive_pronoun($g); }

function possessive_pronoun($g)
{
  if($g == 'male')
    return 'his';
  else
    return 'hers';
}

function random_fortune()
{
  $fortunes = @file('fortunes.txt');

  return $fortunes[array_rand($fortunes)];
}

function random_valentine()
{
  $valentine = @file('valentines.txt');

  return $valentine[array_rand($valentine)];
}

function gender_graphic($gender, $prolific)
{
  if($gender == 'male')
  {
    if($prolific == 'yes')
      return '<img src="/gfx/boy.gif" height="12" width="12" alt="male" />';
    else
      return '<img src="/gfx/boy_fixed.gif" height="12" width="12" alt="neutered male" />';
  }
  else if($gender == 'female')
  {
    if($prolific == 'yes')
      return '<img src="/gfx/girl.gif" height="12" width="12" alt="female" />';
    else
      return '<img src="/gfx/girl_fixed.gif" height="12" width="12" alt="spayed female" />';
  }
}

function gender_description($gender, $prolific)
{
  if($gender == 'male')
  {
    if($prolific == 'yes')
      return 'male';
    else
      return 'neutered male';
  }
  else
  {
    if($prolific == 'yes')
      return 'female';
    else
      return 'spayed female';
  }
}

function plural($num, $single, $plural)
{
  return($num == 1 ? $single : $plural);
}

function alphabetize_letters($name)
{
  $name = str_replace(
    array('é', 'ó', 'ö', 'ñ'),
    array('e', 'o', 'o', 'n'),
    $name
  );
  $name = preg_replace('/[^a-z0-9]/', '', strtolower($name));
  $len = strlen($name);

  for($i = 0; $i < $len; ++$i)
    $letters[] = $name[$i];

  sort($letters);

  return implode('', $letters);
}
?>
