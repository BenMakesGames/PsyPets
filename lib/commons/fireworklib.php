<?php
function get_firework_supply(&$user)
{
  if($user['fireworks'] == '')
    return array();

  $fireworks = explode(',', $user['fireworks']);

  foreach($fireworks as $firework)
  {
    list($id, $qty) = explode(':', $firework);
    $supply[$id] = $qty;
  }
  
  return $supply;
}

function render_firework_data_string(&$supply)
{
  $fireworks = array();

  foreach($supply as $id=>$qty)
    $fireworks[] = $id . ':' . $qty;

  return implode(',', $fireworks);
}

function expend_firework(&$supply, $fireworkid)
{
  if($supply[$fireworkid] > 1)
    $supply[$fireworkid]--;
  else
    unset($supply[$fireworkid]);
}

function gain_firework(&$supply, $fireworkid, $quantity = 1)
{
  $supply[$fireworkid] += $quantity;
}
?>
