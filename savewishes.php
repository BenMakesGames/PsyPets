<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

/*
if($user['user'] == 'telkoth')
  echo $_GET['list'] . '<br />';
*/
$list = take_apart('|', $_GET['list']);

$votes_for = 0;
$votes_against = 0;
$group = 1;

foreach($list as $item)
{
  $id = substr($item, 1);
  if($id == 'break1' || $id == 'break2')
    $group--;
  else if($group == 1)
    $votes_for++;
  else if($group == -1)
    $votes_against++;
}

$total_votes_for = ($votes_for * ($votes_for + 1)) / 2;
$total_votes_against = ($votes_against * ($votes_against + 1)) / 2;

$group = 1;
$for_points = $votes_for;
$against_points = 1;

foreach($list as $item)
{
  $id = substr($item, 1);
  if($id == 'break1' || $id == 'break2')
    $group--;
  else if($group == 1)
  {
    $votes[$id] = round($for_points * 1000 / $total_votes_for);
    $for_points--;
  } 
  else if($group == -1)
  {
    $votes[$id] = -round($against_points * 1000 / $total_votes_against);
    $against_points++;
  }
  else
    $votes[$id] = 0;
}

$command = 'SELECT idnum,sdesc FROM psypets_ideachart';
$wishes = $database->FetchMultipleBy($command, 'idnum', 'fetching wishes');
/*
if($user['user'] == 'telkoth')
{
  echo '<pre>';
  print_r($votes);
  echo '</pre>';
}
*/
$command = 'SELECT ideaid,votes FROM psypets_ideavotes WHERE residentid=' . $user['idnum'];
$my_votes = $database->FetchMultipleBy($command, 'ideaid', 'fetching my votes'); 

foreach($votes as $id=>$points)
{
  if(array_key_exists($id, $wishes))
  {
    if($points == 0)
    {
      $command = 'DELETE FROM psypets_ideavotes WHERE residentid=' . $user['idnum'] . ' AND ideaid=' . $id . ' LIMIT 1';
      $database->FetchNone($command, 'removing vote');
    }
    else
    {
      if(array_key_exists($id, $my_votes))
      {
        if($my_votes[$id]['votes'] != $points)
        {
          $command = 'UPDATE psypets_ideavotes SET votes=' . $points . ' WHERE residentid=' . $user['idnum'] . ' AND ideaid=' . $id . ' LIMIT 1';
          $database->FetchNone($command, 'updating vote');
        }
      }
      else
      {
/*
        if($user['user'] == 'telkoth')
          echo 'Wish #' . $id . ' had no votes.<br />';
*/
        $command = 'INSERT INTO psypets_ideavotes (ideaid, residentid, votes) VALUES ' .
                   '(' . $id . ', ' . $user['idnum'] . ', ' . $points . ')';
        $database->FetchNone($command, 'adding vote');
      }
    }
  } // if the wish exists
/*
  else if($user['user'] == 'telkoth')
  {
    echo 'Wish #' . $id . ' does not exist.<br />';
  }
*/
}
/*
if($user['user'] == 'telkoth')
  exit();
*/
header('Location: ./arrangewishes.php');
?>
