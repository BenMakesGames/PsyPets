<?php
function user_can_vote($userid, $min_date, $max_date)
{
  $command = '
    SELECT idnum
    FROM psypets_favor_history
    WHERE
      userid=' . $userid . '
      AND timestamp>=' . $min_date . '
      AND timestamp<=' . $max_date . '
    LIMIT 1
  ';
  $data = fetch_single($command, 'fetching sample payment');
  
  return($data !== false);
}

function get_feature_drive_suggestions($comicid)
{
  $command = 'SELECT vote,COUNT(residentid) AS total FROM psypets_feature_drive GROUP BY vote ORDER BY total DESC';
  $data = fetch_multiple($command, 'fetching feature drive votes');

  return $data;
}

function get_voter_feature_drive_suggestion($voterid)
{
  $command = 'SELECT vote FROM psypets_feature_drive WHERE residentid=' . (int)$voterid . ' LIMIT 1';
  $vote = fetch_single($command, 'fetching vote for resident #' . $voterid . ' for feature drive');

  return $vote;
}

function delete_feature_drive_suggestion($voterid)
{
  $command = 'DELETE FROM psypets_feature_drive WHERE residentid=' . (int)$voterid . ' LIMIT 1';
  fetch_none($command, 'deleting vote for feature drive');
}

function make_feature_drive_suggestion($suggestion, $voterid)
{
  $command = 'INSERT INTO psypets_feature_drive (vote, residentid) VALUES (' .
    quote_smart($suggestion) . ', ' . (int)$voterid . ')';
  fetch_none($command, 'recording suggestion by resident #' . $voterid . ' for feature drive');
}

function get_feature_drive_suggestion_details($comicid)
{
  $command = 'SELECT vote,residentid FROM psypets_feature_drive ORDER BY vote';
  $data = fetch_multiple($command, 'fetching detailed votes for feature drive');

  return $data;
}
?>
