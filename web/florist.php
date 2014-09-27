<?php
$whereat = 'florist';
$wiki = 'The_Florist';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';

if($user['show_florist'] != 'yes')
{
  header('Location: /404');
  exit();
}

$options = array();

$v_day = (date('n') == 2 && date('j') >=1 && date('j') <= 14);
$after_v_day = (date('n') == 2 && date('j') >= 15 && date('j') <= 17);

$FOR_SALE = array(
  'Purple Lilac' => 4,
  'Arbutus' => 4,
  'Periwinkle' => 4,
  'Honeysuckle' => 4,
  'Yellow Acacia' => 4,
  'Primrose' => 4,
  'White Lily' => 4,
  'Scabious' => 4,
  'Narcissus' => 4,
  'You Can Learn a Lot of Things from the Flowers' => 100,
  'Plum Pit' => 1000,
  'Orchid Seeds' => 1000,
  'Chrysanthemum Seeds' => 1000,
  'Bamboo Seeds' => 1000,
);

if($v_day)
{
  $FOR_SALE['Candy Heart'] = 10;
  $FOR_SALE['Hungry Cherub (level 0)'] = 200;
}
else if(date('n') == 12)
  $FOR_SALE['Poinsettia'] = 50;

asort($FOR_SALE);

$air_quest = get_quest_value($user['idnum'], 'Florist Air Exchange');
$shrine_quest = get_quest_value($user['idnum'], 'shrine quest');

$deleted_airs = 0;

if($_POST['action'] == 'buy')
{
  $total_cost = 0;
  $bad_amount = false;

  foreach($FOR_SALE as $item=>$value)
  {
    $form_item = str_replace(' ', '_', $item);
    $amount = (int)$_POST[$form_item];
    $total_cost += $amount * $value;
    if($amount < 0)
    {
      $bad_amount = true;
      break;
    }
  }

  if($bad_amount === true)
    $error_message = '<span class="failure">You can\'t buy <em>negative</em> flowers!  That... that doesn\'t even make sense!</span>';
  else if($total_cost == 0)
    $error_message = 'Did you want to buy something...?';
  else if($total_cost > $user['money'])
    $error_message = '<span class="failure">Sorry, but you\'re ' . ($total_cost - $user['money']) . ' moneys short...</span>';
  else
  {
    take_money($user, $total_cost, 'Purchase from The Florist');
    
    $performed_puzzle_action = 0;

    foreach($FOR_SALE as $item=>$value)
    {
      $form_item = str_replace(' ', '_', $item);
      $amount = (int)$_POST[$form_item];

      if($amount > 0)
      {
        for($x = 0; $x < $amount; ++$x)
          add_inventory($user['user'], '', $item, '', $user['incomingto']);
          
        if($item == 'White Lily' && $amount == 7)
          $performed_puzzle_action++;
        else if($item == 'Yellow Acacia' && $amount == 4)
          $performed_puzzle_action++;
        else if($item == 'Arbutus' && $amount == 6)
          $performed_puzzle_action++;
        else if($item == 'Plum Pit' && $amount == 1)
          $performed_puzzle_action++;
        else
          $performed_puzzle_action = -1000;
      }
    }

    $message = '<p>Done!  You\'ll find the items in ' . $user['incomingto'] . '.</p>';
    
    if($performed_puzzle_action == 4)
    {
      $badges = get_badges_byuserid($user['idnum']);
      if($badges['cryptographer'] == 'no')
      {
        set_badge($user['idnum'], 'cryptographer');
        $message .= '<p><i>(Also, you received the Cryptographer Badge?  Strange!)</i></p>';
      }
    }
  }
}
else if($air_quest['value'] == 1)
{
  if($_POST['submit'] == 'Trade Air of Mistrust')
    $deleted_airs = delete_inventory_byname($user['user'], 'Air of Mistrust', 1, 'storage');
  else if($_POST['submit'] == 'Trade Air of Sarcasm')
    $deleted_airs = delete_inventory_byname($user['user'], 'Air of Sarcasm', 1, 'storage');
  else if($_POST['submit'] == 'Trade Air of Enthusiasm')
    $deleted_airs = delete_inventory_byname($user['user'], 'Air of Enthusiasm', 1, 'storage');
}

