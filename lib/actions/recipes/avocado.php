<?php
if($okay_to_be_here !== true)
  exit();

$recipes = $database->FetchMultiple('SELECT * FROM monster_recipes WHERE ingredients LIKE \'%Avocado%\'');

echo '<p><i>(The wand begins to speak...)</i></p>';

foreach($recipes as $recipe)
{
  $makes = explode(',', $recipe['makes']);
  $ingredients = explode(',', $recipe['ingredients']);
  sort($ingredients);

  echo '<h6>' . $makes[0] . '</h6>';
  echo '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';
}
?>
