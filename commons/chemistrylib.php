<?php
$PERIODIC_TABLE = array(
  1 => 'Hydrogen',
  6 => 'Small Giamond',
  7 => 'Liquid Nitrogen',
  8 => 'Zephrous',
  26 => 'Iron',
  29 => 'Copper',
  30 => 'Zinc',
  47 => 'Silver',
  50 => 'Tin',
  78 => 'Platinum',
  79 => 'Gold',
  80 => 'Mercury',
  92 => 'Radioactive Material',
);

function fuse_elements($el1, $el2)
{
  global $PERIODIC_TABLE;
  
  $by_element = array_flip($PERIODIC_TABLE);
  
  if(!array_key_exists($el1, $by_element) || !array_key_exists($el2, $by_element))
    return false;
  else
  {
    $new_mass = $by_element[$el1] + $by_element[$el2];
    
    if(array_key_exists($new_mass, $PERIODIC_TABLE))
      return $PERIODIC_TABLE[$new_mass];
    else
      return false;
  }
}

function get_powerplant($userid)
{
  $powerplant = fetch_single('SELECT * FROM psypets_nuclear_power_plants WHERE userid=' . (int)$userid . ' LIMIT 1');
  if($powerplant === false)
  {
    fetch_none('INSERT INTO psypets_nuclear_power_plants (userid) VALUES (' . (int)$userid . ')');
    
    return fetch_single('SELECT * FROM psypets_nuclear_power_plants WHERE userid=' . (int)$userid . ' LIMIT 1');
  }
}
?>
