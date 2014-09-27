<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Game Room';
$THIS_ROOM = 'Game Room';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/gameroomlib.php';

if(!addon_exists($house, 'Game Room'))
{
  header('Location: /myhouse.php');
  exit();
}

$game_room_prizes = array(
  'Astronaut Food' => array('Blue Ticket' => 1),
  'Tacky Sunglasses' => array('Green Ticket' => 1),
  'Small Bag of Candy' => array('Green Ticket' => 1),
  'Bag of Candy' => array('Blue Ticket' => 1, 'Green Ticket' => 1),
  'Red-blue Glasses' => array('Blue Ticket' => 2, 'Green Ticket' => 1),
);

if($now_month >= 5 && $now_month <= 8)
  $game_room_prizes['Odd Plushy'] = array('Yellow Ticket' => 1);
else if($now_month >= 9 && $now_month <= 12)
  $game_room_prizes['Little Piggy Plushy'] = array('Yellow Ticket' => 1);
else if($now_month >= 1 && $now_month <= 4)
  $game_room_prizes['Pink Odd Plushy'] = array('Yellow Ticket' => 1);

$game_room_prizes['Cheap Plastic Earrings'] = array('Blue Ticket' => 4);
$game_room_prizes['Box of Candy'] = array('Blue Ticket' => 1, 'Green Ticket' => 1, 'Yellow Ticket' => 1);
$game_room_prizes['Gold Star Stickers'] = array('Red Ticket' => 2, 'Green Ticket' => 1);
$game_room_prizes['Puniu'] = array('Red Ticket' => 1, 'Yellow Ticket' => 2, 'Blue Ticket' => 1);
$game_room_prizes['Leviathan Plushy'] = array('Foiled Ticket' => 1, 'Blue Ticket' => 1);
$game_room_prizes['Begemulined Minigame'] = array('Foiled Ticket' => 2, 'Yellow Ticket' => 1, 'Green Ticket' => 2, 'Blue Ticket' => 1);
$game_room_prizes['Password Minigame'] = array('Foiled Ticket' => 2, 'Red Ticket' => 1, 'Yellow Ticket' => 1);

$your_tickets = get_game_room_tickets($user);

$game_room = get_game_room($user['idnum'], true);

$game_room_games = get_game_room_games($user['idnum']);

$game_count = get_total_game_count();

