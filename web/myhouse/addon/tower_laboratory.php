<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Tower#Laboratory';
$require_petload = 'no';

$THIS_ROOM = 'Tower';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/alchemylib.php';
require_once 'commons/zodiac.php';
require_once 'commons/towerlib.php';

if(!addon_exists($house, 'Tower'))
{
  header('Location: /myhouse.php');
  exit();
}

$tower = get_tower_byuser($user['idnum']);

$sign_num = get_western_zodiac($now);

$inventory = get_houseinventory_byuser_forpets($user['user']);
$itemcount = array();

foreach($inventory as $i)
  $itemcount[$i['itemname']]++;

if($_POST['action'] == 'distill')
{
  $amount_zephrous = (int)$_POST['amount_zephrous'];
  $amount_pyrium = (int)$_POST['amount_pyrium'];
  $amount_aquite = (int)$_POST['amount_aquite'];

  if($amount_zephrous <= 0)
    ;
  else if($amount_zephrous > (int)$itemcount['Carrot'] || $amount_zephrous > (int)$itemcount['Feather'])
    $message .= "<p><span class=\"failure\">You need $amount_zephrous Carrots and Feathers to make $amount_zephrous Zephrous.</span></p>";
  else
  {
    delete_inventory_fromhome($user['user'], 'Carrot', $amount_zephrous);
    delete_inventory_fromhome($user['user'], 'Feather', $amount_zephrous);

    $itemcount['Carrot'] -= $amount_zephrous;
    $itemcount['Feather'] -= $amount_zephrous;
    $itemcount['Zephrous'] += $amount_zephrous;

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Zephrous', 'Distilled in ' . $user['display'] . '\'s tower', 'home', $amount_zephrous);

    $message .= "<p><span class=\"success\">Distilling yielded $amount_zephrous Zephrous.</span></p>";
  }

  if($amount_pyrium <= 0)
    ;
  else if($amount_pyrium > (int)$itemcount['Blood'] || $amount_pyrium > (int)$itemcount['Coal'])
    $message .= "<p><span class=\"failure\">You need $amount_pyrium Blood and Coal to make $amount_pyrium Pyrium.</span></p>";
  else
  {
    delete_inventory_fromhome($user['user'], 'Blood', $amount_pyrium);
    delete_inventory_fromhome($user['user'], 'Coal', $amount_pyrium);

    $itemcount['Blood'] -= $amount_pyrium;
    $itemcount['Coal'] -= $amount_pyrium;
    $itemcount['Pyrium'] += $amount_pyrium;

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Pyrium', 'Distilled in ' . $user['display'] . '\'s tower', 'home', $amount_pyrium);

    $message .= "<p><span class=\"success\">Distilling yielded $amount_pyrium Pyrium.</span></p>";
  }

  if($amount_aquite <= 0)
    ;
  else if($amount_aquite > (int)$itemcount['Venom'] || $amount_aquite > (int)$itemcount['Coconut Milk'])
    $message .= "<p><span class=\"failure\">You need $amount_aquite Venom and Coconut Milk to make $amount_aquite Aquite.</span></p>";
  else
  {
    delete_inventory_fromhome($user['user'], 'Venom', $amount_aquite);
    delete_inventory_fromhome($user['user'], 'Coconut Milk', $amount_aquite);

    $itemcount['Venom'] -= $amount_aquite;
    $itemcount['Coconut Milk'] -= $amount_aquite;
    $itemcount['Aquite'] += $amount_aquite;

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Aquite', 'Distilled in ' . $user['display'] . '\'s tower', 'home', $amount_aquite);

    $message .= "<p><span class=\"success\">Distilling yielded $amount_aquite Aquite.</span></p>";
  }
}
else if($_POST['action'] == 'transmute')
{
  $enough = true;
  $transmutation = false;

  $quantity = (int)$_POST['quantity'];
  $id = (int)$_POST['transmutation'];
  $alchemy = get_transmutation_byid($id);

  if($alchemy === false)
  {
    $message .= '<p><span class="failure">You forgot to select something to transmute!</span></p>';
    $transmutation = false;
  }
  else if($quantity <= 0)
    $message .= '<p><span class="failure">Quantity must be at least 1...</span></p>';
  else if(transmutation_available($alchemy))
    $transmutation = true;
    
  if($transmutation === true)
  {
    $items = take_apart(',', $alchemy['items_in']);

    foreach($items as $details)
    {
      $itemdetails = explode('|', $details);
      if((int)$itemcount[$itemdetails[1]] < (int)$itemdetails[0] * $quantity)
      {
        $enough = false;
        $message .= '<p><span class="failure">You do not have enough ' . $itemdetails[1] . ' to make that.</span></p>';
      }
    }

    if($enough)
    {
      foreach($items as $details)
      {
        $itemdetails = explode('|', $details);
        $deleted = delete_inventory_fromhome($user['user'], $itemdetails[1], $itemdetails[0] * $quantity);
        $itemcount[$itemdetails[1]] -= $itemdetails[0] * $quantity;
      }

      for($x = 0; $x < $quantity; ++$x)
        add_inventory_cached($user['user'], 'u:' . $user['idnum'], $alchemy['item_out'], 'Transmuted in ' . $user['display'] . '\'s tower', 'home');

      process_cached_inventory();

      $itemcount[$alchemy['item_out']] += $quantity;

      $message = '<p><span class="success">With a sudden flash of light the materials are transmuted into ' . $alchemy['item_out'] . '!</span></p>';
    }
  }
}

