<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$ingredients = array(
  'Redsberry Wine'  => array('Redsberries'),
  'Blueberry Wine'  => array('Blueberries'),
  'Goodberry Wine'  => array('Goodberries'),
  'Evilberry Wine'  => array('Evilberries'),
  'Sake'            => array('Rice'),
  'Vodka'           => array('Potato'),
  'Rum'             => array('Sugar Beet'),
  'Coconut Cordial' => array('Coconut', 'Sugar'),
  'Shōchū'          => array('Yam'),
  'Lye'             => array('Smoke'),
  'Cucumber'        => array('Plain Pickle'),
);

$stores = array();

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo 'You cannot use the ' . $this_inventory['itemname'] . ' away from home.';
else
{
  $myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

  foreach($myhouse as $item)
  {
    if($item['itemname'] == 'Aging Root')
      $agingroot++;
    else
    {
      foreach($ingredients as $wine=>$list)
      {
        foreach($list as $itemname)
        {
          if($item['itemname'] == $itemname)
            $stores[$itemname]++;
        }
      }
    }
  }

  if($_POST['action'] == 'brew')
  {
    $quantity = (int)$_POST['quantity'];
    $wine = $_POST['wine'];
    $errored = false;

    if(!array_key_exists($wine, $ingredients))
      echo '<span class="failure">Please select something to brew.</span>';
    else if($quantity > 0 && $quantity <= 20)
    {
      $items = $ingredients[$wine];
      foreach($items as $itemname)
      {
        if($stores[$itemname] < $quantity)
        {
          $errored = true;
          $message = '<span class="failure">You do not have enough ' . $items[$index] . ' to make ' . $amount . ' ' . $wine . '.</span>';
        }
      }

      if(!$errored && $agingroot > 0)
      {
        foreach($ingredients[$wine] as $itemname)
        {
          delete_inventory_byname($user['user'], $itemname, $quantity, $this_inventory['location']);
          $stores[$itemname] -= $quantity;
        }

        delete_inventory_byname($user['user'], 'Aging Root', 1, $this_inventory['location']);

        if($wine == 'Vodka' || $wine == 'Shōchū')
          $quantity *= 2;

        if($wine == 'Redsberry Wine' || $wine == 'Blueberry Wine')
        {
          for($i = 0; $i < $quantity; ++$i)
            add_inventory($user['user'], 'u:' . $user['idnum'], (mt_rand(1, 100) == 100 ? 'Goodberry Wine' : $wine), 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
        }
        else
        {
          for($i = 0; $i < $quantity; ++$i)
            add_inventory($user['user'], 'u:' . $user['idnum'], $wine, 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);
        }

        if($quantity < 10)
          $message = "You have prepared $wine!";
        else if($quantity < 21)
          $message = "You have prepared some $wine!";
        else if($quantity < 41)
          $message = "You have prepared a lot of $wine!";
        else
          $message = "PARTY!!";

        if($wine == 'Blueberry Wine' || $wine == 'Redsberry Wine')
        {
          $cream_of_tartar = floor($quantity / 3);

          if($cream_of_tartar > 0)
          {
            for($i = 0; $i < $cream_of_tartar; ++$i)
              add_inventory($user['user'], 'u:' . $user['idnum'], 'Cream of Tartar', 'Byproduct of Wine-making with ' . $this_inventory['itemname'], $this_inventory['location']);

            $message .= ' (And you collected Cream of Tartar that was left-over from the process.)';
          }
        }

        echo '<span class="success">' . $message . '</span></p><p>';

        $RECOUNT_INVENTORY = true;

        $agingroot--;
      }
      else
        echo "<span class=\"failure\">You do not have enough ingredients.</span></p>\n<p>";
    }
    else
      echo "<span class=\"failure\">You may not make more than 20 at a time.</span></p>\n<p>";
  }

  if($agingroot > 0)
  {
?>
When aging drinks in the <?= $this_inventory['itemname'] ?>, you only need a single Aging Root.  Efficient!</p>
<p>What, and how much, would you like to make? (up to 20)</p>
<p><i>(Unlike the others, Vodka and Shōchū have double yields; for example if you enter 15 Vodka, you will use 15 Potatoes and get 30 Vodka.)</i></p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table>
 <tr>
  <th>Brew:</th>
  <td><select name="wine">
<?php
    $one_ok = false;
    foreach($ingredients as $wine=>$list)
    {
      $max_ok = 20;
      foreach($list as $itemname)
      {
        if((int)$stores[$itemname] < $max_ok)
          $max_ok = (int)$stores[$itemname];
      }
      
      if($max_ok > 0)
      {
        echo '   <option value="' . $wine . '">' . $wine . ' (up to ' . $max_ok . ')</option>' . "\n";
        $one_ok = true;
      }
      else
        echo '   <option disabled>' . $wine . ' (need ingredients)</option>' . "\n";
    }
?>
  </select></td>
 </tr>
<?php
    if($one_ok)
    {
?>
 <tr>
  <th>Amount:</th>
  <td><input name="quantity" size="2" maxlength="2" /></td>
 </tr>
</table></p>
<p><input type="hidden" name="action" value="brew" /><input type="submit" value="Brew" /></p>
<?php
    }
    else
      echo '</table></p>';
?>
</form>
<?php
  }
  else
    echo 'You need a single Aging Root to brew with the ' . $this_inventory['itemname'] . '.</p><p>(The ' . $this_inventory['itemname'] . ' only uses items in the same room.)';
} // you're at home
?>
