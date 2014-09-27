<?php
$whereat = 'smithery';
$wiki = 'The_Smithery';
$require_petload = 'no';
$url = 'smith.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';
require_once 'commons/economylib.php';

$special_offer = (($now_month == 10 && $now_day >= 21) || ($now_month == 11 && $now_day <= 3));
$special_offer = $special_offer || (($now_month == 12 && $now_day >= 12) || $now_month == 1);

$this_smith = $database->FetchMultipleBy('SELECT * FROM monster_smith WHERE type=\'smith\' ORDER BY cost ASC', 'idnum');

if(date('n j') == '3 17')
{
  $patrick_dialog = true;

  $add_me = array('idnum' => -1, 'secret' => 'no', 'type' => 'smith',
    'supplies' => '3|Gold,1|5-Leaf Clover', 'makes' => 'Gold 5-Leaf Clover',
    'cost' => 0);

  $this_smith[$add_me['idnum']] = $add_me;

  $add_me = array('idnum' => -2, 'secret' => 'no', 'type' => 'smith',
    'supplies' => '3|Silver,1|5-Leaf Clover', 'makes' => 'Silver 5-Leaf Clover',
    'cost' => 0);

  $this_smith[$add_me['idnum']] = $add_me;
}

// if not false, its value is echo'd as the smith's dialog
$dialog_general = false;

$inventory = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    `user`=' . quote_smart($user['user']) . '
    AND `location`=\'storage\'
  GROUP BY itemname
', 'itemname');

if(array_key_exists((int)$_POST['smithid'], $this_smith))
{
  include 'commons/smith/smith.php';
}

if(date('n j') == '3 14')
{
  $quest_pi = get_quest_value($user['idnum'], 'Pi ' . date('Y'));

  if($quest_pi === false)
  {
    if($inventory['Gold']['qty'] >= 5 && $inventory['Aquite']['qty'] >= 2 && $inventory['Zephrous']['qty'] >= 1)
    {
      if($_POST['action'] == 'piaccept')
      {
        $smithy = get_user_byuser('nfaber', 'idnum');

        delete_inventory_byname($user['user'], 'Gold', 5, 'storage');
        delete_inventory_byname($user['user'], 'Aquite', 2, 'storage');
        delete_inventory_byname($user['user'], 'Zephrous', 1, 'storage');
        add_inventory($user['user'], 'u:' . $smithy['idnum'], 'Pi', 'Forged at the Smithery on 3.14', $user['incomingto']);
        add_quest_value($user['idnum'], 'Pi ' . date('Y'), 1);
        $pi_day = 3;
      }
      else
        $pi_day = 2;
    }
    else
      $pi_day = 1;
  }
  else
    $pi_day = 0;
}
else
  $pi_day = 0;

$quest_totemgarden = get_quest_value($user['idnum'], 'TotemGardenActivation');

if($quest_totemgarden['value'] == 1)
{
  if($_GET['dialog'] == 2)
  {
    $user['show_totemgardern'] = 'yes';

    update_quest_value($quest_totemgarden['idnum'], 2);
    $command = 'UPDATE monster_users SET show_totemgardern=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, $url);

    $dialog_totem = true;
  }
  else
    $options[] = '<a href="' . $url . '?dialog=2">Ask for advice on Totem Pole building.</a>';
}

$quest_plushy_collection = get_quest_value($user['idnum'], 'Plushy Collection');

if($quest_plushy_collection === false)
{
  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=\'Desikh Plushy\' LIMIT 1';
  $result = $database->FetchSingle($command, $url);
  
  if($result !== false)
  {
    add_quest_value($user['idnum'], 'Plushy Collection', 1);
  
    $plushy_intro = true;
    $ask_for_plushy = true;
    $collection_step = 1;
  }
}
else
{
  $collection_step = (int)$quest_plushy_collection['value'];

  if($collection_step <= 10)
    $ask_for_plushy = true;
}

if($_GET['dialog'] == 12 && $collection_step == 11)
  $koi_plushy_talk = true;

if($ask_for_plushy)
{
  include 'commons/smith/plushys.php';
}

$shrine_quest = get_quest_value($user['idnum'], 'shrine quest');

