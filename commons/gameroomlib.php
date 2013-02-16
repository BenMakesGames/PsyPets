<?php
$GAME_ROOM_TICKETS = array('cost_blue' => 'Blue Ticket', 'cost_green' => 'Green Ticket', 'cost_yellow' => 'Yellow Ticket', 'cost_red' => 'Red Ticket', 'cost_foiled' => 'Foiled Ticket');

function get_game_room($userid, $create_if_doesnt_exist = false)
{
  $command = 'SELECT * FROM psypets_game_rooms WHERE userid=' . $userid . ' LIMIT 1';
  $game_room = fetch_single($command, 'fetching game room');
  
  if($game_room === false && $create_if_doesnt_exist)
  {
    $command = 'INSERT INTO psypets_game_rooms (userid) VALUES (' . $userid . ')';
    fetch_none($command, 'creating game room');

    $command = 'SELECT * FROM psypets_game_rooms WHERE userid=' . $userid . ' LIMIT 1';
    $game_room = fetch_single($command, 'fetching game room');

    if($game_room === false)
      die('Failed to create new game room!');
  }
  
  return $game_room;
}

function get_game_room_games($userid)
{
  $command = '
    SELECT b.*
    FROM
      psypets_game_room_games AS a
      LEFT JOIN psypets_arcadegames AS b
        ON a.gameid=b.idnum
    WHERE
      a.userid=' . $userid . '
    ORDER BY
      b.name ASC
  ';
  return fetch_multiple_by($command, 'idnum', 'fetching conquered games');
}

function add_game_room_game($userid, $gameid)
{
  $command = '
    INSERT INTO psypets_game_room_games
    (userid, gameid)
    VALUES
    (' . $userid . ', ' . $gameid . ')
  ';
  fetch_none($command, 'adding conquered game');
}

function charge_game_room($userid, $money)
{
  $command = 'UPDATE psypets_game_rooms SET money=money-' . $money . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'charging game room moneys');
}

function credit_game_room($userid, $money)
{
  $command = 'UPDATE psypets_game_rooms SET money=money+' . $money . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'crediting game room moneys');
}

function get_total_game_count()
{
  $command = 'SELECT COUNT(idnum) AS c FROM psypets_arcadegames';
  $data = fetch_single($command, 'fetching game count');
  
  return $data['c'];
}

function get_game_room_tickets(&$user)
{
  global $GAME_ROOM_TICKETS;

  $command = '
    SELECT a.itemname,COUNT(a.idnum) AS qty,b.graphic,b.graphictype
    FROM
      monster_inventory AS a
      LEFT JOIN monster_items AS b
        ON a.itemname=b.itemname
    WHERE
      a.user=' . quote_smart($user['user']) . '
      AND a.itemname IN (\'' . implode('\',\'', $GAME_ROOM_TICKETS) . '\')
      AND a.location LIKE \'home%\'
      AND a.location NOT LIKE \'home/$%\'
    GROUP BY a.itemname
  ';

  return fetch_multiple_by($command, 'itemname', 'fetching tickets');
}
?>
