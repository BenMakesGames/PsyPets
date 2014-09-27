<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/graphiclibrary.php';
require_once 'commons/marketlib.php';
require_once 'commons/favorlib.php';
require_once 'commons/questlib.php';

$reserved_names = array(
  'Hydrogen', 'Helium', 'Lithium', 'Beryllium', 'Boron', 'Carbon', 'Nitrogen', 'Oxygen', 'Fluorine', 'Neon', 'Sodium', 'Magnesium',
  'Aluminium', 'Silicon', 'Phosphorus', 'Sulfur', 'Chlorine', 'Argon', 'Potassium', 'Calcium', 'Scandium', 'Titanium', 'Vanadium',
  'Chromium', 'Manganese', 'Iron', 'Cobalt', 'Nickel', 'Copper', 'Zinc', 'Gallium', 'Germanium', 'Arsenic', 'Selenium', 'Bromine',
  'Krypton', 'Rubidium', 'Strontium', 'Yttrium', 'Zirconium', 'Niobium', 'Molybdenum', 'Technetium', 'Ruthenium', 'Rhodium',
  'Palladium', 'Silver', 'Cadmium', 'Indium', 'Tin', 'Antimony', 'Tellurium', 'Iodine', 'Xenon', 'Caesium', 'Barium', 'Lanthanum',
  'Cerium', 'Praseodymium', 'Neodymium', 'Promethium', 'Samarium', 'Europium', 'Gadolinium', 'Terbium', 'Dysprosium', 'Holmium',
  'Erbium', 'Thulium', 'Ytterbium', 'Lutetium', 'Hafnium', 'Tantalum', 'Tungsten', 'Rhenium', 'Osmium', 'Iridium', 'Platinum',
  'Gold', 'Mercury', 'Thallium', 'Lead', 'Bismuth', 'Polonium', 'Astatine', 'Radon', 'Francium', 'Radium', 'Actinium','Thorium',
  'Protactinium', 'Uranium', 'Neptunium', 'Plutonium', 'Americium', 'Curium', 'Berkelium', 'Californium', 'Einsteinium', 'Fermium',
  'Mendelevium', 'Nobelium', 'Lawrencium', 'Rutherfordium', 'Dubnium', 'Seaborgium', 'Bohrium', 'Hassium', 'Meitnerium',
  'Darmstadtium', 'Roentgenium', 'Copernicium',
  'Aphrodite', 'Apollo', 'Ares', 'Artemis', 'Athena', 'Demeter', 'Dionysus', 'Hades', 'Pluto', 'Hephaestus', 'Hera', 'Hermes',
  'Hestia', 'Poseidon', 'Zeus',
  'Aether', 'Ananke', 'Erebos', 'Erebus', 'Gaia', 'Gaea', 'Hemera', 'Chaos', 'Chronos', 'The Nesoi', 'Nyx', 'Night', 'Uranus',
  'The Ourea', 'Phanes', 'Pontus', 'Tartarus', 'Thalassa',
  'Hyperion', 'Iapetus', 'Coeus', 'Crius', 'Cronus', 'Mnemosyne', 'Oceanus', 'Phoebe', 'Rhea', 'Tethys', 'Theia', 'Themis',
  'Asteria', 'Astraeus', 'Atlas', 'Aura', 'Dione', 'Eos', 'Epimetheus', 'Eurybia', 'Eurynome', 'Helios', 'Clymene', 'Asia',
  'Lelantos', 'Leto', 'Menoetius', 'Metis', 'Ophion', 'Pallas', 'Perses', 'Prometheus', 'Selene', 'Styx',
  'Magnetar', 'Neutron Star', 'Pulsar', 'Nebula', 'White Dwarf', 'Brown Dwarf', 'Red Dwarf', 'Black Hole', 'White Hole', 'Singularity',
);

$special_offer = (($now_month == 10 && $now_day >= 21) || ($now_month == 11 && $now_day <= 3));
$special_offer = $special_offer || (($now_month == 12 && $now_day >= 12) || $now_month == 1);

