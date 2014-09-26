<?php
if($okay_to_be_here !== true)
  exit();

function is_ambiguous_replacement(&$grid, $x, $y, $words, $word_len)
{
  for($i = 0; $i < $word_len; ++$i)
  {
    if($i == $x)
      $row_pattern .= '[a-z]';
    else
      $row_pattern .= $grid[$i][$y];
  }

  for($i = 0; $i < $word_len; ++$i)
  {
    if($i == $y)
      $col_pattern .= '[a-z]';
    else
      $col_pattern .= $grid[$x][$i];
  }

  foreach($words as $word)
  {
    $col_matches += preg_match('/^' . $col_pattern . '$/', $word);
    $row_matches += preg_match('/^' . $row_pattern . '$/', $word);
  }
  
  return($col_matches > 1 || $row_matches > 1);
}

function build_grid($words, $word_len)
{
  // fill in grid with random, but not-impossible letters
  for($y = 0; $y < $word_len; ++$y)
  {
    for($x = 0; $x < $word_len; ++$x)
      $grid[$x][$y] = $words[array_rand($words)]{mt_rand(1, 2) == 1 ? $x : $y};
  }

  $target_count = round($word_len / 2.5, 0);

  $tries = 0;

  do
  {
    do
    {
      $x = mt_rand(1, $word_len) - 1;
      $y = mt_rand(1, $word_len) - 1;
    
      $word = $words[array_rand($words)];
      $i = (mt_rand(1, 2) == 1 ? $x : $y);
      
      if(++$tries > 2000)
      {
        break(2);
        echo '<p><b>Had to break out early.</b></p>';
      }
    } while(is_ambiguous_replacement($grid, $x, $y, $words, $word_len));

    $grid[$x][$y] = '[a-z]';
  } while(count_words($grid, $words, $word_len) < $target_count);

  for($y = 0; $y < $word_len; ++$y)
  {
    for($x = 0; $x < $word_len; ++$x)
    {
      if($grid[$x][$y] == '[a-z]')
        $grid[$x][$y] = '.';
    }
  }

  return $grid;
}

function count_words(&$grid, $words, $word_len)
{
  $word_count = 0;

  // go row-by-row
  for($y = 0; $y < $word_len; ++$y)
  {
    $check = '';
  
    for($x = 0; $x < $word_len; ++$x)
      $check .= $grid[$x][$y];

    foreach($words as $word)
      $word_count += preg_match('/^' . $check . '$/', $word);
  }

  // go column-by-column
  for($x = 0; $x < $word_len; ++$x)
  {
    $check = '';
  
    for($y = 0; $y < $word_len; ++$y)
      $check .= $grid[$x][$y];

    foreach($words as $word)
      $word_count += preg_match('/^' . $check . '$/', $word);
  }

  return $word_count;
}


function render_grid_xhtml(&$grid, $word_len)
{
  $xhtml = '<table class="wordgrid">';
  
  for($y = 0; $y < $word_len; ++$y)
  {
    $xhtml .= '<tr>';

    for($x = 0; $x < $word_len; ++$x)
      $xhtml .= '<td>' . $grid[$x][$y] . '</td>';

    $xhtml .= '</tr>';
  }
  
  $xhtml .= '</table>';
  
  return $xhtml;
}

$word_sets = array(
  array('bananas', 'lantern'),
  array('warrior', 'forlorn'),
  array('wettest', 'sweeten'),
  array('tangelo', 'ageless', 'deletes'),
  array('doodles', 'swooped'),
  array('hexagon', 'sexless'),
  array('festoon', 'shampoo'),
  array('tiptoed', 'fondled', 'fortune'),
  array('seminar', 'minaret'),
  array('cobwebs', 'webbing', 'corncob'),
  array('coughed', 'draught', 'jughead'),
  array('session', 'missive', 'motions'),
  array('moaning', 'innings'),
  array('laundry', 'saunter', 'dusters'),
  array('pumpkin', 'unpacks', 'gunplay'),
  array('gymnast', 'dynasty'),
  array('homonym', 'hormone', 'acronym'),
  array('polygon', 'nondrip'),
  array('angelic', 'apelike', 'unliked'),
  array('unadult', 'dullest', 'lunatic'),
  array('logging', 'godlike'),
);

echo '
  <style type="text/css">
   .wordgrid tr td { width: 16px; height: 16px; text-align: center; vertical-align: middle; }
  </style>
';

$words = $word_sets[array_rand($word_sets)];

$word_length = strlen($words[0]);

$grid = build_grid($words, $word_length);

$remove_word = array();
$word_count = 0;

foreach($words as $i=>$word)
{
  $count = count_words($grid, array($word), $word_length);
  $word_count += $count;
  if($count == 0)
    $remove_word[] = $i;
}

foreach($remove_word as $i)
  unset($words[$i]);

echo '
  <p>The following word' . (count($words) == 1 ? '' : 's') . ' can be placed into the grid below ' . $word_count . ' times total - not each; total - in reading-order (left-to-right, top-to-bottom), no diagonals:</p>
  <ul><li>' . implode('</li><li>', $words) . '</li></ul>
  ' . render_grid_xhtml($grid, $word_length) . '
';

$AGAIN_WITH_SAME = true;
?>
