<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/cardgamelib.php';

$game = get_card_game($user['idnum']);

if($game === false)
{
  new_card_game($user['idnum']);
  $game = get_card_game($user['idnum']);

  if($game === false)
    die('Failed to create new game!  Crazy!');
}

$message = '
  <p>Click on a card to flip it over.  Match two cards to get an item!</p>
  <p>You have ' . $game['tries'] . ' strike' . ($game['tries'] != 1 ? 's' : '') . ' remaining.</p>
';

$link = $this_inventory['idnum'];
$all_done = false;

if($_GET['action'] == 'flip')
{
  $x = (int)$_GET['x'];
  $y = (int)$_GET['y'];
  
  if($x >= 0 && $x < 7 && $y >= 0 && $y < 6)
  {
    $itemname = flip($game, $x, $y);
    
    if($itemname !== false)
    {
      add_inventory($user['user'], '', $itemname, 'Received from ' . $this_inventory['itemname'], $this_inventory['location']);

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Matching Cards Revealed in Magic Cards', 1);

      if(card_game_is_done($game))
      {
        $message = '
          <p>You revealed two ' . $itemname . ' cards!  You receive ' . $itemname . '!</p>
          <p>Unfortunately, you\'re out of cards!  (The ' . $this_inventory['itemname'] . ' vanishes!)  But don\'t worry: you can play again when you get another ' . $this_inventory['itemname'] . '!</p>
        ';

        $all_done = true;

        delete_card_game($user['idnum']);

        if(record_stat_with_badge($user['idnum'], 'Magic Card Games Finished', 1, 3, 'cardplayer'))
          $message .= '<p><i>(You received the Card-player badge!)</i></p>';
      }
      else
      {
        $message = '
          <p>You revealed two ' . $itemname . ' cards!  You receive ' . $itemname . '!</p>
          <p>You have ' . $game['tries'] . ' strike' . ($game['tries'] != 1 ? 's' : '') . ' remaining.</p>
        ';
      }
    }
    else if($game['flipped'] == 0)
    {
      $message = '
        <p>Hm!  Not a pair!</p>
        <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '">Flip the mismatched cards back down to continue.</a></li></ul>
      ';
      $link = false;

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Mismatching Cards Revealed in Magic Cards', 1);
    }
  }
}

if($all_done)
{
  echo $message;

  render_card_game($game, false);

  delete_inventory_byid($this_inventory['idnum']);

  $AGAIN_WITH_ANOTHER = true;
}
else if($game['tries'] > 0)
{
  echo $message;

  render_card_game($game, $link);
}
else
{
  echo '
    <p>Hm!  Not a pair!</p>
    <p>Unfortunately, you\'re out of strikes!  (The ' . $this_inventory['itemname'] . ' vanishes!)  But don\'t worry: your progress has been saved for next time!</p>
  ';

  render_card_game($game, false);

  delete_inventory_byid($this_inventory['idnum']);
  
  reset_card_game_tries($user['idnum']);
  
  $AGAIN_WITH_ANOTHER = true;
}
?>