if($shrine_quest['value'] == 4)
{
  if($_GET['dialog'] == 10)
  {
    update_quest_value($shrine_quest['idnum'], 5);
    $shrine_quest['value'] = 5;
    $caramel_dialog = true;
  }
}
else if($shrine_quest['value'] == 5)
{
  if($_GET['dialog'] == 11)
  {
    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Caramel Squares\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $data = $database->FetchSingle($command, 'fetching caramel squares for nina');

    $caramel_count = $data['c'];

    if($caramel_count >= 50)
    {
      delete_inventory_byname($user['user'], 'Caramel Squares', 50, 'storage');
      $caramel_thankyou = true;
      update_quest_value($shrine_quest['idnum'], 6);
      $shrine_quest['value'] = 6;

      $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
                 'VALUES (\'construct\', ' . $user['idnum'] . ', \'13\', \'0\', \'Nina started this construction.\')';
      $database->FetchNone($command, 'starting project for house add-on');
    }
    else
      $caramel_dialog_2 = true;
  }
}

if($user['idnum'] < 34298 && $user['donated'] == 'yes')
{
  $free_axe_quest = get_quest_value($user['idnum'], 'free dragonaxe');
  if($free_axe_quest === false)
  {
    $give_axe = true;
    add_quest_value($user['idnum'], 'free dragonaxe', 1);
    add_inventory($user['user'], '', 'Dragonaxe', 'Given to you by Nina', $user['incomingto']);
  }
}

$jerky_quest = get_quest_value($user['idnum'], 'spicy jerky quest');
if($jerky_quest['value'] == 1)
{
  if($_GET['dialog'] == 'spicyjerky_suggest')
  {
    $dialog_general = '<p>Ha!  What, a kind of Spicy Jerky De-spicifier, or something?</p><p>Sorry, sorry: I just never heard of such a thing.</p><p>Anyway, why would you want to do that?</p>';
    $options[] = '<a href="smith.php?dialog=spicyjerky_explain">Explain that it\'s for fishing</a>';
  }
  else if($_GET['dialog'] == 'spicyjerky_explain')
  {
    $dialog_general = '<p>Fishing?  I never could never go fishing.</p><p>Anyway... to make this device... I\'m sure I could make one, if I knew where to start.  I am a terrible cook, you know.</p>';
    $options[] = '<a href="smith.php?dialog=spicyjerky_dotdotdot">...</a>';
  }
  else if($_GET['dialog'] == 'spicyjerky_dotdotdot')
  {
    $dialog_general = '<p>Marian - you know, the librarian - I hear she is an excellent cook.  She could tell us how... how to despicify Spicy Jerky.</p><p>I have an idea: you go ask her how to do this, then come back and tell me, and I\'ll make a Spicy Jerky Despicifier for you.</p>';
    $options[] = '<a href="smith.php?dialog=spicyjerky_quest">Agree to do it!</a>';
  }
  else if($_GET['dialog'] == 'spicyjerky_quest')
  {
    $dialog_general = '<p>This will be an interesting project.</p><p>See you soon then!</p>';
    update_quest_value($jerky_quest['idnum'], 2);
  }
  else
    $options[] = '<a href="smith.php?dialog=spicyjerky_suggest">Ask her about removing the spice from Spicy Jerky</a>';
}
else if($jerky_quest['value'] == 4)
{
  if($_GET['dialog'] == 'despicifier_instructions')
  {
    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Instructions for Despicifying Spicy Jerky\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $instruction_data = $database->FetchSingle($command, 'fetching spicy jerky count');

    if($instruction_data['c'] >= 1)
    {
      $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Colander\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
      $colander_data = $database->FetchSingle($command, 'fetching colander count');

      $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Sugar Beater\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
      $beater_data = $database->FetchSingle($command, 'fetching sugar beater count');

      $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Milk Separator\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
      $separator_data = $database->FetchSingle($command, 'fetching milk separator count');

      if($colander_data['c'] >= 1 && $beater_data['c'] >= 1 && $separator_data['c'] >= 1)
      {
        delete_inventory_byname($user['user'], 'Colander', 1, 'storage');
        delete_inventory_byname($user['user'], 'Sugar Beater', 1, 'storage');
        delete_inventory_byname($user['user'], 'Milk Separator', 1, 'storage');
        delete_inventory_byname($user['user'], 'Instructions for Despicifying Spicy Jerky', 1, 'storage');

        $dialog_general = '
          <p>It seems like you have all the materials to make one of them.</p>
          <p>I will begin immediately...</p>
          <p>You know, making your Despicifier is nothing really complicated, just something that I would not have thought of before.</p>
          <p>And here you go!  Everything is done!</p>
          <p><i>(You received a Spicy Jerky Despicifier!  Find it in ' . $user['incomingto'] . '.)</i></p>
          <p>Well, it was a fun project, at least.  But what is this about?  Fishing?  Well good luck with that.</p>
        ';

        $smithy = get_user_byuser('nfaber', 'idnum');

        add_inventory($user['user'], 'u:' . $smithy['idnum'], 'Spicy Jerky Despicifier', 'Given to you by Nina', $user['incomingto']);

        update_quest_value($jerky_quest['idnum'], 5);
      }
      else
        $dialog_general = '<p>Perfect, ' . $user['display'] . '!  With that, I can make something to automate the process.  But I need cooking tools.  A Colander, Sugar Beater, and Milk Separator should be sufficient.</p><p>If you can get them, I will make a Spicy Jerky Despicifier.</p>';
    }
    else
      $_GET['msg'] = '86';
  }
  else
    $options[] = '<a href="smith.php?dialog=despicifier_instructions">Show her the Instructions for Despicifying Spicy Jerky</a>';
}