$favor_cost = 500;

if($user['is_artist'] == 'yes')
{
  $free_item = get_quest_value($user['idnum'], 'artist monthly ' . $now_year . ' ' . $now_month);

  $dialog_extra = '<p class="progress">As a PsyPets artist, you may create one custom item using the Combination Station for free each month!';

  if($free_item === false)
  {
    $dialog_extra .= '</p>';

    $favor_cost = 0;
    $free_artist_item = true;
  }
  else
    $dialog_extra .= '  It looks like you\'ve already done so this month, but don\'t forget to come back in ' . date('F', strtotime('+1 month')) . '!</p>';
}

$inventory = array();

$validtypes = array_keys($categories);

$items = $database->FetchMultiple('SELECT idnum,itemname,message,message2 FROM `monster_inventory` WHERE `user`=' . quote_smart($user['user']) . ' AND `location`=\'storage\' ORDER BY `itemname`');
 
foreach($items as $item)
{
  $details = get_item_byname($item['itemname']);

  if($details['cancombine'] == 'yes')
    $inventory[] = array($item, $details);
}

$step = 1;
$step_error = 0;

$error_msgs = array();

if($_GET['costume'] == 'yes')
{
  $costume = true;
  $get_param = '?costume=yes';
}

if($_GET['details'] == 'yes')
  $combine_details = true;

