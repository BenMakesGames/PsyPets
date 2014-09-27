<?php
if($okay_to_be_here !== true)
  exit();

$recipes = $database->FetchMultiple('SELECT * FROM monster_recipes WHERE ingredients LIKE \'%Onion%\' OR ingredients LIKE \'%Shallot%\' GROUP BY makes');
?>
<style type="text/css">
.recipe-card
{
  float:left;
  width:180px;
  height:180px;
  margin:0 20px 20px 0;
  padding:5px;
  background-color:#fff;
  -moz-box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
  box-shadow:2px 2px 4px rgba(0, 0, 0, 0.3);
}
</style>
<p><i>by Rickman T. Aberystwyth</i></p>
<?php
foreach($recipes as $recipe)
{
  $makes = explode(',', $recipe['makes']);
  $ingredients = explode(',', $recipe['ingredients']);
  sort($ingredients);

  echo '
    <div class="recipe-card">
      <h6>' . $makes[0] . '</h6>
      <ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>
    </div>
  ';
}
?>
<div style="clear:both;"></div>
