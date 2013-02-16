<?php
/* updates god moods, and maybe starts an auction
*/

$_GET['maintenance'] = 'no';

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/newslib.php';

$now = time();

	$gods = $database->FetchMultipleBy('SELECT * FROM monster_gods', 'id');

  // ============================

  $highest = -1;
  $lowest = -1;

  foreach($gods as $id=>$god)
  {
    if($highest == -1)
    {
      $highest = $id;
      $lowest = $id;
    }
    else if($god['contributions'] > $gods[$highest]['contributions'])
      $highest = $id;
    else if($god['contributions'] < $gods[$lowest]['contributions'])
      $lowest = $id;
  }

  foreach($god_list as $i=>$id)
  {
    if($id != $highest && $id != $lowest)
    {
      $middle = $id;
      break;
    }
  }

  $total = 0;

  foreach($gods as $id=>$god)
    $total += $god['currentvalue'];

  if($total < 1) $total = 1;

  foreach($gods as $id=>$god)
  {
    $percent = (int)($god['currentvalue'] * 100 / $total);
    $oldvalue = round($gods[$id]['attitude'] / 30);
    $god_user = get_user_byuser($id);

    // adjust attitude
    // ===============
    if($percent <= 5)
      $gods[$id]['attitude'] -= 7;

    else if($percent <= 10)
      $gods[$id]['attitude'] -= 5;

    else if($percent <= 15)
      $gods[$id]['attitude'] -= 3;

    else if($percent <= 20)
      $gods[$id]['attitude'] -= 2;

    else if($percent <= 25)
      $gods[$id]['attitude'] += 0;

    else if($percent <= 30)
      $gods[$id]['attitude'] += 2;

    else if($percent <= 35)
      $gods[$id]['attitude'] += 3;

    else if($percent <= 40)
      $gods[$id]['attitude'] += 5;

    else if($percent <= 45)
      $gods[$id]['attitude'] += 7;

    else if($percent <= 50)
      $gods[$id]['attitude'] += 11;

    else if($percent <= 55)
      $gods[$id]['attitude'] += 13;

    else if($percent <= 60)
      $gods[$id]['attitude'] += 17;

    else if($percent <= 65)
      $gods[$id]['attitude'] += 19;

    else if($percent <= 70)
      $gods[$id]['attitude'] += 23;

    else if($percent <= 75)
      $gods[$id]['attitude'] += 29;

    else if($percent <= 80)
      $gods[$id]['attitude'] += 31;

    else if($percent <= 85)
      $gods[$id]['attitude'] += 37;

    else if($percent <= 90)
      $gods[$id]['attitude'] += 41;

    else if($percent <= 90)
      $gods[$id]['attitude'] += 43;

    else if($percent <= 100)
      $gods[$id]['attitude'] += 47;

    // ===============

    $newvalue = round($gods[$id]['attitude'] / 30);

    // event posts
    if($id == 'gijubi')
    {
      if(date('M d') == 'Feb 15')
      {
        news_post($god_user['idnum'], 'routine', 'Today: Feast!', '{15}St. Valentine\'s or Lupercalia.  Names are meaningless if the meanings behind them have been forgotten.  Do not forget the gifts of nature.  Feast, and make new life.{/}');
        echo '* Gizubi posted about Lupercalia.', "\r\n";
      }
    }

    // keep within [-104, 104] --- round(# / 30) lies within [-3, 3]
    // ===========================
    if($gods[$id]['attitude'] < -104)
      $gods[$id]['attitude'] = -104;

    else if($gods[$id]['attitude'] > 104)
    {
      if($id == 'kirikashu')
      {
        $possible_items = array('Inspiration Draught', 'Tooth of Ramoth');
        $itemname = $possible_items[array_rand($possible_items)];
        $fancy_desc = "Kaera Ki Ri Kashu in Her wisdom gives The Young Ones a $itemname.";
      }
      else if($id == "rigzivizgi")
      {
        $possible_items = array("Death's Elixir", 'Deck of Many Things', "Proselytism's Broth");
        $itemname = $possible_items[array_rand($possible_items)];
        $fancy_desc = "Rizi Vizi Kaera Ki Ri Kashu will make an exception for The Sojourners.";
      }
      else if($id == "gijubi")
      {
        $possible_items = array('Ring of Regeneration +1/3', 'Wand of Wonder', 'Fertility Draught');
        $itemname = $possible_items[array_rand($possible_items)];
        $fancy_desc = "Gijubi-daera Ki Ri Kashu shares his {$itemname}s with The Tenants of This Land.";
      }

      $gods[$id]['attitude'] = 104;

      if(strlen($itemname) > 0)
      {
        $this_god = get_user_byuser($id);
        if($this_god['idnum'] > 0)
        {
          $itemid = add_inventory('psypets', $id, $itemname, $comment, 'storage');

          if($itemid !== false)
          {
            $command = "INSERT INTO monster_auctions (`ownerid`, `itemid`, `itemname`, `ldesc`, `bidvalue`, `bidtime`) " .
                       "VALUES ('" . $this_god["idnum"] . "', '$itemid', " . quote_smart($itemname) .  ", " . quote_smart('<i class="size15">' . $fancy_desc . '</i>') . ", '0', '" . (time() + (8 * 60 * 60)) . "');";
            $database->FetchNone($command, 'hosting divine auction');

            $gods[$id]['attitude'] = 0;

            echo '* ', $fancy_desc, ' (auctioning ', $itemname, ')', "\r\n";
          }
        }
      }
    }
    // ===========================
  }

  // for each god, subtract out the currentvalue we considered, and update attitude
  foreach($gods as $id=>$god)
  {
    $command = 'UPDATE monster_gods SET currentvalue=currentvalue-' . $god['currentvalue'] . ',attitude=' . $god['attitude'] . ' WHERE id=' . quote_smart($id) . ' LIMIT 1';
    $database->FetchNone($command, 'updating god\'s mood');
  }

  $data = $database->FetchSingle('SELECT * FROM psypets_slots LIMIT 1');
  $value = $data['money'];

  if($value >= 1000)
  {
    require_once 'commons/petlib.php';
    $added_pets = 0;
  
    while($value >= 1000)
    {
      $added_pets++;
      $value -= 1000;
      create_random_pet('psypets');
    }

    echo '* Added ' . $added_pets . ' pets from gambling money (1000m/pet).' . "\r\n";
    
    $command = 'UPDATE psypets_slots SET money=money-' . ($added_pets * 1000) . ' LIMIT 1';
    $database->FetchNone($command, 'updating slot money');
  }

  echo 'Finished considering gods.';
?>
