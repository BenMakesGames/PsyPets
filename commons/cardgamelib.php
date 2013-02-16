<?php
$CARD_IMAGES = array(
  1 => 'caprioska',
  2 => 'mlt',
  3 => 'honeycomb',
  4 => 'window',
  5 => 'hyacinth',
  6 => 'paper',
  7 => 'autumn',
);

$CARD_ITEMS = array(
  1 => 'Mint Caprioska',
  2 => 'M.L.T.',
  3 => 'Honeycomb',
  4 => 'Window Blueprint',
  5 => 'Hyacinth Bulb',
  6 => 'Paper',
  7 => 'Autumn Leaf',
);

function new_card_game($userid)
{
  $cards = array();

  for($x = 0; $x < 21; ++$x)
  {
    $card = mt_rand(1, 7);
    $cards[] = $card;
    $cards[] = $card;
  }

  shuffle($cards);

  $string = implode('', $cards);

  $command = '
    INSERT INTO psypets_cardgame
    (userid, cards)
    VALUES
    (' . $userid . ', \'' . $string . '\')
  ';
  fetch_none($command, 'starting new card game');
}

function get_card_game($userid)
{
  $command = 'SELECT * FROM psypets_cardgame WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching card game');
}

function render_card_game($game, $link)
{
  global $CARD_IMAGES, $CARD_ITEMS;

  echo '<table>';

  for($y = 0; $y < 6; ++$y)
  {
    echo '<tr>';

    for($x = 0; $x < 7; ++$x)
    {
      echo '<td>';

      $i = $x + $y * 7;

      if($game['mask']{$i} == '?') // unknown
      {
        if($link === false)
          echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/cards/back.png" width="48" height="64" alt="face-down card" />';
        else
          echo '<a href="itemaction.php?idnum=' . $link . '&action=flip&x=' . $x . '&y=' . $y . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/cards/back.png" width="48" height="64" alt="face-down card" /></a>';
      }
      else if($game['mask']{$i} == '.') // already collected
        echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/cards/' . $CARD_IMAGES[$game['cards']{$i}] . '.png" width="48" height="64" alt="' . $CARD_ITEMS[$game['cards']{$i}] . ' card (already collected)" class="transparent_image" />';
      else if($game['mask']{$i} == '!') // revealed
        echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/cards/' . $CARD_IMAGES[$game['cards']{$i}] . '.png" width="48" height="64" alt="' . $CARD_ITEMS[$game['cards']{$i}] . ' card" />';

      echo '</td>';
    }

    echo '</tr>';
  }

  echo '</table>';
}

function flip(&$game, $x, $y)
{
  global $CARD_ITEMS;

  $item = false;

  $i = $x + $y * 7;
  
  if($game['tries'] > 0 && $game['mask']{$i} == '?')
  {
    if($game['flipped'] == 0)
    {
      $game['mask']{$i} = '!';
      $game['flipped'] = 1;
      
      $command = '
        UPDATE psypets_cardgame
        SET
          mask=\'' . $game['mask'] . '\',
          flipped=\'1\'
        WHERE userid=' . $game['userid'] . '
        LIMIT 1
      ';
      fetch_none($command, 'recording first flip in card game');
    }
    else if($game['flipped'] == 1)
    {
      $j = 0;
      for($j = 0; $j < 42; ++$j)
      {
        if($game['mask']{$j} == '!')
          break;
      }
      
      $real_mask = $game['mask'];
      
      if($game['cards']{$i} == $game['cards']{$j})
      {
        // match!
        $game['mask']{$i} = '.';
        $game['mask']{$j} = '.';
        
        $real_mask{$i} = '.';
        $real_mask{$j} = '.';

        $item = $CARD_ITEMS[$game['cards']{$i}];
      }
      else
      {
        // no match!
        $game['mask']{$i} = '!';
        $game['mask']{$j} = '!';

        $real_mask{$i} = '?';
        $real_mask{$j} = '?';

        $game['tries']--;
      }
      
      $game['flipped'] = 0;
      
      $command = '
        UPDATE psypets_cardgame
        SET
          mask=\'' . $real_mask . '\',
          tries=' . $game['tries'] . ',
          flipped=' . $game['flipped'] . '
        WHERE userid=' . $game['userid'] . '
        LIMIT 1
      ';
      fetch_none($command, 'recording second flip in card game');
    }
    else
      die('Whaaa?');
  }
  
  return $item;
}

function reset_card_game_tries($userid)
{
  $command = 'UPDATE psypets_cardgame SET tries=3 WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'resetting number of tries for card game');
}

function card_game_is_done(&$game)
{
  return($game['mask'] == '..........................................');
}

function delete_card_game($userid)
{
  $command = 'DELETE FROM psypets_cardgame WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'deleting card game');
}
?>