$transmutes = get_transmutations();

$zephrous_item = get_item_byname('Zephrous');
$aquite_item = get_item_byname('Aquite');
$pyrium_item = get_item_byname('Pyrium');
$carrot_item = get_item_byname('Carrot');
$feather_item = get_item_byname('Feather');
$venom_item = get_item_byname('Venom');
$coconut_milk_item = get_item_byname('Coconut Milk');
$coal_item = get_item_byname('Coal');
$blood_item = get_item_byname('Blood');

$CONTENT_STYLE = 'background: #fff url(//' . $SETTINGS['site_domain'] . '/gfx/sephiroth.png) no-repeat scroll top center;';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Tower &gt; Laboratory</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Tower &gt; Laboratory</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/tower.php">Balcony</a></li>
 <li class="activetab"><a href="/myhouse/addon/tower_laboratory.php">Laboratory</a></li>
<?php
if($tower['bell'] == 'yes')
  echo '<li><a href="/myhouse/addon/tower_bell.php">Bell Tower</a></li>';
?>
</ul>
<?= $message ?>
     <h5>Distill Elemental Compounds</h5>
     <p style="background-color:#fff;">You will need 1 of each component to distill a single vial of alchemical materia.</p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th colspan="2">Components</th>
       <th></th>
       <th colspan="2">Alchemy</th>
      </tr>

      <tr class="row">
       <td class="centered"><?= item_display($carrot_item, '') ?></td>
       <td><?= (int)$itemcount['Carrot'] ?></td>
       <td rowspan="2"><img src="/gfx/alchemybracket.png" alt="combine to make" /></td>
       <td rowspan="2" class="centered"><?= item_display($zephrous_item, '') ?></td>
       <td rowspan="2"><input name="amount_zephrous" title="quantity" size="3" maxlength="3" /></td>
      </tr>
      <tr class="row">
       <td class="centered"><?= item_display($feather_item, '') ?></td>
       <td><?= (int)$itemcount['Feather'] ?></td>
      </tr>

      <tr class="altrow">
       <td class="centered"><?= item_display($coconut_milk_item, '') ?></td>
       <td><?= (int)$itemcount['Coconut Milk'] ?></td>
       <td rowspan="2"><img src="/gfx/alchemybracket.png" alt="combine to make" /></td>
       <td rowspan="2" class="centered"><?= item_display($aquite_item, '') ?></td>
       <td rowspan="2"><input name="amount_aquite" title="quantity" size="3" maxlength="3" /></td>
      </tr>
      <tr class="altrow">
       <td class="centered"><?= item_display($venom_item, '') ?></td>
       <td><?= (int)$itemcount['Venom'] ?></td>
      </tr>

      <tr class="row">
       <td class="centered"><?= item_display($coal_item, '') ?></td>
       <td><?= (int)$itemcount['Coal'] ?></td>
       <td rowspan="2"><img src="/gfx/alchemybracket.png" alt="combine to make" /></td>
       <td rowspan="2" class="centered"><?= item_display($pyrium_item, '') ?></td>
       <td rowspan="2"><input name="amount_pyrium" title="quantity" size="3" maxlength="3" /></td>
      </tr>
      <tr class="row">
       <td class="centered"><?= item_display($blood_item, '') ?></td>
       <td><?= (int)$itemcount['Blood'] ?></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="distill" /><input type="submit" value="Distill" />
     </form>
