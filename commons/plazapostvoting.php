<?php
function get_post_vote($postid, $userid)
{
  $command = 'SELECT * FROM psypets_post_thumbs WHERE postid=' . $postid . ' AND voterid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching resident\'s post vote');
}

function delete_post_vote($postid, $userid)
{
  $command = 'DELETE FROM psypets_post_thumbs WHERE postid=' . $postid . ' AND voterid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'deleting resident\'s post vote');
}

function create_post_vote($postid, $userid, $vote)
{
  $command = '
    INSERT INTO psypets_post_thumbs
    (postid, voterid, vote, timestamp)
    VALUES
    (' . $postid . ', ' . $userid . ', ' . $vote . ', ' . time() . ')
  ';
  fetch_none($command, 'creating resident\'s post vote');
}

function update_post_vote($postid, $userid, $vote)
{
  $command = '
    UPDATE psypets_post_thumbs
    SET vote=' . $vote . ',timestamp=' . time() . '
    WHERE
    postid=' . $postid . ' AND voterid=' . $userid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating resident\'s post vote');
}
?>
