<?php
function get_totem_byuserid($userid)
{
  $command = 'SELECT * FROM psypets_totempoles WHERE userid=' . $userid . ' LIMIT 1';
  $totem = fetch_single($command, 'totem library > fetch resident totem');
  
  return $totem;
}

function delete_totem_byuserid($userid)
{
  $command = 'DELETE FROM psypets_totempoles WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'totem library > delete resident totem');
}

function create_totem($userid, $firstpiece)
{
  $command = 'INSERT INTO psypets_totempoles (userid, totem, last_add) VALUES (' . $userid . ', \'' . str_replace("'", '', $firstpiece) . '\', ' . time() . ')';
  fetch_none($command, 'totem library > create totem');
}

function add_to_totem_byuserid($userid, $piece)
{
  $command = 'UPDATE psypets_totempoles SET totem=CONCAT(totem,' . quote_smart(',' . $piece) . '), last_add=' . time() . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'totem library > add totem to resident totem pole');
}

function replace_totem_byuserid($userid, $pieces)
{
  if(count($pieces) == 0)
    $command = 'UPDATE psypets_totempoles SET last_add=0,rating=0, totem=\'\' WHERE userid=' . $userid . ' LIMIT 1';
  else
  {
    $score = totem_score($pieces);
    $command = 'UPDATE psypets_totempoles SET last_add=0,rating=' . $score . ', totem=\'' . str_replace("'", '', implode(',', $pieces)) . '\' WHERE userid=' . $userid . ' LIMIT 1';
  }

  fetch_none($command, 'totem library > build resident totem pole');
}

function get_num_totems()
{
  $command = 'SELECT COUNT(*) AS c FROM psypets_totempoles';
  $data = fetch_single($command, 'fetching totem pole count');
  
  return $data['c'];
}

function get_totems_byscore($first, $num)
{
  $command = 'SELECT idnum,userid,rating,totem FROM psypets_totempoles ORDER BY rating DESC LIMIT ' . $first . ',' . $num;
  $poles = fetch_multiple($command, 'totem library > fetch totems by rating');
  
  return $poles;
}

function set_totem_score($userid, $score)
{
  $command = 'UPDATE psypets_totempoles SET rating=' . $score . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'setting totem score');
}

function totem_score($totems)
{
  $totem_count = array();
  $score = 0;
  $height = count($totems);

  foreach($totems as $totem)
  {
    // count how many of each totem the pole contains
    $totem_count[$totem]++;
    
    $score += max(11 - $totem_count[$totem], 1);
  }

  return $score;
}

function totem_rating($score, $article = false)
{
  if($article === true)
  {
    $a = 'a ';
    $an = 'an ';
  }
  else
  {
    $a = '';
    $an = '';
  }

  if($score >= 700)
    return $a . 'legendary';
  else if($score >= 600)
    return $an . 'amazing';
  else if($score >= 450)
    return $a . 'great';
  else if($score >= 300)
    return $a . 'good';
  else if($score >= 150)
    return $an . 'average';
  else if($score >= 50)
    return $a . 'below-average';
  else
    return $a . 'poor';
}
?>
