<?php
require_once 'commons/spamchecker.php';

function is_troll($text)
{
  $bayesian_filter = new spamchecker();

  return($bayesian_filter->checkSpam($text) > .90);
}

function train_as_trolling($text)
{
  $bayesian_filter = new spamchecker();

  $bayesian_filter->train($text, true);
}

function create_troll_report($postid, $userid, $text)
{
  global $now;

  $command = '
    INSERT INTO psypets_possible_trolling (timestamp, postid, userid, text)
    VALUES (' . $now . ', ' . $postid . ', ' . $userid . ', ' . quote_smart($text) . ')
  ';
  fetch_none($command, 'logging statistics');
}
?>
