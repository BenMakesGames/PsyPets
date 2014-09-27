<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/fireworklib.php';
require_once 'commons/threadfunc.php';
require_once 'commons/itemlib.php';

$recipes_known = array(1,2,3,4,6,7,8,9,13,14,20,21,69,70,71,72,73,74,117,118,136,137,243,244,245,246,247,248,279,280,281,282,283,284,285,286,287,288,289,290,345,346,427,428);

$command = 'SELECT * FROM monster_recipes WHERE idnum IN (' . implode(',', $recipes_known) . ') LIMIT ' . count($recipes_known);
$recipes = $database->FetchMultiple($command, 'fetching recipes');

$stores = array();

if($_GET['action'] == 'firework')
  ;
else if(substr($this_inventory['location'], 0, 4) != 'home')
  echo '<h5>Prepare Food</h5><p>You cannot use the ' . $this_inventory['itemname'] . ' to prepare food away from home.</p><hr />';
else
{
  echo '<h5>Prepare Food</h5>';

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

          echo '<p class="success">You have prepared ' . implode(', ', $made_messages) . '!</p>';
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
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
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
<hr />
<?php
} // you're at home

$fireworkid = 15;
$quantity = 2;

echo '<h5>Release Trapped Souls</h5>';

if($_GET['action'] == 'firework')
{
  delete_inventory_byid($this_inventory['idnum']);

  $supply = get_firework_supply($user);

  gain_firework($supply, $fireworkid, $quantity);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing firework from player');

  echo '
    <p>The ' . $this_inventory['itemname'] . ' is readied!</p>
    <ul>
     <li>To apply it to a Plaza post, find the post you want, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
     <li>To apply it to a room in your house, visit that room, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
     <li>To apply it to your profile, visit your profile, and click the <img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /> icon.</li>
    </ul>
  ';
}
else
{
  echo '
    <p>Doing this will consume the ' . $this_inventory['itemname'] . ', but will give you a pretty graphic to apply to a Plaza post or room of your house, or to change your profile background!</p>
  ';

  if($quantity > 1)
    echo '<p>(Actually, it will give you the graphic for use ' . $quantity . ' times!)</p>';

  echo '
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=firework">Release the souls trapped within the ' . $this_inventory['itemname'] . '!</a></li>
    </ul>
  ';
}
?>