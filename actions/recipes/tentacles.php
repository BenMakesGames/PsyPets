<?php
if($okay_to_be_here !== true)
  exit();

$command = 'SELECT * FROM monster_recipes WHERE ingredients LIKE \'%Tentacle%\'';
$recipes = $database->FetchMultiple($command, 'item: ' . $this_inventory['itemname']);

echo '<h5>Recipes</h5>';

foreach($recipes as $recipe)
{
  $makes = explode(',', $recipe['makes']);
  $ingredients = explode(',', $recipe['ingredients']);

  echo '<h6>' . $makes[0] . '</h6>';
  echo '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';
}

echo '<h5>Crafts</h5>';

$tables = array('psypets_bindings', 'psypets_carpentry', 'psypets_chemistry', 'psypets_crafts',
  'psypets_inventions', 'psypets_jewelry', 'psypets_mechanics', 'psypets_paintings',
  'psypets_sculptures', 'psypets_smiths', 'psypets_tailors'
);

$command = 'SELECT makes,ingredients FROM ' . implode(' WHERE ingredients LIKE \'%Tentacle%\' UNION SELECT makes,ingredients FROM ', $tables) . ' WHERE ingredients LIKE \'%Tentacle%\'';
$recipes = $database->FetchMultiple($command, 'fetching crafts');

foreach($recipes as $recipe)
{
  $ingredients = explode(',', $recipe['ingredients']);
  sort($ingredients);

  echo '<h6>' . $recipe['makes'] . '</h6>';
  echo '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';
}

?>
