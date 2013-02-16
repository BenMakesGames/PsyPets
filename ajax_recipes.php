<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/utility.php';
require_once 'commons/formatting.php';

$house = $database->FetchSingle('SELECT * FROM monster_houses WHERE userid=' . $user['idnum'] . ' LIMIT 1');

if(strlen($house['curroom']) > 0)
{
  $curroom = $house['curroom'];
  $curlocation = 'home/' . $curroom;
}
else
{
  $curroom = 'Common';
  $curlocation = 'home';
}

$favorite_recipes = $database->FetchMultiple('
  SELECT recipeid
  FROM psypets_known_recipes
  WHERE userid=' . $user['idnum'] . '
  ORDER BY favorite DESC,times_prepared DESC
  LIMIT 20
');

if(count($favorite_recipes) > 0)
{
  foreach($favorite_recipes as $recipe)
  {
    $ingredients = $database->FetchSingle('SELECT ingredients,makes FROM monster_recipes WHERE idnum=' . $recipe['recipeid'] . ' LIMIT 1');

    $ingreident_list = explode(',', $ingredients['ingredients']);

    $requires = array();
    foreach($ingreident_list as $item)
      $requires[$item]++;

    $okay = true;
    $these_ingredients = array();
    $max = 999;

    foreach($requires as $itemname=>$quantity)
    {
      $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($curlocation) . ' AND itemname=' . quote_smart($itemname) . ' LIMIT ' . $quantity;
      $data = $database->FetchSingle($command, 'checking house for ingredients');
      if($data['c'] < $quantity)
      {
        $okay = false;
        $these_ingredients[] = '<span class="failure">' . $itemname . '</span>';
        $max = 0;
      }
      else
      {
        $these_ingredients[] = $itemname;
        if($max > (int)($data['c'] / $quantity))
          $max = (int)($data['c'] / $quantity);
      }
    }

    $make_list = explode(',', $ingredients['makes']);
    
    $makes = array();
    
    foreach($make_list as $item)
      $makes[$item]++;

    $products = array();

    foreach($makes as $item=>$quantity)
      $products[] = $quantity . '&times; ' . $item;

    $recipes[$recipe['recipeid']] = array($okay, implode('<br />', $these_ingredients), implode('<br />', $products), $max);
  }
}
else
  $recipes = array();

$rowclass = begin_row_class();
?>
<?php
if(count($recipes) > 0)
{
?>
<form action="/moveinventory2.php?confirm=1" method="post">
<div>
<table>
<thead>
<tr><th></th><th>Ingredients</th><th>Prepares</th><th>Max&nbsp;Batches</th></tr>
</thead>
<tbody>
<?php

  $max_max = 0;

  foreach($recipes as $index=>$recipe)
  {
    echo '<tr class="' . $rowclass . '">';
    $ingredients = $recipe[1];
    $makes = $recipe[2];
    $max = $recipe[3];
    $max_max = max($max, $max_max);
    if($recipe[0])
      echo '<td><input type="radio" name="recipe1" value="' . $index . '" /></td>';
    else
      echo '<td><input type="radio" name="recipe1" disabled /></td>';

    echo '<td>' . $ingredients . '</td><td>' . $makes . '</td><td class="centered">' . $max . '</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }
?>
</tbody>
</table>
</div>
<p>Quantity: <input type="number" min="0" max="<?= $max_max ?>" name="quantity" size="3" maxlength="3" value="1" /> <input type="submit" name="submit" value="Prepare" /> <input type="button" onclick="closepreparewindow();" value="Cancel" /></p>
<p style="margin-bottom:0;"><i>(If you do not have enough ingredients to make the quantity desired, you will make as many as possible.)</i></p>
</form>
<?php
}
else
{
?>
<p>This window shows you your top 20 recipes, however you have not prepared anything yet!</p>
<p>Once you've prepared something, you can prepare the recipe repeatedly here, without the tedium of checking all the items over and over again!  Useful!</p>
<p style="margin-bottom:0;"><input type="button" onclick="closepreparewindow();" value="Close" /></p>
<?php
}
?>