if($_GET['dialog'] == 'pyrium')
  $dialog_general = '
    <p>Oh, Pyrium is a material capable of generating the high heat needed for certain jobs.</p>
    <p>A chemist in a lab can do it easily, but I\'m not a chemist.  You should talk with Thaddeus if you want to know more about chemistry.</p>
  ';
else if($dialog_general == '')
  $options[] = '<a href="smith.php?dialog=pyrium">Ask about Pyrium</a>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Smithery</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Smithery &gt; Smith</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="<?= $url ?>">Smith</a></li>
      <li><a href="repair.php">Repair</a></li>
      <li><a href="af_getrare2.php">Unique Item Shop</a></li>
      <li><a href="af_combinationstation3.php">Combination Station</a></li>
<?php
if($special_offer)
  echo '<li><a href="specialoffer_smith.php">Special Offer <i style="color:red;">ooh!</i></a></li>';
?>
<!--      <li><a href="af_replacegraphic.php">Broken Image Repair</a></li>-->
     </ul>
<?php
// SMITHY NPC NINA
echo '<a href="npcprofile.php?npc=Nina+Faber"><img src="gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" /></a>';

if(strlen($_GET['msg']) > 0)
  $error_messages[] = form_message(explode(',', $_GET['msg']));

include 'commons/dialog_open.php';
if(count($error_messages) > 0)
  echo '<p>' . implode('</p><p>', $error_messages) . '</p>';