if($_POST['action'] == 'submit' && $user['favor'] >= $favor_cost)
{
  if($_POST['step'] >= 1)
  {
    $itemid1 = (int)$_POST['item'][0];
    $itemid2 = (int)$_POST['item'][1];

    $item1 = get_inventory_byid($itemid1);
    $item2 = get_inventory_byid($itemid2);

    if($item1 === false || $item2 === false || $item1['user'] != $user['user'] || $item2['user'] != $user['user'])
      $error_msgs[] = 'Please select two items.';
    else if($item1['idnum'] == $item2['idnum'])
      $error_msgs[] = 'You selected the same item twice...';
    else
    {
      $details1 = get_item_byname($item1['itemname']);
      $details2 = get_item_byname($item2['itemname']);
       
      if($details1['cancombine'] == 'yes' && $details2['cancombine'] == 'yes')
      {
        if(strlen($details1['action']) > 0 && strlen($details2['action']) > 0)
          $error_msgs[] = 'Both items cannot have actions (for example, you cannot combine two books, or a book and a crystal ball).';
        else if(strlen($details1['playdesc']) > 0 && strlen($details2['playdesc']) > 0)
          $error_msgs[] = 'Both items cannot have half-hourly actions (for example, two computers, or a katamari and a computer).';
      }
      else
        $error_msgs[] = 'Please select two items.';
    }
  }

  if(count($error_msgs) > 0 && $step_error == 0)
    $step_error = 1;

  if($_POST['step'] >= 2)
  {
    $itemgraphicid = (int)$_POST['itemgraphic'];
    $itemname = trim($_POST['itemname']);

    if($costume === true)
      $itemtype = 'clothing/costume';
    else
      $itemtype = trim($_POST['itemtype']);

    $petaction = trim($_POST['petaction']);
    $useaction = trim($_POST['useaction']);

    $item_gfx = get_graphic_byid($itemgraphicid);
    $avatar_gfx = get_graphic_byid($avatargraphicid);

    if($item_gfx === false || $item_gfx['h'] != 32)
    {
      $errored = true;
      $error_msgs[] = 'Please select an item graphic.';
    }
    else if($item_gfx['recipient'] > 0 && $item_gfx['recipient'] != $user['idnum'])
    {
      $errored = true;
      $error_msgs[] = 'Please select an item graphic.';
    }

    if(strlen($itemname) < 2 || strlen($itemname) > 48)
    {
      $errored = true;
      $error_msgs[] = 'You forgot to name your item (or it\'s simply too short - 2 character minimum).';
    }
    else if(strpos($itemname, '_') !== false)
    {
      $errored = true;
      $error_msgs[] = 'Item names may not contain an underscore.  Sorry.';
    }
    else
    {
      $command = 'SELECT * FROM `monster_items` WHERE `itemname`=' . quote_smart($itemname) . ' LIMIT 1';
      $existing_item = $database->FetchSingle($command, 'fetching existing item');

      if($existing_item !== false)
      {
        $errored = true;
        $error_msgs[] = 'That item name is already in use.';
      }
      else if(in_array($itemname, $reserved_names))
      {
        $errored = true;
        $error_msgs[] = 'That item name - "' . $itemname . '" - is reserved for future use by PsyPets.';
      }
      else if(substr($itemname, 0, 10) == 'Figurine #')
      {
        $errored = true;
        $error_msgs[] = 'Item names beginning with "Figurine #" are reserved for future use by PsyPets.';
      }
      else if(substr($itemname, 0, 6) == 'Model ')
      {
        $errored = true;
        $error_msgs[] = 'Item names beginning with "Model " are reserved for future use by PsyPets.';
      }
    }
   
    if(strlen($itemtype) < 8 || strlen($itemtype) > 32)
    {
      $errored = true;
      $error_msgs[] = 'You forgot the item type (or it\'s simply too short - 8 character minimum).';
    }
    else if(preg_match('/[^a-zA-Z\/]/', $itemtype))
    {
      $errored = true;
      $error_msgs[] = 'The item type must only contain letters and slashes (not even spaces are okay).';
    }

    if(strlen($details1['playdesc']) > 0 || strlen($details2['playdesc']) > 0)
    {
      if(strlen($petaction) < 2 || strlen($petaction) > 48)
      {
        $errored = true;
        $error_msgs[] = 'You forgot the item\'s use action name (or it\'s simply too short - 2 character minimum).';
      }
      else if(preg_match('/;/', $petaction))
      {
        $errored = true;
        $error_msgs[] = 'Oh, I forgot to mention: semi-colons are not allowed in the half-hourly action.';
      }
    }

    if(strlen($details1['action']) > 0 || strlen($details2['action']) > 0)
    {
      if(strlen($useaction) < 2 || strlen($useaction) > 32)
      {
        $errored = true;
        $error_msgs[] = 'You forgot the item\'s use action name (or it\'s simply too short - 2 character minimum).';
      }
      else if(preg_match('/;/', $useaction))
      {
        $errored = true;
        $error_msgs[] = 'Oh, I forgot to mention: semi-colons are not allowed in the use action.';
      }
      else
      {
        if(strlen($details1['action']) > 0)
          $action = explode(';', $details1['action']);
        else
          $action = explode(';', $details2['action']);

        $action[0] = $useaction;
        $realaction = implode(';', $action);
      }
    }
  }

  if(count($error_msgs) > 0 && $step_error == 0)
    $step_error = 2;

  if(count($error_msgs) == 0)
  {
    if($_POST['step'] == 1)
    {
      $step = 2;

      $_POST['petaction'] = $details1['playdesc'] . $details2['playdesc'];

      if(strlen($details1['action']) > 0)
        $action = explode(';', $details1['action']);
      else
        $action = explode(';', $details2['action']);

      $_POST['useaction'] = $action[0];
      $_POST['comment'] = 'Put together by the Combination Station';
    }
    else if($_POST['step'] == 2)
    {
      $comment = trim($_POST['comment']);

      $weight = min($details1['weight'], $details2['weight']);
      $bulk = min($details1['bulk'], $details2['bulk']);
      
      $playbed = (($details1['playbed'] == 'yes' || $details2['playbed'] == 'yes') ? 'yes' : 'no');
      
      $playstat = ($details1['playstat'] != '' ? $details1['playstat'] : $details2['playstat']);
      $playfood = $details1['playfood'] + $details2['playfood'];
      $playsafety = $details1['playsafety'] + $details2['playsafety'];
      $playlove = $details1['playlove'] + $details2['playlove'];
      $playesteem = $details1['playesteem'] + $details2['playesteem'];

      $hourlystat = ($details1['hourlystat'] != '' ? $details1['hourlystat'] : $details2['hourlystat']);
      $hourlyfood = $details1['hourlyfood'] + $details2['hourlyfood'];
      $hourlysafety = $details1['hourlysafety'] + $details2['hourlysafety'];
      $hourlylove = $details1['hourlylove'] + $details2['hourlylove'];
      $hourlyesteem = $details1['hourlyesteem'] + $details2['hourlyesteem'];
/*
      echo $item_gfx["url"] . " $itemname ($itemtype)<br />\n" .
           "weight/bulk: $weight/$bulk<br />\n" .
           "half-hourly: $petaction<br />\n" .
           "use action: $realaction<br />\n" .
           "stats on use: $playfood/$playsafety/$playlove/$playesteem<br />\n" .
           "stats per hour: $hourlyfood/$hourlysafety/$hourlylove/$hourlyesteem<br />\n";

      echo "delete inventory items " . $item1["idnum"] . " and " . $item2["idnum"] . "<br />\n";
*/
      $q_itemname = quote_smart($itemname);
      $q_itemtype = quote_smart($itemtype);
      $q_graphic = quote_smart('../../' . $item_gfx['url']);
      $q_petaction = quote_smart($petaction);
      $q_useaction = quote_smart($realaction);
      $q_comment = quote_smart($comment);

      $reqs = array('str', 'sta', 'int', 'per', 'wit', 'dex', 'athletics');
      $equips = array('open', 'extraverted', 'conscientious', 'playful', 'independent',
        'str', 'dex', 'sta', 'int', 'per', 'wit',
        'mining', 'lumberjacking', 'fishing', 'painting',
        'sculpting', 'carpentry', 'jeweling', 'electronics',
        'mechanics', 'adventuring', 'hunting', 'gathering',
        'smithing', 'tailoring', 'leather', 'crafting', 'binding',
        'chemistry', 'piloting', 'gardening', 'stealth',
        'athletics', 'fertility',
      );
      
      $yes_or_no_equips = array(
        'equip_goldenmushroom',
        'equip_vampire_slayer',
        'equip_berry_craft',
        'equip_were_killer',
        'equip_pressurized',
        'equip_flight',
        'equip_fire_immunity',
        'equip_chill_touch',
        'equip_healing',
        'equip_more_dreams'
      );

      foreach($reqs as $req)
        $equip_reqs['req_' . $req] = max($details1['req_' . $req], $details2['req_' . $req]);

      foreach($equips as $equip)
        $equip_equips['equip_' . $equip] += $details1['equip_' . $equip] + $details2['equip_' . $equip];

      foreach($yes_or_no_equips as $equip)
        $equip_equips[$equip] = (($details1[$equip] == 'yes' || $details2[$equip] == 'yes') ? '\'yes\'' : '\'no\'');

      $is_equipment = (($details1['is_equipment'] == 'yes' || $details2['is_equipment'] == 'yes') ? 'yes' : 'no');
      $reincarnateonly = (($details1['equipreincarnateonly'] == 'yes' || $details2['equipreincarnateonly'] == 'yes') ? 'yes' : 'no');

      // create the item
      $command = '
        INSERT INTO monster_items
        (
          `itemname`, `itemtype`, `custom`,
          `bulk`, `weight`,
          `graphic`,

          `is_equipment`, `equipreincarnateonly`,

          `' . implode('`, `', array_keys($equip_reqs)) . '`,
          `' . implode('`, `', array_keys($equip_equips)) . '`,

          `playdesc`,
          `playbed`, `playstat`, `playfood`, `playsafety`, `playlove`, `playesteem`,
          `hourlystat`, `hourlyfood`, `hourlysafety`, `hourlylove`, `hourlyesteem`,
          `action`,
          `rare`, `nosellback`
        )
        VALUES
        (
          ' . $q_itemname . ', ' . $q_itemtype . ', \'yes\',
          ' . $bulk . ', ' . $weight . ',
          ' . $q_graphic . ',

          \'' . $is_equipment . '\', \'' . $reincarnateonly . '\',
          ' . implode(', ', $equip_reqs) . ',
          ' . implode(', ', $equip_equips) . ',

          ' . $q_petaction . ',
          ' . quote_smart($playbed) . ', ' . quote_smart($playstat) . ', ' . $playfood . ', ' . $playsafety . ', ' . $playlove . ', ' . $playesteem . ',
          ' . quote_smart($hourlystat) . ', ' . $hourlyfood . ', ' . $hourlysafety . ', ' . $hourlylove . ', ' . $hourlyesteem . ',
          ' . $q_useaction . ',
          \'yes\', \'yes\'
        )
      ';

      $database->FetchNone($command, 'creating new combination station item');

      // create the inventory item reference
      $command = 'INSERT INTO monster_inventory (`user`, `creator`, `itemname`, `message`, `location`) VALUES ' .
                 '(' . quote_smart($user['user']) . ', ' . quote_smart('u:' . $user['idnum']) . ", $q_itemname, $q_comment, 'storage/incoming')";
      $database->FetchNone($command, 'creating new inventory item');

      $id = $database->InsertID();

      flag_new_incoming_items($user['user']);

      // record the Favor
      spend_favor($user, $favor_cost, 'custom item - "' . $itemname . '"', $id);

      if($free_artist_item === true)
        add_quest_value($user['idnum'], 'artist monthly ' . $now_year . ' ' . $now_month, 1);

      // delete the two items used
      $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . $item1['idnum'] . ',' . $item2['idnum'] . ') LIMIT 2';
      $database->FetchNone($command, 'deleting used items');

      $uploader = get_user_byid($item_gfx['uploader']);

      record_graphic_use($itemgraphicid, $item_gfx, $uploader);

      if($uploader !== false)
      {
        $badges = get_badges_byuserid($uploader['idnum']);
        if($badges['artist'] == 'no')
        {
          set_badge($uploader['idnum'], 'artist');
          $extra = '<br /><br />{i}(You won the Artist Badge!){/}';
        }

        psymail_user($uploader['user'], 'psypets', 'Your graphic from the Graphic Library was used!', "{r " . $user["display"] . "} has used your {i}" . $item_gfx["title"] . "{/} graphic to make a custom item: $itemname.$extra");
      }

      require_once 'commons/dailyreportlib.php';
      record_daily_report_stat('Combination Station: Someone Made a Custom Item', 1);

      $_POST = array();

      $step = 3;
    }
    else
      exit();
  }
  else
    $step = $step_error;
}

