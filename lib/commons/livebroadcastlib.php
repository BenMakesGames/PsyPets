<?php
function get_live_broadcast_suggestions($comicid)
{
  $command = 'SELECT vote FROM psypets_broadcasting_topics GROUP BY vote ORDER BY idnum DESC';
  $data = fetch_multiple($command, 'fetching votes');

  return $data;
}

function get_voter_live_broadcast_suggestion($voterid)
{
  $command = 'SELECT vote FROM psypets_broadcasting_topics WHERE residentid=' . (int)$voterid . ' LIMIT 1';
  $vote = fetch_single($command, 'fetching vote for resident #' . $voterid . ' for live broadcasting');

  return $vote;
}

function get_live_broadcast_suggestion_results()
{
  $command = 'SELECT vote,COUNT(voterid) AS total FROM psypets_broadcasting_topics GROUP BY vote ORDER BY total DESC';
  $data = fetch_multiple($command, 'fetching votes for live broadcasting');

  return $data;
}

function delete_live_broadcast_suggestion($voterid)
{
  $command = 'DELETE FROM psypets_broadcasting_topics WHERE residentid=' . (int)$voterid . ' LIMIT 1';
  fetch_none($command, 'deleting vote for live broadcasting');
}

function make_live_broadcast_suggestion($suggestion, $voterid)
{
  $command = 'INSERT INTO psypets_broadcasting_topics (vote, residentid) VALUES (' .
    quote_smart($suggestion) . ', ' . (int)$voterid . ')';
  fetch_none($command, 'recording suggestion by resident #' . $voterid . ' for live broadcasting');
}

function get_live_broadcast_suggestion_details($comicid)
{
  $command = 'SELECT vote,residentid FROM psypets_broadcasting_topics ORDER BY vote';
  $data = fetch_multiple($command, 'fetching detailed votes for live broadcasting');

  return $data;
}
?>