else
{
  if($dialog_general !== false)
    echo $dialog_general;
  else if($give_axe === true)
    echo '<p>Ah, ' . $user['display'] . '!  You know I make Dragonaxes?  One of the most beautiful creations of mine... I give to anyone who purchases Favor.</p><p>That\'s you, friend!  Do not be shy!  Take it!</p><p><i>(You received a Dragonaxe.  Find it in ' . $user['incomingto'] . '.)</i></p>';
  else if($plushy_quest_tease)
    echo '<p><i>*Tsk!*</i>  You do not have one in Storage!  And I was all excited, too...</p>';
  else if($plushy_quest_intro)
    echo '<p>Thank you a million ties, ' . $user['display'] . '!  I love these little things.  In fact, I try to make a small collection... you think you could help me?</p>' .
         '<p>Hm... what about a ' . $PLUSHY_QUEST_LIST[$collection_step - 1] . '...</p>';
  else if($plushy_quest_update)
  {
    $messages1 = array('Haha!  Great!', 'Thanks again, ' . $user['display'] . '.', 'This will do perfectly!', '<em>Just</em> what I needed - thank you!');
    $messages2 = array('Let\'s see... next is %s...', 'Hm.  How about a %s?', 'Oh, I am still missing the %s!', 'Do you think you could get me a %s next?');
    echo '<p>' . $messages1[array_rand($messages1)] . '</p>' .
         '<p>' . sprintf($messages2[array_rand($messages2)], $PLUSHY_QUEST_LIST[$collection_step - 1]) . '</p>';

    if($plushy_quest_hint === true)
      echo '<p>Oh, you know that some Plushies can be found in Dyed Eggs, and some only in Chocolate Eggs, yes?  I had the most difficulty in finding these myself.  Just bad luck, I guess.</p>';
  }
  else if($plushy_quest_done)
  {
    echo '<p>Wow!  This is the last of them, ' . $user['display'] . '!  I really cannot thank you enough!  But here, at least take this Deed to 100 Units.</p>' .
         '<p>You know what, take this Plushy Collector Badge, too.  Accidentally I ended up with an extra from the myPlushy convention.</p>' .
         '<p>Well, thank you again for your help.  See you around!</p>' .
         '<p><i>(You won the Plushy Collector Badge!  Also, you received a Deed to 100 Units!  You can find it in ' . $user['incomingto'] . '.)</i></p>';
  }
  else if($_GET['dialog'] == 4)
  {
    echo '<p>Hm... there is a ' . item_text_link('Fancy Chess Set') . '.  I\'ve did it for a while.  It is one of the hardest to put together, because you need all the pieces.  Chess Club Treasonists drop them, but there are some other monsters that also carry pieces.</p>' .
         '<p>The ' . item_text_link('Medieval Suit of Armor') . ' is another old one.  Only very strong pets can equip this heavy armor.  Also, ' . item_text_link('Antique Armor') . ' is hard to find!  I heard Achromatic Dragons sometimes have this treasure, and some people say you can get it as a gift from the Gods if you make large Temple donations.</p>' .
         '<p>' . item_text_link('Sea\'s Embrace') . ' is a relatively new feature offered.  The ' . item_text_link('Conch Shell') . 's can be found in only some places, so it is difficult to get 100 of them.  If you have a pet that always finds them, you are lucky!</p>' .
         '<p>Finally, ' . item_text_link('Mythic Edge') . ' is a powerful weapon, although I heard that there are things more powerful.  The ' . item_text_link('50-foot Red Ribbon') . ' is randomly evoked by a ' . item_text_link('Wand of Wonder') . ', which is also difficult to find.</p>' .
         '<p>Oh, I\'ll also tell you that I can make more things, but to save space, not all are listed here.  However, if you come back with the right materials to do something else, I\'ll let you know.</p>';
  }
  else if($koi_plushy_talk)
    echo '<p>Have you ever seen Koi Plushies; is difficult to get hold of them!</p><p>Bring me a plush Bekko, Kohaku, and Showa Koi, and I will make a plush Yamabuki Ogon Koi.</p>';
  else if($pi_day == 3)
    echo '<p>Great!  I put it in ' . $user['incomingto'] . '.  I hope you enjoy!</p>';
  else if($pi_day == 2)
    echo '<p>5 Gold, 2 Aquite and 1 Zephrous: that will do!  With that I can make a sculture of Pi.</p>' .
         '<form action="' . $url . '" method="post"><input type="hidden" name="action" value="piaccept" /><input type="submit" value="Let\'s Do It" /></form>';
  else if($pi_day == 1)
    echo '<p>You\'ve probably already noticed it, but today is the Pi day (3.14).</p><p>I\'m making sculptures of Pi for people!</p><p>If you can bring me the materials, I\'ll make one for you.  5 Gold, 2 Aquite and 1 Zephrous.  (Materials must be in your Storage.)</p>';
  else if($dialog_totem === true)
  {
    echo '<p>Glad you asked.  See, residents are not allowed to build their own Totem Poles.  There was an accident a while ago, and now there are all kinds of rules and regulations about Totem Pole building.</p>' .
         '<p>However, there is this Totem Pole "Garden" that\'s allowed to manage personal Totem Poles.  It was set up on the southern tip of the island by Matalie a few years back.</p>' .
         '<p>If you\'re interested, you should definitely give it a look.  I like Totem Poles, myself, but have never had the time to build a real nice one.  In fact, how \'bout this:  if you can build up a real nice one, I\'ll trade you a <a href="encyclopedia2.php?item=Hephaestus%27%20Hammer">Hephaestus\' Hammer</a> for it.</p>' .
         '<p><i>(The Totem Pole Garden has been revealed to you!  You can find it in the Recreation menu.)</i></p>';
  }
  else if($caramel_dialog)
  {
    echo '<p>I know, I know, but Lakisha wants me to bring Caramel Squares - 50 of them - but I can not cook to save my life!</p>' .
         '<p>I am a little desperate here.  How about this: if you bring 50 Caramel Squares, I will create an interesting project for your home.  Sounds good?</p>';
  }
  else if($caramel_dialog_2)
  {
    if($caramel_count > 0)
      echo '<p>I do not want to sound demanding, but I need 50 Caramel Squares; there are only ' . $caramel_count . ' in your storage.</p>';
    else
      echo '<p>You do not seem to have Caramel Squares in your Storage...</p>';
  }
  else if($caramel_thankyou)
  {
    echo '<p>Thank you, thank you!  Hundred times, thank you!</p>' .
         '<p>As I promised, I set the stage for an interesting project at home. With luck, your pets should be completed in no time!</p>' .
         '<p>And thank you again, ' . $user['display'] . '.  And do not worry about RSVP - I\'ll let Lakisha know now.</p>';
  }
  else if($patrick_dialog)
    echo '<p>I\'m smithin\' Gold an\' Silver 5-Leaf Clovers today, if you\'re interested.  Free of charge!  Just bring me the materials needed...</p>';
  else
  {
    $easter2007 = (date('M d Y') == 'Apr 08 2007' || date('M d Y') == 'Apr 07 2007' || date('M d Y') == 'Apr 06 2007');

    if($easter2007)
      echo '<p>I do not know what Julio has spoken... Never have I made him, or any other HERG researcher, Plastic Eggs.</p>';
    else
      echo '<p>I can create some interesting items from the Storage material for you if you want. Search options and let me know if you want something.</p>';
  }
  
  if($_GET['dialog'] != 4)
    $options[] = '<a href="' . $url . '?dialog=4">Ask about some of the more unique smithing options</a>';

  if($plushy_intro === true)
    echo '<p>Oh! Is it a plush Desikh there, rotting in your storage?</p>';

  if($ask_for_plushy === true)
    $options[] = '<a href="' . $url . '?dialog=3">Give ' . $PLUSHY_QUEST_LIST[$collection_step - 1] . ' to Nina</a>';

  if($shrine_quest['value'] == 4)
    $options[] = '<a href="' . $url . '?dialog=10">Remind about Lakisha\'s RSVP</a>';
  else if($shrine_quest['value'] == 5)
    $options[] = '<a href="' . $url . '?dialog=11">Give 50 Caramel Squares</a>';

  if($collection_step == 11 && $_GET['dialog'] != 12)
    $options[] = '<a href="' . $url . '?dialog=12">Ask about plushies</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>' , $options) . '</li></ul>';