$itemgraphics = get_graphics_byuserid($user["idnum"], 32);

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Smithery &gt; Combination Station</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="smith.php">The Smithery</a> &gt; Combination Station</h4>
     <ul class="tabbed">
      <li><a href="smith.php">Smith</a></li>
      <li><a href="repair.php">Repair</a></li>
      <li><a href="af_getrare2.php">Unique Item Shop</a></li>
      <li class="activetab"><a href="af_combinationstation3.php<?= $get_param ?>">Combination Station</a></li>
<?php
if($special_offer)
  echo '<li><a href="specialoffer_smith.php">Special Offer <i style="color:red;">ooh!</i></a></li>';
?>
<!--      <li><a href="af_replacegraphic.php">Broken Image Repair</a></li>-->
     </ul>
<a href="npcprofile.php?npc=Nina+Faber"><img src="//saffron.psypets.net/gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" /></a>
<?php
include 'commons/dialog_open.php';

if(count($error_msgs) > 0)
{
  foreach($error_msgs as $error_message)
    echo '<p class="failure">' . $error_message . "</p>\n";
}
else if($message)
  echo '<p class="success">' . $message . "</p>\n";
else if($step == 1)
{
  if($combine_details === true)
  {
?>
<p>There are a couple kind of combinations that are not possible...</p>
<ul>
 <li>
  Some items simply can not be combined.  The encyclopedia can tell you if a particular item is "combinable" or not.
  <ul>
   <li>Food items are never combinable.</li>
   <li>Items which have a chance to destroy themselves when used (for example, Shrinky Ray Guns) are never combinable.</li>
   <li>Custom-made items, monthly items, and some other unique items, are never combinable.</li>
  </ul>
 </li>
 <li>You cannot combine two items that both have actions (for example, any two books).</li>
 <li>You cannot combine two items that both have half-hourly "toy" actions (for example, any two board games).</li>
</ul>
<p>Other than that, combine away!  You could make a sword containing the text from The Butterfly That Stamped, or a cloak that you can play Go on, or any other number of bizarre combinations!</p>
<?php
  }
  else
  {
?>
<p>I can take any two of your items (from storage) and combine them into a single, new item.  This new item will have the combined properties of both, and you may freely customize its name and appearance.</p>
<p>The process costs <strong><?= $favor_cost ?> Favor</strong>.<?php
    if($user['favor'] < $favor_cost)
      echo ' You\'ll need to <a href="buyfavors.php">buy some</a>.';
?></p>
<?php
    echo $dialog_extra;

    if($costume == true)
      echo '<p><strong>Costumes must be equipable!</strong>  To make an equipable custom item, you should choose at least one equipment to go in to the mix.  The exact equipment does not matter (so long as your pet can equip it!) - it could be a sword, nailgun, or anything.</p>';
  }
}
else if($step == 2)
{
?>
<p><?= $item1["itemname"] ?> and <?= $item2["itemname"] ?>, hm?  Yeah, I can do this.</p>
<p>What do you call this <?= $item1["itemname"] ?>/<?= $item2["itemname"] ?> combination?</p>
<?php
}
else if($step == 3)
{
?>
<p>Done!  This is quite the item, this <?= $itemname ?>.  I will have to remember it.</p>
<p>Anyway, I put it into <a href="incoming.php">Incoming</a>.  And if you ever want another one, come to my <a href="af_getrare2.php">Unique Item Shop</a>.</p>
<?php
}
include 'commons/dialog_close.php';

