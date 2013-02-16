<?php
function get_user_profile_text($userid)
{
  $command = 'SELECT * FROM psypets_profile_text WHERE player_id='. $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching profile text');
}

function create_user_profile($userid, $text)
{
  $command = '
    INSERT INTO psypets_profile_text (player_id, text, last_update)
    VALUES (' . $userid . ', ' . quote_smart($text) . ', ' . time() . ')
  ';
  fetch_none($command, 'creating profile text');
}

function update_user_profile($userid, $text)
{
  $command = '
    UPDATE psypets_profile_text SET text=' . quote_smart($text) . ',
    last_update=' . time() . ' WHERE player_id=' . $userid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating profile text');
}

function get_display_items(&$this_user)
{
  // alternative join:
  /*
      monster_inventory AS a
      LEFT JOIN monster_items AS b
        ON a.itemname=b.itemname
        LEFT JOIN psypets_profile_treasures AS c
          ON b.idnum=c.itemid
  */

  return fetch_multiple('
    SELECT a.*,b.graphic,b.graphictype,a.message,a.message2,COUNT(a.idnum) AS qty,c.ranking
    FROM
      psypets_profile_treasures AS c
      LEFT JOIN monster_items AS b
        ON c.itemid=b.idnum
        LEFT JOIN monster_inventory AS a
          ON b.itemname=a.itemname
    WHERE
      c.userid=' . (int)$this_user['idnum'] . ' AND
      c.ranking>0 AND
      a.user=' . quote_smart($this_user['user']) . ' AND
      a.location LIKE \'home%\'
    GROUP BY a.itemname
    ORDER BY c.ranking DESC
    LIMIT 141
  ');
}

function get_display_items_as_hoard(&$this_user)
{
  // alternative join:
  /*
      monster_inventory AS a
      LEFT JOIN monster_items AS b
        ON a.itemname=b.itemname
        LEFT JOIN psypets_profile_treasures AS c
          ON b.idnum=c.itemid
  */

  return fetch_multiple('
    SELECT a.*,b.graphic,b.graphictype,a.message,a.message2,c.ranking
    FROM
      psypets_profile_treasures AS c
      LEFT JOIN monster_items AS b
        ON c.itemid=b.idnum
        LEFT JOIN monster_inventory AS a
          ON b.itemname=a.itemname
    WHERE
      c.userid=' . (int)$this_user['idnum'] . ' AND
      c.ranking>0 AND
      a.user=' . quote_smart($this_user['user']) . ' AND
      a.location LIKE \'home%\'
    ORDER BY c.ranking DESC
  ');
}
?>