if($_POST['action'] == 'Exchange')
{
  $total_cost = array();
  foreach($game_room_prizes as $itemname=>$cost)
  {
    $item_qty = (int)$_POST[itemname_to_form_value($itemname)];
    if($item_qty > 0)
    {
      foreach($cost as $ticket=>$quantity)
        $total_cost[$ticket] += $quantity * $item_qty;
    }
  }

  $enough_tickets = true;

  foreach($total_cost as $ticket=>$quantity)
  {
    if($quantity > $your_tickets[$ticket]['qty'])
    {
      $CONTENT['messages'][] = '<p class="failure">You do not have enough ' . $ticket . 's.</p>';
      $enough_tickets = false;
    }
  }

  if($enough_tickets)
  {
    $tickets_used = array();

    foreach($game_room_prizes as $itemname=>$cost)
    {
      $item_qty = (int)$_POST[itemname_to_form_value($itemname)];

      if($item_qty > 0)
      {
        $to_delete = 0;
        $deleted = 0;

        foreach($cost as $ticket=>$quantity)
        {
          $to_delete += $quantity * $item_qty;

          $command = '
            DELETE FROM monster_inventory
            WHERE
              user=' . quote_smart($user['user']) . '
              AND itemname=' . quote_smart($ticket) . '
              AND location LIKE \'home%\'
              AND location NOT LIKE \'home/$%\'
            LIMIT
              ' . ($quantity * $item_qty);
          $database->FetchNone($command, 'deleting tickets');

          $tickets_deleted = $database->AffectedRows();
          $deleted += $tickets_deleted;

          $tickets_used[$ticket] += $tickets_deleted;
        }

        if($deleted >= $to_delete)
        {
          add_inventory_quantity($user['user'], '', $itemname, $user['display'] . ' got this at their Game Room', 'home', $item_qty);
          $CONTENT['messages'][] = 'You claimed ' . $item_qty . '&times; ' . $itemname . '.';
        }
        else
          $CONTENT['messages'][] = 'Unable to delete tickets (deleted ' . $deleted . ' of ' . $to_delete . ').';
      }
    }

    $your_tickets = get_game_room_tickets($user);

    if(count($tickets_used) > 0)
    {
      $qtys = array();
      foreach($tickets_used as $ticket=>$qty)
        $qtys[] = $qty . '&times; ' . $ticket;

      $CONTENT['messages'][] = 'Tickets used: ' . implode(', ', $qtys);
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Game Room &gt; Prize Shop</title>
<?php include 'commons/head.php'; ?>
   <script type="text/javascript">
   function strtoint(v)
   {
     var i = parseInt(v);
     if(isNaN(i))
       return 0;
     else
       return i;
   }
   
   function update_total()
   {
     var blue = 0;
     var green = 0;
     var yellow = 0;
     var red = 0;
     var foiled = 0;
   
     $('.quantity').each(function() {
       var quantity = strtoint($(this).val());
       // -> td -> tr
       var row = $(this).parent().parent();
       
       if(quantity > 0)
       {
         blue += quantity * strtoint(row.children('td.cost_blue').html());
         green += quantity * strtoint(row.children('td.cost_green').html());
         yellow += quantity * strtoint(row.children('td.cost_yellow').html());
         red += quantity * strtoint(row.children('td.cost_red').html());
         foiled += quantity * strtoint(row.children('td.cost_foiled').html());
       }
     });
     
     $('#total_blue').html(blue);
     $('#total_green').html(green);
     $('#total_yellow').html(yellow);
     $('#total_red').html(red);
     $('#total_foiled').html(foiled);
   }

   $(function() {
     $('.quantity').keyup(update_total);
   });
   </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Game Room &gt; Prize Shop</h4>
<?php
echo $message;

room_display($house);
?>
  <ul class="tabbed">
   <li><a href="/myhouse/addon/game_room.php">Game Room</a></li>
   <li class="activetab"><a href="/myhouse/addon/game_room_prizes.php">Prize Shop</a></li>
  </ul>
  <table><tr>
<?php
foreach($GAME_ROOM_TICKETS as $itemname)
{
  if(array_key_exists($itemname, $your_tickets))
  {
    $details = $your_tickets[$itemname];
    echo '
      <td class="centered" style="width:100px;">
       ' . item_display($details) . '<br />
       ' . $itemname . '<br />
       &times;' . $details['qty'] . '
      </td>
    ';
  }
}
?>
  </tr></table>
  <form method="post">
  <table>
   <thead>
    <tr class="titlerow">
     <th rowspan="2"></th><th rowspan="2"></th><th rowspan="2">Prize</th rowspan="2"><th colspan="5" class="centered">Cost</th>
    </tr>
    <tr class="titlerow">
     <th><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/ticket/blue.png" width="16" height="16" alt="Blue Tickets" /></th>
     <th><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/ticket/green.png" width="16" height="16" alt="Green Tickets" /></th>
     <th><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/ticket/yellow.png" width="16" height="16" alt="Yellow Tickets" /></th>
     <th><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/ticket/red.png" width="16" height="16" alt="Red Tickets" /></th>
     <th><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/ticket/foiled.png" width="16" height="16" alt="Foiled Tickets" /></th>
    </tr>
   </thead>
   <tbody>
<?php
$rowclass = begin_row_class();

foreach($game_room_prizes as $prize=>$cost)
{
  $details = get_item_byname($prize);

  echo '
    <tr class="' . $rowclass . '">
     <td><input type="number" min="0" name="' . itemname_to_form_value($prize) . '" size="4" class="quantity" /></td><td class="centered">' . item_display($details) . '</td><td>' . $prize . '</td>
  ';

  foreach($GAME_ROOM_TICKETS as $class=>$ticket)
  {
    echo '<td class="centered ' . $class . '">';

    if(array_key_exists($ticket, $cost))
      echo $cost[$ticket];
    else
      echo '&ndash;';

    echo '</td>';
  }

  echo '</tr>';
  
  $rowclass = alt_row_class($rowclass);
}
?>
    <tr style="border-top: 1px dashed #999;">
     <td colspan="2" class="centered"><input type="submit" name="action" value="Exchange" /></td><td class="righted"><b>Total</b></td>
     <td class="centered" id="total_blue">0</td>
     <td class="centered" id="total_green">0</td>
     <td class="centered" id="total_yellow">0</td>
     <td class="centered" id="total_red">0</td>
     <td class="centered" id="total_foiled">0</td>
    </tr>
   </tbody>
  </table>
  </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
