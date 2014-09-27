<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo '<p>You cannot use the ' . $this_inventory['itemname'] . ' away from home.</p>';
else
{
  $quantity = (int)$_POST['quantity'];
  $ingredient = $_POST['ingredient'];
  $errored = false;
  $max_quantity = 0;

  if($ingredient != 'Blueberries' && $ingredient != 'Amethyst Rose'
    && $ingredient != 'Onion' && $ingredient != 'Shallot' && $ingredient != 'Beet'
    && $ingredient != 'Pomegranate' && $ingredient != 'Tomato' && $ingredient != 'Tea Leaves')
  {
    $errored = true;
  }

  $myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

  foreach($myhouse as $item)
  {
    if($item['itemname'] == $ingredient)
      $max_quantity++;

    if($item['itemname'] == 'Blueberries')
      $blueberries++;
    else if($item['itemname'] == 'Amethyst Rose')
      $amethyst_rose++;
    else if($item['itemname'] == 'Onion')
      $onion++;
    else if($item['itemname'] == 'Shallot')
      $shallot++;
    else if($item['itemname'] == 'Beet')
      $beet++;
    else if($item['itemname'] == 'Pomegranate')
      $pomegranate++;
    else if($item['itemname'] == 'Tomato')
      $tomato++;
    else if($item['itemname'] == 'Tea Leaves')
      $tea_leaves++;
  }

  if(!$errored)
  {
    if($quantity > 0)
    {
      if($max_quantity >= $quantity)
      {
        delete_inventory_byname($user['user'], $ingredient, $quantity, $this_inventory['location']);

        if($ingredient == 'Blueberries')
        {
          $dye = 'Blue Dye';
          $real_quantity = $quantity;
          $blueberries -= $quantity;
        }
        else if($ingredient == 'Amethyst Rose')
        {
          $dye = 'Red Dye';
          $real_quantity = $quantity * 2;
          $amethyst_rose -= $quantity;
        }
        else if($ingredient == 'Onion')
        {
          $dye = 'Yellow Dye';
          $real_quantity = $quantity * 2;
          $onion -= $quantity;
        }
        else if($ingredient == 'Shallot')
        {
          $dye = 'Yellow Dye';
          $real_quantity = $quantity;
          $shallot -= $quantity;
        }
        else if($ingredient == 'Beet')
        {
          $dye = 'Red Dye';
          $real_quantity = $quantity * 2;
          $beet -= $quantity;
        }
        else if($ingredient == 'Pomegranate')
        {
          $dye = 'Red Dye';
          $real_quantity = $quantity * 2;
          $pomegranate -= $quantity;
        }
        else if($ingredient == 'Tomato')
        {
          $dye = 'Red Dye';
          $real_quantity = $quantity * 2;
          $tomato -= $quantity;
        }
        else if($ingredient == 'Tea Leaves')
        {
          if($quantity > 1)
            $dye = 'Blue and Yellow Dye';
          else
            $dye = 'Yellow Dye';

          $real_quantity = $quantity;
          $tea_leaves -= $quantity;
        }

        if($ingredient == 'Tea Leaves')
        {
          if($quantity > 1)
            add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Blue Dye', 'Made with ' . $this_inventory['itemname'], $this_inventory['location'], floor($quantity / 2));

          add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Yellow Dye', 'Made with ' . $this_inventory['itemname'], $this_inventory['location'], ceil($quantity / 2));
        }
        else
          add_inventory_quantity($user['user'], 'u:' . $user['idnum'], $dye, 'Made with ' . $this_inventory['itemname'], $this_inventory['location'], $real_quantity);

        if($real_quantity <= 10)
          $message = "You have distilled $dye!";
        else if($real_quantity <= 30)
          $message = "You have distilled some $dye!";
        else if($real_quantity <= 60)
          $message = "You have distilled a lot of $dye!";
        else
          $message = "Seriously... what will you do with all that $dye?";

        echo '<span class="success">' . $message . '</span></p><p>';

        $RECOUNT_INVENTORY = true;
      }
      else
        echo "<span class=\"failure\">You do not have enough.</span></p>\n<p>";
    }
    else
      echo "<span class=\"failure\">You didn't pick a number to distill.</span></p>\n<p>";
  }
  else if($_POST['action'] == 'distill')
    echo "<span class=\"failure\">You want to distill what?</span></p>\n<p>";

  if($amethyst_rose > 0 || $beet > 0 || $blueberries > 0 || $onion > 0 || $shallot > 0 || $tomato > 0 || $tea_leaves > 0)
  {
?>
<p>What would you like to distill?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<p><table>
 <tr>
  <th>Distill:</th>
  <td><select name="ingredient">
<?php
    if($amethyst_rose > 0)
      echo "   <option value=\"Amethyst Rose\">Amethyst Rose ($amethyst_rose available)</option>\n";
    if($beet > 0)
      echo "   <option value=\"Beet\">Beet ($beet available)</option>\n";
    if($blueberries > 0)
      echo "   <option value=\"Blueberries\">Blueberries ($blueberries available)</option>\n";
    if($onion > 0)
      echo "   <option value=\"Onion\">Onion ($onion available)</option>\n";
    if($shallot > 0)
      echo "   <option value=\"Shallot\">Shallot ($shallot available)</option>\n";
    if($pomegranate > 0)
      echo "   <option value=\"Pomegranate\">Pomegranate ($pomegranate available)</option>\n";
    if($tomato > 0)
      echo "   <option value=\"Tomato\">Tomato ($tomato available)</option>\n";
    if($tea_leaves > 0)
      echo "   <option value=\"Tea Leaves\">Tea Leaves ($tea_leaves available)</option>\n";
?>
  </select></td>
 </tr>
 <tr>
  <th>Amount:</th>
  <td><input type="number" name="quantity" size="4" maxlength="4" min="0" autocomplete="off" /></td>
 </tr>
</table></p>
<p><input type="hidden" name="action" value="distill" /><input type="submit" value="Distill" /></p>
</form>
<?php
  }
  else
    echo '<p>You do not have anything available to distill.</p>';
} // you're at home
?>
