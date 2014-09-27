<?php
function get_poll_byid($id)
{
  $command = 'SELECT * FROM psypets_polls WHERE idnum=' . $id . ' LIMIT 1';
  $poll = fetch_single($command, 'fetching poll');

  return $poll;
}

function cast_vote($pollid, $userid, $ip, $votenum)
{
  $command = 'INSERT INTO psypets_poll_votes (pollid, residentid, ip, vote) VALUES ' .
    '(' . $pollid . ', ' . $userid . ', ' . quote_smart($ip) . ', ' . $votenum . ')';
  fetch_none($command, 'registering vote');
}

function create_poll($title, $description, $options)
{
  $command = 'INSERT INTO psypets_polls (title, description, options) VALUES ' .
             '(' . quote_smart($title) . ', ' . quote_smart($description) . ', ' . quote_smart(implode('|', $options)) . ')';
  fetch_none($command, 'creating poll');
  
  $id = $GLOBALS['database']->InsertID();
  
  return $id;
}

function get_poll_results($pollid, $vote)
{
  $command = 'SELECT COUNT(*) AS c FROM psypets_poll_votes WHERE pollid=' . $pollid . ' AND vote=' . $vote;
  $data = fetch_single($command, 'fetching poll results (1)');
  
  return $data['c'];
}

function get_poll_results_byid($pollid)
{
  $command = 'SELECT * FROM psypets_poll_votes WHERE pollid=' . $pollid;
  $votes = fetch_multiple($command, 'fetching poll results (2)');
  
  return $votes;
}

function get_polls()
{
  $command = 'SELECT * FROM psypets_polls ORDER BY idnum DESC';
  $polls = fetch_multiple($command, 'fetching all polls');

  return $polls;
}
?>
