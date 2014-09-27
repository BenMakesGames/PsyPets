<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/adventurelib.php';
require_once 'commons/grammar.php';

require_once 'libraries/extra_functions.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$adventure = get_adventure($user['idnum']);

$adventuring_pet_ids = array();

$msgs = array();

foreach($userpets as $index=>$pet)
{
  if(!array_key_exists('love' . $pet['idnum'], $_POST))
    continue;

  if($now - $pet['last_love'] >= 30 * 60 || $pet['last_love_by'] != $user['idnum'])
  {
    $loveaction = $_POST['love' . $pet['idnum']];

    // do nothing
    if($loveaction == 0)
    {
      if($pet['sleeping'] == 'no')
      {
        $command = 'UPDATE monster_pets SET last_love_action=0 WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'update last love action');
      }

      continue;
    }

    // pet pet
    else if($loveaction == -1)
    {
      $safety_gain = gain_safety($userpets[$index], 1, true);
      $love_gain = gain_love($userpets[$index], 1, true);
      $esteem_gain = gain_esteem($userpets[$index], 1, true);

      $userpets[$index]['last_love'] = $now;
      $userpets[$index]['love_exp']++;
      $userpets[$index]['last_love_by'] = $user['idnum'];

      save_pet($userpets[$index], array('love', 'safety', 'esteem', 'last_love', 'love_exp', 'last_love_by'));

      add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'You pet ' . $pet['petname'] . '.', array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

      $love_actions++;
      $love_petname = $userpets[$index]['petname'];
      
      $command = 'UPDATE monster_pets SET last_love_action=-1 WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'update last love action');
    }

    // pet pet to bed
    else if($loveaction == -2)
    {
      $success_odds = 110 - (($pet['energy'] * 100) / max_energy($pet)) - $userpets[$index]['independent'] * 4;

      $userpets[$index]['last_love'] = $now;
      $userpets[$index]['love_exp']++;
      $userpets[$index]['last_love_by'] = $user['idnum'];

      if(rand(1, 100) < $success_odds)
      {
        $safety_gain = gain_safety($userpets[$index], 1, true);
        $love_gain = gain_love($userpets[$index], 1, true);
        $esteem_gain = gain_esteem($userpets[$index], 1, true);

        $userpets[$index]['sleeping'] = 'yes';

        $msgs[] = '68:' . $pet['petname'];

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'You put ' . $pet['petname'] . ' to bed.', array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));
      }
      else
      {
        $safety_gain = $love_gain = $esteem_gain = 0;

        $msgs[] = '67:' . $pet['petname'];

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'You attempted to put ' . $pet['petname'] . ' to bed, who stubbornly refused.');
      }

      save_pet($userpets[$index], array('love', 'safety', 'esteem', 'sleeping', 'last_love', 'love_exp', 'last_love_by'));
    }

    // wake up pet
    else if($loveaction == -3)
    {
      $userpets[$index]['last_love'] = $now;
      $userpets[$index]['last_love_by'] = $user['idnum'];

      if($pet['energy'] > 0 && $pet['sleeping'] == 'yes' && mt_rand(1, 100) <= 60 + $userpets[$index]['energy'] * 2 - $userpets[$index]['independent'] * 3)
      {
        $userpets[$index]['sleeping'] = 'no';

        $msgs[] = '65:' . $pet['petname'];

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'You woke ' . $pet['petname'] . ' up.', array());
      }
      else
      {
        $msgs[] = '66:' . $pet['petname'];

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'You tried to wake ' . $pet['petname'] . ' up, but ' . pronoun($pet['gender']) . ' wouldn\'t budge.', array());
      }

      save_pet($userpets[$index], array('sleeping', 'last_love', 'last_love_by'));
    }

    // drink from refreshing spring
    else if($loveaction == -4)
    {
      if(addon_exists($house, 'Refreshing Spring'))
      {
        $safety_gain = gain_safety($userpets[$index], 2, true);
        $love_gain = gain_love($userpets[$index], 2, true);
        $esteem_gain = gain_esteem($userpets[$index], 2, true);

        $userpets[$index]['last_love'] = $now;
        $userpets[$index]['caffeinated'] = 0;
        $userpets[$index]['love_exp']++;
        $userpets[$index]['last_love_by'] = $user['idnum'];

        save_pet($userpets[$index], array('love', 'safety', 'esteem', 'caffeinated', 'last_love', 'love_exp', 'last_love_by'));

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, $pet['petname'] . ' drank from a Refreshing Spring.', array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

        $love_actions++;
        $love_petname = $userpets[$index]['petname'];

        $command = 'UPDATE monster_pets SET last_love_action=-4 WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'update last love action');
      }
    }
    
    // play in the lake
    else if($loveaction == -5)
    {
      if(addon_exists($house, 'Lake'))
      {
        require_once 'commons/lakelib.php';
        
        list($safety, $love, $esteem) = lake_play_value($user['idnum']);

        $safety_gain = gain_safety($userpets[$index], $safety, true);
        $love_gain = gain_love($userpets[$index], $love, true);
        $esteem_gain = gain_esteem($userpets[$index], $esteem, true);

        $userpets[$index]['last_love'] = $now;
        $userpets[$index]['love_exp']++;
        $userpets[$index]['last_love_by'] = $user['idnum'];

        save_pet($userpets[$index], array('love', 'safety', 'esteem', 'last_love', 'love_exp', 'last_love_by'));

        add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, $pet['petname'] . ' played in a Lake.', array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

        $love_actions++;
        $love_petname = $userpets[$index]['petname'];

        $command = 'UPDATE monster_pets SET last_love_action=-5 WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'update last love action');
      }
    }

    // Macro Polo (requires lake or swimming pool)
    else if($loveaction == -6)
    {
      if(addon_exists($house, 'Lake') || addon_exists($house, 'Indoor Swimming Pool'))
      {
      
      }
    }
    
    // play blind man's buff
    else if($loveaction == -6)
    {
    }

    // play hide and seek
    else if($loveaction == -7)
    {
    }

    // wrestle
    else if($loveaction == -8)
    {
    }

    // adventure!
    else if($loveaction == -1000)
    {
      if($adventure !== false && $adventure['progress'] < $adventure['difficulty'])
      {
        $stats = explode(',', $adventure['stats']);
        
        foreach($stats as $stat)
        {
          $dice += $userpets[$index][$stat];
        }
        
        $safety_gain = gain_safety($userpets[$index], 1, true);
        $love_gain = gain_love($userpets[$index], 1, true);

        $save_stats = array('love', 'safety', 'last_love');
        
        if($adventure['level'] <= $dice + 1)
        {
          $success = mt_rand(ceil($dice / 2), $dice);
          
          adventure_progress($adventure, $success);
          
          foreach($stats as $stat)
          {
            train_pet($userpets[$index], $stat, 4, 0, false);

            $esteem_gain = gain_esteem($userpets[$index], 1, true);
            $save_stats[] = 'esteem';
              
            $userpets[$index]['actions_since_last_level']++;

            $save_stats[] = $stat;
            $save_stats[] = $stat . '_count';
            $save_stats[] = 'actions_since_last_level';
          }
        
          $userpets[$index]['last_love'] = $now;
          $userpets[$index]['love_exp']++;
          $save_stats[] = 'love_exp';
          
          $adventuring_pets[] = $userpets[$index]['petname'];
        }
        else
        {
          adventure_progress($adventure, 1);
          $msgs[] = '157:' . $userpets[$index]['petname'];
          $userpets[$index]['last_love'] = $now;
        }
        
        $adventuring_pet_ids[] = $userpets[$index]['idnum'];

        $userpets[$index]['last_love_by'] = $user['idnum'];
        $save_stats[] = 'last_love_by';
/*
        $userpets[$index]['last_love_action'] = -1000;
        $save_stats[] = 'last_love_action';
*/
        save_pet($userpets[$index], $save_stats);
      }
      else if($did_adventure_message !== true)
      {
        $msgs[] = '156:' . $userpets[$index]['petname'];
        $did_adventure_message = true;
      }
    }

    // use an item ($loveaction refers to that item's idnum)
    else
    {
      $this_item = get_inventory_byid((int)$loveaction);
      if($this_item['user'] == $user['user'])
      {
        $game_item = get_item_byname($this_item['itemname']);

        if(strlen($game_item['playdesc']) > 0)
        {
          $gain_stats = true;

          $save_stats = array('last_love', 'love_exp', 'last_love_by');

          if($game_item['playbed'] == 'yes')
          {
            $success_odds = 110 - (($pet['energy'] * 100) / max_energy($pet)) - $userpets[$index]['independent'] * 4;

            $userpets[$index]['last_love'] = $now;

            if(rand(1, 100) < $success_odds)
            {
              $userpets[$index]['sleeping'] = 'yes';

              $msgs[] = '68:' . $pet['petname'];

              $log_message = 'You put ' . $pet['petname'] . ' to bed.';
              
              $save_stats[] = 'sleeping';
            }
            else
            {
              $gain_stats = false;

              $msgs[] = '67:' . $pet['petname'];

              $log_message = 'You attempted to put ' . $pet['petname'] . ' to bed, who stubbornly refused.';
            }
          }
          else
          {
            $log_message = 'You ' . $game_item['playdesc'] . ' with ' . $pet['petname'] . '.';
            $love_actions++;
            $love_petname = $userpets[$index]['petname'];

            $database->FetchNone('UPDATE monster_pets SET last_love_action=' . (int)$loveaction . ' WHERE idnum=' . $pet['idnum'] . ' LIMIT 1');
          }

          $userpets[$index]['last_love'] = $now;
          $userpets[$index]['love_exp']++;
          $userpets[$index]['last_love_by'] = $user['idnum'];

          if($gain_stats)
          {
            if($game_item['playsafety'] >= 0)
              $safety_gain = gain_safety($userpets[$index], $game_item['playsafety'] + 1, true);
            else
            {
              lose_stat($userpets[$index], 'safety', -$game_item['playsafety']);
              $safety_gain = $game_item['playsafety'];
            }

            if($game_item['playlove'] >= 0)
              $love_gain = gain_love($userpets[$index], $game_item['playlove'] + 1);
            else
            {
              lose_stat($userpets[$index], 'love', -$game_item['playlove']);
              $love_gain = $game_item['playlove'];
            }

            if($game_item['playesteem'] >= 0)
              $esteem_gain = gain_esteem($userpets[$index], $game_item['playesteem'] + 1);
            else
            {
              lose_stat($userpets[$index], 'esteem', -$game_item['playesteem']);
              $esteem_gain = $game_item['playesteem'];
            }
            
            if($game_item['playstat'] != '')
            {
              $stat_desc = array(
                'str' => 'Strength',
                'dex' => 'Quickness',
                'sta' => 'Toughness',
                'per' => 'Perception',
                'int' => 'Intelligence',
                'wit' => 'Wits',
                'bra' => 'Brawl',
                'athletics' => 'Athletics',
                'stealth' => 'Stealth',
                'sur' => 'Survival',
                'gathering' => 'Nature',
                'fishing' => 'Fishing',
                'mining' => 'Mining',
                'cra' => 'Handicrafts',
                'painting' => 'Painting',
                'carpentry' => 'Carpentry',
                'jeweling' => 'Jeweling',
                'sculpting' => 'Sculpting',
                'eng' => 'Electronics',
                'mechanics' => 'Mechanics',
                'chemistry' => 'Chemistry',
                'smi' => 'Smithing',
                'tai' => 'Tailory',
                'leather' => 'Leather-working',
                'binding' => 'Magic-binding',
                'pil' => 'Piloting',
                'astronomy' => 'Astronomy',
                'music' => 'Music',
              );  

              require_once 'commons/statlib.php';
              
              $badge = $game_item['playstat'] . '_trainer';

              train_pet($userpets[$index], $game_item['playstat'], 4, 0, false, true);
              $got_badge = record_stat_with_badge($user['idnum'], 'Trained a Pet in ' . $stat_desc[$game_item['playstat']], 1, 50, $badge);
              
              if($got_badge)
              {
                require_once 'commons/badges.php';
                $msgs[] = '90:' . $BADGE_DESC[$badge];
              }
              
              $userpets[$index]['actions_since_last_level']++;

              $save_stats[] = $game_item['playstat'] . '_count';
              $save_stats[] = 'actions_since_last_level';
            }
          }
          else
          {
            $safety_gain = 0;
            $love_gain = 0;
            $esteem_gain = 0;
          }

          if($safety_gain != 0) $save_stats[] = 'safety';
          if($love_gain != 0)   $save_stats[] = 'love';
          if($esteem_gain != 0) $save_stats[] = 'esteem';

          save_pet($userpets[$index], $save_stats);

          add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, $log_message, array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

          // rolling a Katamari restores durability!
          if($game_item['itemtype'] == 'divine/katamari')
          {
            if($this_item['health'] < $game_item['durability'])
            {
              $increase = min($game_item['durability'] - $this_item['health'], rand(5, 15));

              $this_item['health'] += $increase;

              $database->FetchNone("UPDATE monster_inventory SET health=health+$increase WHERE idnum=" . $this_item['idnum'] . " LIMIT 1");
            }

            $get_katamari_badge = true;
          }
        }
        else
          $msgs[] = 27;
      }
    }
  }
  else
    $msgs[] = 29;
}

