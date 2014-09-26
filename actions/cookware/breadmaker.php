<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$ingredients = array(
  1 => array('Butter', 'Sugar', 'Banana', 'Egg', 'Flour'),
  2 => array('Butter', 'Sugar', 'Banana', 'Speckled Egg', 'Flour'),
  3 => array('Yeast', 'Flour', 'Sugar', 'Milk'),
  4 => array('Yeast', 'Flour', 'Sugar', 'Milk', 'Butter'),
  5 => array('Yeast', 'Flour', 'Sugar', 'Milk', 'Shortening'),
  6 => array('Baking Chocolate', 'Egg', 'Flour', 'Sugar', 'Baking Powder', 'Butter'),
  7 => array('Baking Chocolate', 'Speckled Egg', 'Flour', 'Sugar', 'Baking Powder', 'Butter'),
);

$stores = array();

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo 'You cannot use the ' . $this_inventory['itemname'] . ' away from home.';
else
{
  $myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

  foreach($myhouse as $item)
    $stores[$item['itemname']]++;

  if($_POST['action'] == 'bake')
  {
    $quantity = (int)$_POST['quantity'];
    $bread = $_POST['bread'];
    $errored = false;
    
    if(array_key_exists($bread, $ingredients) && $quantity > 0)
    {
      $items = $ingredients[$bread];
      foreach($items as $itemname)
      {
        if($stores[$itemname] < $quantity)
        {
          $errored = true;
          break;
        }
      }

      if(!$errored)
      {
        foreach($ingredients[$bread] as $itemname)
        {
          delete_inventory_byname($user['user'], $itemname, $quantity, $this_inventory['location']);
          $stores[$itemname] -= $quantity;
        }

        if($bread == 1 || $bread == 2)
          $made['Banana Bread'] = $quantity;
        else if($bread == 6 || $bread == 7)
          $made['Chocolate Bread'] = $quantity;
        else if($bread == 3)
          $white_bread_amount = 4;
        else
          $white_bread_amount = 5;

        for($i = 0; $i < $quantity; ++$i)
        {
          if($bread == 1 || $bread == 2)
            add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Banana Bread', 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
          else if($bread == 6 || $bread == 7)
            add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Chocolate Bread', 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
          else if($bread >= 3 || $bread <= 5)
          {
            for($x = 0; $x < $white_bread_amount; ++$x)
            {
              if(mt_rand(1, 10) == 10)
                $make_this = 'Wheat Bread';
              else
                $make_this = 'White Bread';

              $made[$make_this]++;

              add_inventory_cached($user['user'], 'u:' . $user['idnum'], $make_this, 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
            }
          }
        }

        process_cached_inventory();
        
        foreach($made as $what=>$amount)
          $made_messages[] = $amount . ' ' . $what;

        echo '<span class="success">You have prepared ' . implode(', ', $made_messages) . '!</span></p><p>';

        $agingroot--;
      }
      else
        echo '<span class="failure">You do not have enough ingredients.</span></p><p>';
    }
  }
?>
What will you put into the bread maker, and how many batches will you make?</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table>
 <tr>
  <th>Ingredients:</th>
  <td><select name="bread">
<?php
  $one_ok = false;
  foreach($ingredients as $bread=>$list)
  {
    $max_ok = -1;
    foreach($list as $itemname)
    {
      if((int)$stores[$itemname] < $max_ok || $max_ok == -1)
        $max_ok = (int)$stores[$itemname];
    }
      
    if($max_ok > 0)
    {
      echo '   <option value="' . $bread . '">' . implode(', ', $list) . ' (up to ' . $max_ok . ')</option>' . "\n";
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
</table></p>
<p><input type="hidden" name="action" value="bake" /><input type="submit" value="Bake" /></p>
<?php
    }
    else
      echo '</table></p>';
?>
</form>
<?php
} // you're at home
?>
