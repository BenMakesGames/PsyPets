<?php
function get_notes_sort_bycategory($userid)
{
  $command = 'SELECT idnum,timestamp,modifiedon,icon,category,title FROM psypets_notes WHERE userid=' . $userid . ' ORDER BY category ASC';
  return fetch_multiple($command, 'fetcthing notes by category');
}

function get_notes_sort_bytitle($userid)
{
  $command = 'SELECT idnum,timestamp,modifiedon,icon,category,title FROM psypets_notes WHERE userid=' . $userid . ' ORDER BY title ASC';
  return fetch_multiple($command, 'fetcthing notes by category');
}

function get_notes_sort_bycreation($userid)
{
  $command = 'SELECT idnum,timestamp,modifiedon,icon,category,title FROM psypets_notes WHERE userid=' . $userid . ' ORDER BY idnum DESC';
  return fetch_multiple($command, 'fetcthing notes by timestamp');
}

function get_notes_sort_bymodification($userid)
{
  $command = 'SELECT idnum,timestamp,modifiedon,icon,category,title FROM psypets_notes WHERE userid=' . $userid . ' ORDER BY modifiedon DESC,idnum DESC';
  return fetch_multiple($command, 'fetcthing notes by timestamp');
}

function get_note_byid($idnum)
{
  $command = 'SELECT * FROM psypets_notes WHERE idnum=' . $idnum . ' LIMIT 1';
  return fetch_single($command, 'fetching note #' . $idnum);
}

function save_note($idnum, $icon, $category, $title, $body)
{
  $command = 'UPDATE psypets_notes SET modifiedon=' . time() . ',icon=' . quote_smart($icon) . ',' .
             'category=' . quote_smart($category) . ',title=' . quote_smart($title) . ',' .
             'body=' . quote_smart($body) . ' WHERE idnum=' . $idnum . ' LIMIT 1';
  fetch_none($command, 'saving note #' . $idnum);
}

function new_note($userid, $icon, $category, $title, $body)
{
  $command = 'INSERT INTO psypets_notes (userid,timestamp,icon,category,title,body) VALUES ' .
             '(' . $userid . ',' . time() . ',' . quote_smart($icon) . ',' .
             quote_smart($category) . ',' . quote_smart($title) . ',' .
             quote_smart($body) . ')';

  fetch_none($command, 'creating new note');
  
  return $GLOBALS['database']->InsertID();
}

function delete_note_byid($idnum)
{
  $command = 'DELETE FROM psypets_notes WHERE idnum=' . $idnum . ' LIMIT 1';
  fetch_none($command, 'deleting note #' . $idnum);
}

function detel_notes_byuser($userid)
{
  $command = 'DELETE FROM psypets_notes WHERE userid=' . $userid;
  fetch_none($command, 'deleting all of resident\'s notes');
}
?>