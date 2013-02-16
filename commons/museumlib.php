<?php
function add_item_to_museum($itemid, $userid, $double_check = true)
{
  if($double_check === true)
  {
    $command = 'SELECT * FROM psypets_museum WHERE itemid=' . $itemid . ' AND userid=' . $userid . ' LIMIT 1';
    $item = fetch_single($command, 'fetching existing museum item');

    if($item !== false)
      return false;
  }

  global $now;

  $command = 'INSERT INTO psypets_museum (timestamp, itemid, userid) VALUES ' .
    '(' . $now . ', ' . $itemid . ', ' . $userid . ')';
  fetch_none($command, 'adding museum item');
    
  return true;
}

function get_user_museum_count($userid)
{
  $command = 'SELECT COUNT(*) AS c FROM psypets_museum WHERE userid=' . $userid;
  $data = fetch_single($command, 'fetching museum item count');
  return (int)$data['c'];
}

function get_user_museum_page_by_time($userid, $page)
{
  $command = 'SELECT a.itemid,a.timestamp,b.itemname,b.graphic,b.graphictype FROM psypets_museum AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum WHERE a.userid=' . $userid . ' ORDER BY a.timestamp DESC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching museum by page');
}

function get_user_museum_page($userid, $page)
{
  $command = 'SELECT a.itemid,a.timestamp,b.itemname,b.graphic,b.graphictype FROM psypets_museum AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum WHERE a.userid=' . $userid . ' ORDER BY b.itemname ASC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching museum by page');
}

function get_museum_item($userid, $itemid)
{
  $command = 'SELECT * FROM psypets_museum WHERE userid=' . $userid . ' AND itemid=' . $itemid . ' LIMIT 1';
  return fetch_single($command, 'fetching single museum item');
}

function get_museum_item_count($itemid)
{
  $command = 'SELECT COUNT(*) AS c FROM psypets_museum WHERE itemid=' . $itemid;
  $data = fetch_single($command, 'fetching item count');

  return (int)$data['c'];
}

function get_museum_item_donators($itemid, $page)
{
  $command = 'SELECT a.userid,a.timestamp,b.display,b.museumcount FROM psypets_museum AS a LEFT JOIN monster_users AS b ON a.userid=b.idnum WHERE a.itemid=' . $itemid . ' ORDER BY b.display ASC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching item donators');
}

function get_museum_wing_count($friend_list = false)
{
	if(is_array($friend_list))
	{
		$data = fetch_single('
			SELECT COUNT(*) AS c
			FROM monster_users
			WHERE
				museumcount>=100
				AND idnum IN (' . implode(',', $friend_list) . ')
			LIMIT ' . count($friend_list) . '
		');
	}
	else
		$data = fetch_single('SELECT COUNT(*) AS c FROM monster_users WHERE museumcount>=100');

  return (int)$data['c'];
}

function update_museum_count($userid)
{
  $count = get_user_museum_count($userid);
  
  $command = 'UPDATE monster_users SET museumcount=' . $count . ' WHERE idnum=' . $userid . ' LIMIT 1';
  fetch_none($command, 'updating user museum count');
}

function get_user_unmuseum_count($userid)
{
  $command = 'SELECT COUNT(table1.idnum) AS c FROM monster_items aS table1 LEFT JOIN psypets_museum AS table2 ON (table1.idnum = table2.itemid AND table2.userid=' . $userid . ') WHERE table2.itemid IS NULL AND table1.custom=\'no\' ORDER BY table1.itemname ASC';
  $data = fetch_single($command, 'fetching user\'s unmuseum item count');
  return (int)$data['c'];
}

function get_user_unmuseum_page($userid, $page)
{
  $command = 'SELECT table1.itemname,table1.graphictype,table1.graphic,table1.idnum AS itemid FROM monster_items aS table1 LEFT JOIN psypets_museum AS table2 ON (table1.idnum = table2.itemid AND table2.userid=' . $userid . ') WHERE table2.itemid IS NULL AND table1.custom=\'no\' ORDER BY table1.itemname ASC LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching user\'s unmuseum, page ' . $page);
}
?>
