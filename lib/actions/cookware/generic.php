<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$recipes_known = explode(',', $action_info[2]);

$command = 'SELECT * FROM monster_recipes WHERE idnum IN (' . implode(',', $recipes_known) . ') LIMIT ' . count($recipes_known);
$recipes = $database->FetchMultiple($command, 'itemaction.php?idnum=' . $_GET['idnum']);

$stores = array();

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo '<p>You cannot use the ' . $this_inventory['itemname'] . ' away from home.</p>';
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

    if($quantity > 9999)
      $quantity = 9999;

    if(in_array($recipe, $recipes_known) && $quantity > 0)
    {
      $items = array();
    
      foreach($recipes as $this_recipe)
      {
        if($this_recipe['idnum'] == $recipe)
        {
          $items = explode(',', $this_recipe['ingredients']);
          $items_to_add = explode(',', $this_recipe['makes']);
					$machine_only = ($this_recipe['machine_only'] == 'yes');
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
          $total_made = 0;

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
              $total_made++;
            }
          }

          process_cached_inventory();

          $RECOUNT_INVENTORY = true;

          foreach($made as $what=>$amount)
            $made_messages[] = $amount . ' ' . $what;

          echo '<p class="success">You have prepared ' . implode(', ', $made_messages) . '!</p>';

					if(!$machine_only)
					{
						require_once 'commons/kitchenlib.php';
						record_known_recipe($user['idnum'], $recipe);
					}

          if($this_inventory['itemname'] == 'Jellier' && $total_made >= 100)
          {
            $command = 'UPDATE psypets_badges SET jellier=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
            $database->FetchNone($command, 'jellier badge');

            if($database->AffectedRows() > 0)
              echo '<p><i>(You received the Jellier badge!)</i></p>';
          }
        }
        else
          echo '<p class="failure">You do not have enough ingredients.</p>';
      }
      else
        echo '<p class="failure">Choose a recipe for real this time.</p>';
    }
  }
?>
<p>What will you put into the <?= $this_inventory['itemname'] ?>, and how many batches will you make?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table>
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
  <td><input name="quantity" size="4" maxlength="4" /></td>
 </tr>
</table>
<p><input type="hidden" name="action" value="prepare" /><input type="submit" value="Prepare" /></p>
<?php
    }
    else
      echo '</table>';
?>
</form>
<?php
} // you're at home
?>
