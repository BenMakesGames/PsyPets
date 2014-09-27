<?php
function get_fireplace_byuser($userid, $locid = 0)
{
  $command = "SELECT * FROM psypets_fireplaces WHERE userid=$userid LIMIT 1";
  $fireplace = fetch_single($command, 'feting fireplace');

  return $fireplace;
}

function create_fireplace($userid, $locid = 0)
{
  $command = "INSERT INTO psypets_fireplaces (userid) VALUES ($userid)";
  fetch_none($command, 'creating fireplace');
}

// odd function.  takes an array of items as returned from
// fetch_multiple_by(..., 'idnum', ...)
// and a list of idnums
// and rearranges the array so that the items appear in the order that they
// appear in the list of idnums.  items not mentioned in the idnum list are
// tacked on at the end

function sort_items_by_mantle(&$items, $idnums)
{
  $newlist = array();
  
  foreach($idnums as $idnum)
  {
    if(array_key_exists($idnum, $items))
    {
      $newlist[$idnum] = $items[$idnum];
      unset($items[$idnum]);
    }
  }
  
  if(count($items) > 0)
  {
    foreach($items as $idnum=>$item)
      $newlist[$idnum] = $item;
  }
  
  $items = $newlist;
}

function log_fireplace_event($time, $userid, $event)
{
  $command = 'INSERT INTO psypets_fireplace_log (userid, timestamp, event) VALUES ' .
    '(' . $userid . ', ' . $time . ', ' . quote_smart($event) . ')';
  fetch_none($command, 'logging fireplace event');
}
?>