?>
     <form action="<?= $url ?>" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th><nobr>Item to Forge</nobr></th>
       <th></th>
       <th><nobr>Items Needed</nobr></th>
       <th><nobr>Additional Cost</nobr></th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($this_smith as $smith_recipe)
{
  $ingredients = explode(',', $smith_recipe['supplies']);
  $itemcounts = array();
  foreach($ingredients as $item)
  {
    if(strpos($item, '|') !== false)
    {
      $data = explode('|', $item);
      $itemcounts[$data[1]] += $data[0];
    }
    else
      $itemcounts[$item]++;
  }

  $ok = true;
  $enough_money = true;

  $itemdescripts = array();
  foreach($itemcounts as $item=>$count)
  {
    if($inventory[$item]['qty'] < $count)
    {
      if($inventory[$item]['qty'] == 0)
        $inventory[$item]['qty'] = '0';

      $itemdescripts[] = item_text_link($item, 'failure') . ' <span class="failure">(' . $inventory[$item]['qty'] . ' / ' . $count . ')</span>';
      $ok = false;
    }
    else
      $itemdescripts[] = item_text_link($item) . ' (' . $inventory[$item]['qty'] . ' / ' . $count . ')';
  }

  if($smith_recipe['makes'] == 'Pot of Gold')
    $real_cost = $smith_recipe['cost'];
  else
    $real_cost = value_with_inflation($smith_recipe['cost']);

  if($user['money'] < $real_cost)
    $enough_money = false;

  $supplies = implode('<br />', $itemdescripts);

  if($ok == false && $smith_recipe['secret'] == 'yes')
    continue;
?>
      <tr class="<?= $rowclass ?>">
<?php
  if($ok && $enough_money)
  {
?>
       <td><input type="radio" name="smithid" value="<?= $smith_recipe['idnum'] ?>" /></td>
<?php
  }
  else
  {
?>
       <td><input type="radio" name="smithid" value="0" disabled /></td>
<?php
  }
  
  $item_details = get_item_byname($smith_recipe['makes']);
?>
       <td class="centered"><?= item_display($item_details, '') ?>
       <td><?= $smith_recipe['makes'] ?></td>
       <td></td>
       <td>
        <?= $supplies ?>
       </td>
<?php
  if($enough_money)
    echo '<td class="centered">' . $real_cost . '<span class="money">m</span></td>';
  else
    echo '<td class="centered failure">' . $real_cost . '<span class="money">m</span></td>';
?>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p>Quantity: <input type="number" min="1" style="width:60px;" name="quantity" value="1" /> <input type="submit" value="Forge" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
