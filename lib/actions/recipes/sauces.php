<?php
if($okay_to_be_here !== true)
  exit();

$command = 'SELECT * FROM monster_recipes WHERE makes LIKE \'% Sauce\' ORDER BY makes';
$recipes = $database->FetchMultiple($command, 'item: ' . $this_inventory['itemname']);

foreach($recipes as $recipe)
{
  $makes = explode(',', $recipe['makes']);
  $ingredients = explode(',', $recipe['ingredients']);
  sort($ingredients);

  echo '<h6>' . $makes[0] . '</h6>';
  echo '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';
}
?>
