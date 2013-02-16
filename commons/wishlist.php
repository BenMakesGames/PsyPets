<?php
function get_todo_list_vote($itemid, $userid)
{
  $command = 'SELECT * FROM psypets_ideavotes WHERE ideaid=' . $itemid . ' AND residentid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching todo list vote');
}

function delete_todo_list_vote($itemid, $userid)
{
  $command = 'DELETE FROM psypets_ideavotes WHERE ideaid=' . $itemid . ' AND residentid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'deleting todo list vote');
}

function update_todo_list_vote($itemid, $userid, $new_vote)
{
  $command = '
    UPDATE psypets_ideavotes
    SET votes=' . $new_vote . '
    WHERE ideaid=' . $itemid . ' AND residentid=' . $userid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating todo list vote');
}

function create_todo_list_vote($itemid, $userid, $new_vote)
{
  $command = '
    INSERT INTO psypets_ideavotes
    (ideaid, residentid, votes)
    VALUES
    (' . $itemid . ', ' . $userid . ', ' . $new_vote . ')
  ';
  fetch_none($command, 'creating todo list vote');
}
?>