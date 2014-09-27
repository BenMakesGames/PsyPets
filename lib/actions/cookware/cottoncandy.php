<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$recipes_known = array(-1, -2);

$recipes = array(
  array('idnum' => -1, 'ingredients' => 'Sugar,Sugar,Red Dye', 'makes' => 'Pink Cotton Candy'),
  array('idnum' => -2, 'ingredients' => 'Sugar,Sugar,Blue Dye', 'makes' => 'Blue Cotton Candy'),
);

$stores = array();

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo 'You cannot use the ' . $this_inventory['itemname'] . ' away from home.';
else
{
  $myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

  foreach($myhouse as $item)
    $stores[$item['itemname']]++;

  if($_POST['action'] == 'prepare')
  {
    $quantity = (int)$_POST['quantity'];
    $recipe = (int)$_POST['recipe'];
    $errored = false;

    if(in_array($recipe, $recipes_known) && $quantity > 0)
    {
      $items = array();
    
      foreach($recipes as $this_recipe)
      {
        if($this_recipe['idnum'] == $recipe)
        {
          $items = explode(',', $this_recipe['ingredients']);
          $items_to_add = explode(',', $this_recipe['makes']);
          break;
        }
      }

      if(count($items) > 0)
      {
        $needed = array();

        // check that we have enough of the items at home
        foreach($items as $itemname)
        {
          $needed[$itemname] += $quantity;
          if($stores[$itemname] < $needed[$itemname])
          {
            $errored = true;
            break;
          }
        }

        if(!$errored)
        {
          foreach($needed as $itemname=>$amount)
          {
            delete_inventory_byname($user['user'], $itemname, $amount, $this_inventory['location']);
            $stores[$itemname] -= $amount;
          }

          for($i = 0; $i < $quantity; ++$i)
          {
            foreach($items_to_add as $make_this)
            {
              add_inventory_cached($user['user'], 'u:' . $user['idnum'], $make_this, 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
              $made[$make_this]++;
            }
          }

          process_cached_inventory();

          $RECOUNT_INVENTORY = true;

          foreach($made as $what=>$amount)
            $made_messages[] = $amount . ' ' . $what;

          echo '<span class="success">You have prepared ' . implode(', ', $made_messages) . '!</span></p><p>';
        }
        else
          echo '<span class="failure">You do not have enough ingredients.</span></p><p>';
      }
      else
        echo '<span class="failure">Choose a recipe for real this time.</span></p><p>';
    }
  }
?>
What will you put into the <?= $this_inventory['itemname'] ?>, and how many batches will you make?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table class="nomargin">
 <tr>
  <th>Ingredients:</th>
  <td><select name="recipe">
<?php
  $one_ok = false;
  foreach($recipes as $this_recipe)
  {
    $list = explode(',', $this_recipe['ingredients']);
  
    $max_ok = -1;
    $needed = array();
    foreach($list as $itemname)
    {
      $needed[$itemname]++;
      if((int)($stores[$itemname] / $needed[$itemname]) < $max_ok || $max_ok == -1)
        $max_ok = (int)($stores[$itemname] / $needed[$itemname]);
    }

    if($max_ok > 0)
    {
      echo '   <option value="' . $this_recipe['idnum'] . '">' . implode(', ', $list) . ' (up to ' . $max_ok . ')</option>' . "\n";
      $one_ok = true;
    }
    else
      echo '   <option disabled>' . implode(', ', $list) . ' (need ingredients)</option>' . "\n";
  }
?>
  </select></td>
 </tr>
<?php
    if($one_ok)
    {
?>
 <tr>
  <th>Batches:</th>
  <td><input name="quantity" size="2" maxlength="2" /></td>
 </tr>
</table>
<p><input type="hidden" name="action" value="prepare" /><input type="submit" value="Prepare" /></p>
<?php
    }
    else
      echo '</table></p>';
?>
</form>
<?php
} // you're at home
?>