if($adventure !== false)
{
  save_adventure_progress($adventure);
  
  if(count($adventuring_pet_ids) > 1)
  {
    require_once 'commons/relationshiplib.php';
  
    $adventuring_pet_ids2 = $adventuring_pet_ids;
  
    foreach($adventuring_pet_ids as $i=>$id1)
    {
      unset($adventuring_pet_ids2[$i]);
      
      foreach($adventuring_pet_ids2 as $id2)
      {
        // if idnums are identical, don't hang out :P  also, 66% of matches don't hang out
        if($id1 == $id2 || mt_rand(1, 3) > 1) continue;
        
        $this_pet = get_pet_byid($id1);
        $other_pet = get_pet_byid($id2);

        do_adventuring_hang_out($this_pet, $other_pet);
      }
    }
  }
}

if($get_katamari_badge === true)
{
  $badges = get_badges_byuserid($user['idnum']);
  if($badges['royal'] == 'no')
  {
    set_badge($user['idnum'], 'royal');
    $msgs[] = 95;
  }
}

if($love_actions > 1)
  $msgs[] = '117:' . $love_actions;
else if($love_actions == 1)
  $msgs[] = '116:' . $love_petname;

if(count($adventuring_pets) > 0)
  $msgs[] = '158:' . str_replace(',', '&#44;', list_nice($adventuring_pets));

if($_POST['ajax'])
{
  require_once 'commons/messages.php';
  echo form_message($msgs);
}
else
  header('Location: /myhouse.php?msg=' . link_safe(implode(',', $msgs)));
?>
