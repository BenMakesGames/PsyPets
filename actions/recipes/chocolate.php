<?php
if($okay_to_be_here !== true)
  exit();
?>
<p>In Catalonia, August 15th was once a festival day during which locals would drink.</p>
<p>A lot.</p>
<p>The following day, as you might expect, there were many people suffering from a hang over.  So to make people feel better, the village chocolatier would offer hot chocolate, claiming it was a remedy.</p>
<p>Over time, the act of drinking hot chocolate on August 16th became a festival in and of itself.</p>
<p>This festival is called Xicolatada.</p>
<hr />
<?php
$command = 'SELECT makes,ingredients FROM monster_recipes WHERE ingredients LIKE \'%Chocolate%\' GROUP BY(makes) ORDER BY makes ASC';
$recipes = $database->FetchMultiple($command, 'item: ' . $this_inventory['itemname']);

foreach($recipes as $recipe)
{
  $makes = explode(',', $recipe['makes']);
  $ingredients = explode(',', $recipe['ingredients']);
  sort($ingredients);

  echo '<h5>' . $makes[0];

  if(count($makes) > 1)
    echo ' <i>(makes ' . count($makes) . ')</i>';

  echo '</h5>' .
       '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';
}
?>
