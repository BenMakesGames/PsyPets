<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Fiberoptic Link';
$THIS_ROOM = 'Fiberoptic Link';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Fiberoptic Link'))
{
  header('Location: /myhouse.php');
  exit();
}

// --------------------------------------------------------
//                  HEY BEN!  BE CAREFUL!!
// --------------------------------------------------------
// it's important that these are listed in numerical order!
// (it fucks up the javascript otherwise >_>)
// --------------------------------------------------------
$STORE = array(
  0 => array('NUL', 1),
  1 => array('Dingo Corpse', 4),
  2 => array('Copper', 6),
  3 => array('Vector', 8),
  4 => array('Iron', 9),
  5 => array('Artificial Grapes', 10),
  6 => array('Square Wave', 14),
  7 => array('Pixel Totem', 22),
  8 => array('Photon', 30),
  9 => array('Triangle', 31),
  10 => array('Blue Pill', 50),
  11 => array('Red Pill', 100),
  12 => array('Game Room Blueprint', 1000),
);

if($_POST['action'] == 'Buy')
{
  $num_items = 0;

  foreach($STORE as $itemid=>$option)
  {
    $quantity = (int)$_POST['quantity_' . $itemid];
    if($quantity > 0)
    {
      $total += $option[1] * $quantity;
      $buying[$option[0]] += $quantity;
      $num_items += $quantity;
    }
  }
  
  if($num_items > 0)
  {
    if($user['pixels'] >= $total)
    {
      $command = 'UPDATE monster_users SET pixels=pixels-' . $total . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'spending pixels');
      
      $user['pixels'] -= $total;
      
      $game_room = false;
    
      foreach($buying as $itemname=>$quantity)
      {
        add_inventory_quantity($user['user'], '', $itemname, '', 'home', $quantity);

        if($itemname  == 'Game Room Blueprint')
          $game_room = true;
      }
        

      $messages[] = '<span class="success">Done!  You\'ll find ' . ($num_items == 1 ? 'it' : 'them') . ' in your Common room.</span>';

      if($game_room)
      {
        $badges = get_badges_byuserid($user['idnum']);

        if($badges['gamer'] == 'no')
        {
          set_badge($user['idnum'], 'gamer');
          $messages[] = '<i class="success">(You received the Gamer badge!)</i>';
        }
      }
    }
    else
      $messages[] = '<span class="failure">You do not have enough Pixels.</span>';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Fiberoptic Link &gt; Pixel Assembler</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  function recalc()
  {
    var total = 0;
    var prices = [<?php foreach($STORE as $option) echo $option[1] . ', '; ?>];
  
    for(var x = 0; x < <?= count($STORE) ?>; x++)
    {
      quantity = parseInt($('#quantity_' + x).val());
      if(quantity > 0)
        total += quantity * prices[x];
    }
    
    $('#quantity_total').html(total);
  }
  
  function validate(id)
  {
    var value = parseInt($('#quantity_' + id).val());

    if(!value || value <= 0)
      $('#quantity_' + id).val(0);
    else
      $('#quantity_' + id).val(value);

    recalc();
  }
  
  $(document).ready(function() {
    for(var x = 0; x < <?= count($STORE) ?>; x++)
    {
      $('#quantity_' + x).keyup(recalc);
    }
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fiberoptic Link &gt; Pixel Assembler</h4>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

room_display($house);
?>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/fiberoptic_link.php">Pixel Assembler</a></li>
 <li><a href="/myhouse/addon/fiberoptic_link_titles.php">Title Databank</a></li>
</ul>
     <p><i>(Items here must be paid for with Pixels.  You have <?= $user['pixels'] ?> Pixels.)</i></p>
     <form method="post">
     <table>
      <thead>
       <tr class="titlerow"><th></th><th>Item</th><th class="centered">Cost</th><th></th><th class="centered">Buy</th></tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($STORE as $itemid=>$option)
{
  $details = get_item_byname($option[0]);

  echo '
    <tr class="' . $rowclass . '">
     <td class="centered">' . item_display($details, '') . '</td>
     <td>' . $option[0] . '</td>
     <td class="centered">' . $option[1] . ' Pixels</td>
     <td>&times;</td>
     <td><input type="text" name="quantity_' . $itemid . '" id="quantity_' . $itemid . '" onblur="validate(' . $itemid . ')" maxlength="3" size="2" /></td>
    </tr>
  ';

  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
     <p><b>Total:</b> <span id="quantity_total">0</span> Pixels</p>
     <p><input type="submit" name="action" value="Buy" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
