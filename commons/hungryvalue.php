<?php
function generic_craft_value($itemname)
{
  $q_item = quote_smart($itemname);

  $command = '
    SELECT MIN(d) AS average_level FROM
    (
      ( SELECT MIN(difficulty) AS d FROM psypets_crafts WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_inventions WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_smiths WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_tailors WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_carpentry WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_chemistry WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_jewelry WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_mechanics WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_paintings WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_sculptures WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_bindings WHERE makes=' . $q_item . ' )
      UNION
      ( SELECT MIN(difficulty) AS d FROM psypets_leatherworks WHERE makes=' . $q_item . ' )
    )
    AS all_crafts
  ';
  
  $data = fetch_single($command, 'fetching average craft level');
  
  return (int)$data['average_level'];
}

function greenhouse_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.5);
}

function cherub_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.5);
}

function sword_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.5);
}

function shovel_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.5);
}

function polygon_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.5);
}

function tapestry_food_value($itemname)
{
  return ceil(generic_craft_value($itemname) / 1.4);
}
?>
