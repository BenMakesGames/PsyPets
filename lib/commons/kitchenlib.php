<?php
function get_total_recipes()
{
  $data = fetch_single('
    SELECT COUNT(*) AS qty
    FROM monster_recipes
    WHERE
			availability=\'standard\'
			AND machine_only=\'no\'
  ');

  return $data['qty'];
}

function get_known_standard_recipes_count($userid)
{
  $data = fetch_single('
    SELECT COUNT(a.idnum) AS qty
    FROM
      psypets_known_recipes AS a
      LEFT JOIN monster_recipes AS b
        ON a.recipeid=b.idnum
    WHERE
      a.userid=' . $userid . ' AND
      b.availability=\'standard\'
  ');

  return $data['qty'];
}

function get_all_known_recipes_count($userid)
{
  $data = fetch_single('
    SELECT COUNT(a.idnum) AS qty
    FROM
      psypets_known_recipes AS a
      LEFT JOIN monster_recipes AS b
        ON a.recipeid=b.idnum
    WHERE a.userid=' . $userid . '
  ');

  return $data['qty'];
}

function get_known_recipes($userid, $page)
{
  return fetch_multiple('
    SELECT a.learned_on,a.times_prepared,a.favorite,b.*
    FROM
      psypets_known_recipes AS a
      LEFT JOIN monster_recipes AS b
        ON a.recipeid=b.idnum
    WHERE a.userid=' . $userid . '
    LIMIT ' . (($page - 1) * 20) . ',20
  ');
}

function search_known_recipes_count($userid, $search)
{
  $q_search = quote_smart('%' . $search . '%');

  $data = fetch_single('
    SELECT COUNT(a.idnum) AS qty
    FROM
      psypets_known_recipes AS a
      LEFT JOIN monster_recipes AS b
        ON a.recipeid=b.idnum
    WHERE
      a.userid=' . $userid . '
      AND (b.ingredients LIKE ' . $q_search . ' OR b.makes LIKE ' . $q_search . ')
  ');

  return $data['qty'];
}

function search_known_recipes($userid, $search, $page)
{
  $q_search = quote_smart('%' . $search . '%');

  return fetch_multiple('
    SELECT a.learned_on,a.times_prepared,a.favorite,b.*
    FROM
      psypets_known_recipes AS a
      LEFT JOIN monster_recipes AS b
        ON a.recipeid=b.idnum
    WHERE
      a.userid=' . $userid . '
      AND (b.ingredients LIKE ' . $q_search . ' OR b.makes LIKE ' . $q_search . ')
    LIMIT ' . (($page - 1) * 20) . ',20
  ');
}

function record_known_recipe($userid, $recipeid, $quantity = 1)
{
  $existing_recipe = fetch_single('SELECT idnum FROM psypets_known_recipes WHERE userid=' . $userid . ' AND recipeid=' . $recipeid . ' LIMIT 1');
  
  if($existing_recipe === false)
    fetch_none('INSERT INTO psypets_known_recipes (userid, recipeid, learned_on, times_prepared) VALUES (' . $userid . ', ' . $recipeid . ', ' . time() . ', ' . $quantity . ')');
  else
    fetch_none('UPDATE psypets_known_recipes SET times_prepared=times_prepared+' . $quantity . ' WHERE idnum=' . $existing_recipe['idnum']);
}
?>