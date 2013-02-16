<?php
$PUZZLES = array(
  'jumpditch',
  'needyfellow',
  'sneak',
  'defeat',
  'repair',
  'befriend',
  'disarm',
  'hungryfellow',
  'hunter',
  'botanist',
  'needsmetal',
);

$BONUS_ROOMS = array(
  'fountain', 'obelisk',
);

function get_challenge($userid)
{
  $command = 'SELECT * FROM psypets_dailychallenge WHERE userid=' . (int)$userid . ' LIMIT 1';
  return fetch_single($command, 'fetching resident\'s daily challenge info');
}

function create_challenge($userid)
{
  global $now;

  $command = 'INSERT INTO psypets_dailychallenge (userid) VALUES (' . (int)$userid . ')';
  fetch_none($command, 'initializing resident\'s daily challenge info');
}

function cancel_challenge($userid)
{
  $command = 'UPDATE psypets_dailychallenge SET step=0 WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'canceling daily challenge');
}

function start_challenge($userid, $difficulty)
{
  global $PUZZLES;

  $elements = array();
  
  if($difficulty == 1 || $difficulty == 0)
    $count = 2;
  else if($difficulty == 2)
    $count = 3;
  else if($difficulty == 3 || $difficulty == 4)
    $count = 4;
  else
    return;

  $elements = array_rand($PUZZLES, $count);

  $command = 'UPDATE psypets_dailychallenge SET ' .
               'lastchallenge=\'' . date('Ymd') . '\', ' .
               'difficulty=' . (int)$difficulty . ', ' .
               'puzzle=' . quote_smart(implode(',', $elements)) . ', ' .
               'step=1 ' .
             'WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'starting daily challenge');
}

function failed_challenge($challenge)
{
  if($challenge['difficulty'] == 0)
    return;

  if($challenge['failed'] == 'yes')
    $command = 'UPDATE psypets_dailychallenge SET copper=copper+1,failed=\'no\' WHERE userid=' . $challenge['userid'] . ' LIMIT 1';
  else
    $command = 'UPDATE psypets_dailychallenge SET failed=\'yes\' WHERE userid=' . $challenge['userid'] . ' LIMIT 1';

  fetch_none($command, 'recording adventure failure');
}

function update_challenge($challenge)
{
  $command = 'UPDATE psypets_dailychallenge SET ' .
               'step=' . $challenge['step'] . ', ' .
               'plastic=' . $challenge['plastic'] . ', ' .
               'copper=' . $challenge['copper'] . ', ' .
               'silver=' . $challenge['silver'] . ', ' .
               'gold=' . $challenge['gold'] . ', ' .
               'platinum=' . $challenge['platinum'] . ' ' .
             'WHERE userid=' . $challenge['userid'] . ' LIMIT 1';
  fetch_none($command, 'updating challenge progress');
}

function puzzle_post($challenge)
{
  global $PUZZLES, $user, $message;

  $puzzle = explode(',', $challenge['puzzle']);

  $this_puzzle = $puzzle[$challenge['step'] - 1];

  $success = false;

  require 'puzzles/' . $PUZZLES[$this_puzzle] . '_post.php';

  return $success;
}

function render_puzzle($challenge)
{
  global $PUZZLES, $user;

  $puzzle = explode(',', $challenge['puzzle']);
  
  $this_puzzle = $puzzle[$challenge['step'] - 1];

  require 'puzzles/' . $PUZZLES[$this_puzzle] . '.php';
}
?>