<?php
$prev_type = '';

if(count($transmutes) > 0)
{
  $rowclass = begin_row_class();

  foreach($transmutes as $alchemy)
  {
    if(!transmutation_available($alchemy))
      continue;

    if($prev_type != $alchemy['type'])
    {
      if($prev_type != '')
        echo '
          </table></p>
          <p>Quantity: <input name="quantity" value="1" size="2" /> <input type="hidden" name="action" value="transmute" /><input type="submit" value="Transmute" /></p>
          </form>
        ';

      if($alchemy['type'] == 'potion')
        echo '<h5>Potions</h5>';
      else
        echo '<h5>' . ucfirst($alchemy['type']) . ' Transmutations</h5>';

      if($alchemy['type'] == 'zodiac')
        echo '<p style="background-color:#fff;">These transmutations are available depending on the astrological sign - currently ' . $WESTERN_ZODIAC[$sign_num] . ' ' . $CHINESE_ZODIAC_EN[get_chinese_zodiac($now)] . ' - current moon phase, and other factors.</p>';
?>
     <form method="post">
     <p><table>
      <tr class="titlerow">
       <th></th>
       <th>Ingredients</th>
       <th></th>
       <th>Result</th>
       <th></th>
      </tr>
<?php
      $prev_type = $alchemy['type'];
    }

    $enough = true;
    $items = take_apart(',', $alchemy['items_in']);
    foreach($items as $details)
    {
      $itemdetails = explode('|', $details);
      if((int)$itemcount[$itemdetails[1]] < $itemdetails[0])
      {
        $enough = false;
        break;
      }
    }
    
    $details2 = get_item_byname($alchemy['item_out']);

?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="transmutation" value="<?= $alchemy['idnum'] ?>"<?= $enough ? '' : ' disabled' ?> /></td>
       <td>
<?php
    foreach($items as $details)
    {
      $itemdetails = explode('|', $details);
      $thisitem = get_item_byname($itemdetails[1]);

      if($itemcount[$itemdetails[1]] >= $itemdetails[0])
        echo item_text_link($itemdetails[1]) . ' (' . $itemcount[$itemdetails[1]] . '/' . $itemdetails[0] . ')<br />';
      else
        echo item_text_link($itemdetails[1], 'failure') . ' <span class="failure">(' . (int)$itemcount[$itemdetails[1]] . '/' . $itemdetails[0] . ')</span><br />';
    }
?>
       </td>
       <td><img src="/gfx/lookright.gif" width="16" height="16" /></td>
       <td class="centered"><?= item_display_extra($details2, '', true) ?></td>
       <td><?= $details2['itemname'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table></p>
     <p>Quantity: <input name="quantity" value="1" size="2" /> <input type="hidden" name="action" value="transmute" /><input type="submit" value="Transmute" /> <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ancientscript/tower-laboratory-message.png" width="442" height="11" style="margin-left:10px;" alt="" /></p>
     </form>
<?php
}
else
  echo '<p>Nothing currently in your house can be transmuted.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
