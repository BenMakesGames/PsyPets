<?php
$TRANSMUTATION_CACHED_BY_NAME = array();

function get_transmutation_byid($idnum)
{
  $command = 'SELECT * FROM psypets_alchemy WHERE idnum=' . $idnum . ' LIMIT 1';
  $transmutation = fetch_single($command, 'fetching transmutation by id');

  return $transmutation;
}

function get_transmutations_byitem($name)
{
  global $TRANSMUTATION_CACHED_BY_NAME;

  if(!array_key_exists($name, $TRANSMUTATION_CACHED_BY_NAME))
  {
    $command = 'SELECT * FROM psypets_alchemy WHERE item_in=' . quote_smart($name);
    $alchemies = fetch_multiple($command);

    if(count($alchemies) == 0)
      $TRANSMUTATION_CACHED_BY_NAME[$name] = false;
    else
    {
      foreach($alchemies as $alchemy)
        $TRANSMUTATION_CACHED_BY_NAME[$name][] = $alchemy;
    }
  }

  return $TRANSMUTATION_CACHED_BY_NAME[$name];
}

function get_transmutations()
{
  $command = 'SELECT * FROM psypets_alchemy ORDER BY `type` ASC,item_out ASC';
  $transmutations = fetch_multiple($command, 'fetching transmutations');
  
  return $transmutations;
}
?>