$tip_quest = get_quest_value($user['idnum'], 'Tip Quest!');

if($_GET['dialog'] == 6)
{
  if($shrine_quest === false)
    add_quest_value($user['idnum'], 'shrine quest', 1);

  if($shrine_quest['value'] < 2)
  {
    $shrine_dialog = true;

    $data = $database->FetchSingle('
			SELECT COUNT(idnum) AS c
			FROM monster_inventory
			WHERE
				itemname=\'Silver Bell\' AND
				user=' . quote_smart($user['user']) . ' AND
				location=\'storage\'
		');
  
    $silver_bell_count = $data['c'];
  }
}
else if($_GET['dialog'] == 7)
{
  if($shrine_quest['value'] == 1)
  {
    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Silver Bell\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $data = $database->FetchSingle($command, 'fetching silver bells for florist');

    $silver_bell_count = $data['c'];
    
    if($silver_bell_count >= 10)
    {
      delete_inventory_byname($user['user'], 'Silver Bell', 10, 'storage');
      update_quest_value($shrine_quest['idnum'], 2);
      $shrine_quest['value'] = 2;

      $shrine_thank_you = true;
    }
  }
}
else if($_GET['dialog'] == 4)
{
  if($tip_quest['value'] == 1)
  {
    add_inventory($user['user'], '', 'Fennel Flower', '', $user['incomingto']);
    $tip_quest_dialog = true;
    update_quest_value($tip_quest['idnum'], 2);
    $tip_quest['value'] = 2;
  }
}

$special_avatars = array(
  'special-secret/delighted.png', 'special-secret/kaera-anime.png',
  'special-secret/suspicious.png', 'special-secret/rizivizi-pissed.png',
  'special-secret/sarcastic.png', 'special-secret/gizubi-dizzy.png'
);

if(in_array($user['graphic'], $special_avatars))
{
  if($air_quest === false)
  {
    $air_dialog = true;
    add_quest_value($user['idnum'], 'Florist Air Exchange', 1);
    $air_quest['value'] = 1;
  }
}

if($_GET['dialog'] == 5)
{
  if($air_quest['value'] == 1)
  {
    $air_exchange_dialog = true;
    
    $air_of_mistrust = $database->FetchSingle('
			SELECT idnum
			FROM monster_inventory
			WHERE
				itemname=\'Air of Mistrust\' AND
				user=' . quote_smart($user['user']) . ' AND
				location=\'storage\'
			LIMIT 1
		');

    $air_of_sarcasm = $database->FetchSingle('
			SELECT idnum
			FROM monster_inventory
			WHERE
				itemname=\'Air of Sarcasm\' AND
				user=' . quote_smart($user['user']) . ' AND
				location=\'storage\'
			LIMIT 1
		');

    $air_of_enthusiasm = $database->FetchSingle('
			SELECT idnum
			FROM monster_inventory
			WHERE
				itemname=\'Air of Enthusiasm\' AND
				user=' . quote_smart($user['user']) . ' AND
				location=\'storage\'
			LIMIT 1
		');
  }
}

if($deleted_airs > 0)
{
  $message = 'Oh, thanks a lot!  If you find anymore, I\'d be more than happy to make another trade.';

  add_inventory_quantity($user['user'], '', 'Large Locked Chest', 'Traded for at the Florist\'s', $user['incomingto'], $deleted_airs);
}

if($after_v_day)
{
  if($_GET['dialog'] == 'cherub')
    $message = '<p>Oh, it\'s the weirdest thing!  At the stroke of midnight on Valentine, nearly all the little cherubs just up and leave!</p><p>I really can\'t explain it, but I\'m happy a couple stayed behind.  It was the ones I\'d fed myself that seem to have stayed.  Maybe they got attached to me?</p><p>Well, I\'m sure I\'ll be seeing more of the things next year.  Maybe I\'d better stock up on some more Comfy Chairs until then...</p>';
  else
    $options[] = '<a href="florist.php?dialog=cherub">Ask why she\'s not selling Hungry Cherubs anymore.</a>';  
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Florist &gt; Flower Shop</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   $(function() {
     function update_quantity()
     {
			 var total = 0;
		 
			 $('.quantity').each(function() {

			   var quantity = parseInt($(this).val());
				 var price = parseInt($(this).parent().parent().find('.price').html());
				 
				 if(!isNaN(price) && !isNaN(quantity))
					total += quantity * price;
			 });
			 
			 $('#total').html(total);
     }
   
     $('.quantity').keyup(update_quantity);
     $('.quantity').change(update_quantity);
   });
	</script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? '<p style="color:blue;">$check_message</p>' : '') ?>
     <h4>The Florist &gt; Flower Shop</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/florist.php">Flower Shop</a></li>
      <li><a href="/florist_anonymous.php">Flower Delivery</a></li>
      <li><a href="/florist_exchange.php">Exchanges</a></li>
      <li><a href="/giftwrapping.php">Gift-wrapping</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// VANESSA ROSELLE
echo '<a href="/npcprofile.php?npc=Vanessa+Roselle"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
{
  if(!$silver_bell_dialog && $shrine_quest['value'] == 1 && $shrine_dialog !== true)
    $options[] = '<a href="?dialog=6">Ask about Silver Bells</a>';

  if(strlen($message) > 0)
  {
    $options[] = '<a href="?dialog=2">Ask about flowers</a>';

    echo $message;
  }
  else if($shrine_dialog)
  {
?>
<p>I really need some Silver Bells for this party that's coming up.  If you could bring me... oh, 10 would do, I'd be in your debt.</p>
<?php
    if($silver_bell_count >= 10)
    {
      echo '<p>Oh, you have 10 now?  Great!  May I?</p>';
      $options[] = '<a href="?dialog=7">Give her 10 Silver Bells</a>';
    }
    else if($silver_bell_count > 0)
      echo '<p>You have ' . $silver_bell_count . ' Silver Bell' . ($silver_bell_count == 1 ? '' : 's') . ' in your storage?  Just a couple more...</p>';
  }
  else if($shrine_thank_you)
  {
    echo '<p>Awesome, than-- hey... wait, um... these... aren\'t the kind of Silver Bells I meant &gt;_&gt;  I needed the <em>flower</em> Silver Bells.</p>' .
         '<p>Well, I should at least be able to sell these to buy the flowers somewhere else, but it will take me some more time...</p>' .
         '<p>Oh, sorry, I\'m thinking out loud.  But, look, do you think you could do something else for me?  The flowers are for a party Lakisha is holding, and it might take me a little longer than I expected to get a hold of them.  Could you let her know that there might be a little delay?</p>' .
         '<p>Thanks again.</p>';
  }
  else if($air_dialog)
  {
    $options[] = '<a href="?dialog=2">Ask about flowers</a>';
?>
<p>Hm?  What's that smell?  Lilac, and...</p>
<p><?= $user['display'] ?>, are you in possession of one of the Airs?</p>
<?php
  }
  else if($air_exchange_dialog)
  {
    $options[] = '<a href="?dialog=2">Ask about flowers</a>';
?>
<p>To be completely truthful, I'm collecting those Airs.  If you'd be willing to part with one, I could give you one of these Large Locked Chests I've collected.</p>
<?php
    $show_form = ($air_of_mistrust !== false || $air_of_sarcasm !== false || $air_of_enthusiasm !== false);
    
    if($show_form)
      echo '<form method="post">';

    if($air_of_mistrust !== false)
      echo ' <input type="submit" name="submit" value="Trade Air of Mistrust" class="bigbutton" />';

    if($air_of_sarcasm !== false)
      echo ' <input type="submit" name="submit" value="Trade Air of Sarcasm" class="bigbutton" />';

    if($air_of_enthusiasm !== false)
      echo ' <input type="submit" name="submit" value="Trade Air of Enthusiasm" class="bigbutton" />';

    if($show_form)
      echo '</form>';
  }
  else if($tip_quest_dialog)
  {
?>
<p>Hm?</p>
<p>Oh, the tips!  Of course!</p>
<p>Here, it's a Fennel Flower.  They're very hard to get around here.</p>
<p>Doesn't it smell delightful?</p>
<p><i>(You received a Fennel Flower!  You'll find it in <?= $user['incomingto'] ?>.)</i></p>
<?php
  }
  else if($_GET['dialog'] == 2)
  {
?>
<p>Glad you asked!  Of course flowers have been used to convey affection for thousands of years, but it wasn't until relatively recently - in the Victorian era - that specific meanings and messages have been associated with them.  Before giving someone a flower, you really should know what they mean, or you might end up sending the wrong message...</p>
<ul class="spacedlist">
 <li>Purple lilac - "you are my first love"</li>
 <li>Arbutus - "you are my only love"</li>
 <li>Periwinkle - recalls pleasant memories</li>
 <li>Honeysuckle - a token of love</li>
 <li>Fennel - a token of praise</li>
 <li>Yellow acacia - "our love is secret"</li>
 <li>Primrose - indicates a fickle love</li>
 <li>Scabious - "you are mistaken; I do not love you"</li>
 <li>Narcissus - "you love no one better than yourself"</li>
</ul>
<p>Unfortunately, I have not been able to get any Fennel for a long time.<?= $shrine_quest === false ? ' Or Silver Bells.' : '' ?></p>
<p>If you want to learn more about flowers, you should check out my book, <?= item_text_link('You Can Learn a Lot of Things from the Flowers') ?>.  Your pets might be able to learn a thing or two from it, as well!</p>
<?php
    if($shrine_quest === false)
      $options[] = '<a href="?dialog=6">Ask about Silver Bells</a>';
  }
  else
  {
    $options[] = '<a href="?dialog=2">Ask about flowers</a>';
    if($v_day)
    {
?>
<p>Welcome to my flower shop!  I'm Vanessa, the owner.</p>
<p>You've come at a strange time!  Every February, these little pink creatures show up from nowhere and start eating stuff around the shop.  A group of them devoured one of my Comfy Chairs the other day!</p>
<p>I'm not exactly complaining - they're very cute and friendly - but at the same time, it's hard to run the shop with all of them around...</p>
<p>Anyway, I certainly have more of the things than I can handle on my own.  I'd give them away for free, but I'd also kind of like to buy another Comfy Chair, so I'm selling them for <?= $FOR_SALE['Hungry Cherub (level 0)'] ?><span class="money">m</span> each.</p>
<p>Let me know if you want any.  There are plenty to go around!</p> 
<?php
    }
    else
    {
?>
<p>Welcome to my flower shop!  I'm Vanessa, the owner, and I know all about flowers, so feel free to ask me anything about them.</p>
<?php
    }
  }

  if($tip_quest['value'] == 1)
    $options[] = '<a href="?dialog=4">Ask about a reward...?</a>';
  if($air_quest['value'] == 1)
  {
    if($air_exchange_dialog)
    {
    }
    else
      $options[] = '<a href="?dialog=5">Ask about her interest in "Airs"</a>';
  }
}
include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
<form method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th>Item</th>
  <th>Price</th>
  <th>Quantity</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($FOR_SALE as $itemname=>$price)
{
  $itemdetails = get_item_byname($itemname);
  
  $input_length = ($price < 100 ? 1 : 3);
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><?= item_display($itemdetails, '') ?></td>
  <td><?= $itemname ?></td>
  <td class="righted"><span class="price"><?= $price ?></span><span class="money">m</span></td>
  <td><input type="number" min="0" name="<?= str_replace(' ', '_', $itemname) ?>" style="width:60px;" maxlength="<?= $input_length ?>" class="quantity" /></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
 <tr style="border-top: 1px solid #000;">
  <td></td>
	<th>Total</th>
	<td class="righted"><span id="total">0</span><span class="money">m</span></td>
	<td></td>
 </tr>
</table>
<p><input type="hidden" name="action" value="buy" /><input type="submit" value="Purchase" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