if($step == 1)
{
  if($combine_details !== true)
    $options[] = '<a href="af_combinationstation3.php?details=yes' . ($costume === true ? '&costume=yes' : '') . '">Ask for details on which items may be combined</a>';

  if($costume === true)
    $options[] = '<a href="af_combinationstation3.php">Ask about making normal equipment</a>';
  else if(date('m') == 10)
    $options[] = '<a href="af_combinationstation3.php?costume=yes">Ask about making Halloween costumes</a>';

  $options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

  if(count($options) > 0)
    echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
}

if($user['favor'] >= $favor_cost)
{
  if(count($itemgraphics) < 1)
  {
    if($step < 3)
      echo '<p>There are no item graphics available in the <a href="graphicslibrary.php">Graphics Library</a> at this time.  You will need to upload one yourself, or get someone else to upload one for you.</p>';
  }
  else
  {
    if($step == 1)
    {
      if(count($inventory) >= 2)
      {
?>
     <h5>Select two items from your Storage to combine</h5>
     <form action="af_combinationstation3.php<?= $get_param ?>" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Comment</th>
      </tr>
<?php
        $bgcolor = begin_row_class();

        foreach($inventory as $item)
        {
?>
      <tr class="<?= $bgcolor ?>">
       <td><input type="checkbox" name="item[]" value="<?= $item[0]['idnum'] ?>" /></td>
       <td class="centered"><?= item_display_extra($item[1]) ?></td>
       <td><?= $item[0]['itemname'] ?></td>
       <td><?= $item[0]['message'] . (strlen($item[0]['message2']) > 0 ? ' / ' . $item[0]['message2'] : '') ?></td>
      </tr>
<?php
          $bgcolor = alt_row_class($bgcolor);
        }
?>
     </table>
     <p><input type="hidden" name="step" value="1" /><input type="hidden" name="action" value="submit" /><input type="submit" value="Next &gt;" /></p>
     </form>
<?php
      }
      else
        echo "     <p>There are no two items in your storage which can be combined in this way.</p>\n";
    }
    else if($step == 2)
    {
      if($costume == true)
      {
        $note = '<p>(Costumes must be of type "clothing/costume" to work with Halloween trick-or-treating.)</p>';
        $disabled = ' disabled';

        if(strlen($_POST['itemtype']) == 0)
          $_POST['itemtype'] = 'clothing/costume';
          
        if(strlen($details1['equipeffect']) == 0 && strlen($details1['equipeffect']) == 0)
          echo '<p><strong>Note:</strong> you did not select an equipable item, therefore the combined item will not be equipable.  If you want to make a costume for trick-or-treating, it needs to be equipable!  If you want to select different items, press your browser\'s back button to select them before going any further.</p>';
      }
      else
      {
        $note = '(ex: "craft/accessory/hat" or "whateverNoOneCares" - without the quotes; letters and slashes only)';
        $disabled = '';
      }
?>
     <form action="af_combinationstation3.php<?= $get_param ?>" method="post">
     <div style="display: none;">
     <input type="checkbox" name="item[]" value="<?= $itemid1 ?>" checked="checked" />
     <input type="checkbox" name="item[]" value="<?= $itemid2 ?>" checked="checked" />
     </div>
     <h5>Item Name</h5>
     <p><input name="itemname" maxlength="48" value="<?= $_POST['itemname'] ?>" /></p>
     <h5>Item Type</h5>
     <?= $note ?></p>
     <p><input name="itemtype" maxlength="32" value="<?= $_POST['itemtype'] ?>"<?= $disabled ?> /></p>
     <h5>Item Graphic</h5>
<?php include 'commons/gl_warning.php'; ?>
<table>
<tr>
<?php
$i = 0;
foreach($itemgraphics as $graphic)
{
  if($i % 4 == 0 && $i > 0)
    echo "</tr><tr>\n";
?>
<td align="center">
 <table><tr><td><img src="<?= $graphic['url'] ?>" /></td><td bgcolor="#f0f0f0"><img src="<?= $graphic['url'] ?>" /></td></tr></table>
 <input type="radio" name="itemgraphic" value="<?= $graphic['idnum'] ?>" />
</td>
<?php
  ++$i;
}
?>
</tr>
</table>
<?php
if(strlen($details1["playdesc"]) > 0 || strlen($details2["playdesc"]) > 0)
{
  if(strlen($details1['playdesc']) > 0)
    $play_desc = $details1['itemname'] . '\'s "' . $details1['playdesc'] . '"';
  else
    $play_desc = $details2['itemname'] . '\'s "' . $details2['playdesc'] . '"';
?>
     <h5>Half-hourly Action</h5>
     <p>When you and a pet play with this item, it will perform the <?= $play_desc ?>, but you may call it whatever you like...</p>
     <p><input name="petaction" value="<?= $_POST["petaction"] ?>" maxlength="32" /></p>
<?php
}

if(strlen($details1["action"]) > 0 || strlen($details2["action"]) > 0)
{
  if(strlen($details1['action']) > 0)
  {
    $act = explode(';', $details1['action']);
    $action_desc = $details1['itemname'] . '\'s "' . $act[0] . '"';
  }
  else
  {
    $act = explode(';', $details2['action']);
    $action_desc = $details2['itemname'] . '\'s "' . $act[0] . '"';
  }
?>
     <h5>Use Action</h5>
     <p>When you use this item, it will perform the <?= $action_desc ?>, but you may call it whatever you like...</p>
     <p><input name="useaction" value="<?= $_POST["useaction"] ?>" maxlength="16" /></p>
<?php
}
?>
     <h5>Item Comment</h5>
     <p><input name="comment" value="<?= $_POST["comment"] ?>" maxlength="48" size="48" /></p>
     <p><input type="hidden" name="step" value="2" /><input type="hidden" name="action" value="submit" /><input type="submit" value="Create!" /></p>
     </form>
<?php
    }
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
