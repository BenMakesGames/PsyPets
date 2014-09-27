<?php
$LAKE_ADDONS = array(
  'fountain', 'waterfall', 'tunnel', 'monster'
);

function get_lake_byuser($userid)
{
  $command = 'SELECT * FROM psypets_lakes WHERE userid=' . $userid . ' LIMIT 1';
  $lake = fetch_single($command, 'fetching lake');

  return $lake;
}

function create_lake($userid)
{
  $command = 'INSERT INTO psypets_lakes (userid) VALUES (' . $userid . ')';
  fetch_none($command, 'creating lake');
}

function monster_description(&$pet)
{
  $total = $pet['bra'] + $pet['str'] + $pet['sta'] - (10 - $pet['independent']);

  if($total > 30)
    return 'terrifying';
  else if($total > 25)
    return 'imposing';
  else if($total > 20)
    return 'menacing';
  else if($total > 15)
    return 'alarming';
  else if($total > 10)
    return 'there';
  else if($total > 5)
    return 'timid';
  else
    return 'pansy';
}

function lake_play_value($userid)
{
  $safety = 0;
  $love = 0;
  $esteem = 0;

  $lake = get_lake_byuser($userid);
  
  if($lake['boats'] != '')
  {
    $boats = explode(',', $lake['boats']);
    
    foreach($boats as $boat)
    {
      if($boat == 'Swan Boat')
        $love++;
      else if($boat == 'Black Swan Boat')
        $esteem++;
      else if($boat == 'Small Greek Trireme')
      {
        $esteem++;
        $safety++;
      }
      else
        $safety++;
    }
  }
  
  return array($safety, $love, $esteem);
}
?>
