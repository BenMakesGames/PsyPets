<?php
require_once 'commons/userlib.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/itemlib.php';
require_once 'commons/petlib.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grammar.php';
require_once 'commons/dungeonlib.php';
require_once 'commons/zoolib.php';
require_once 'commons/maproomlib.php';
require_once 'commons/fireplacelib.php';
require_once 'commons/economylib.php';
require_once 'commons/houseresources.php';
require_once 'commons/farmlib.php';
require_once 'commons/aquariumlib.php';
require_once 'commons/gameroomlib.php';
require_once 'commons/flavorlib.php';
require_once 'commons/relationshiplib.php';
require_once 'commons/petstatlib.php';
require_once 'commons/dreamlib.php';

require_once 'libraries/db_messages.php';

$MORE_TO_UPDATE = false;
$AWARD_BADGE = array();

function check_pets($userid, $hour_limit = false)
{
    global $TIME_IS_FUCKED, $LAST_NEW_PET_NAME;

    if($TIME_IS_FUCKED === true)
        return '<i>(Hourly pet actions have been disabled.  Refer to the City Hall for more information.)</i>';

    global $simulated_time, $MATERIALS_LIST, $now, $MORE_TO_UPDATE, $AWARD_BADGE, $FREE_STORAGE_DAYS;

    $myuser = get_user_byid($userid);
    $house = get_house_byuser($userid);
    $badges = get_badges_byuserid($userid);

    if($myuser === false)
    {
        echo 'user does not exist?  (encountered this problem while trying to check pets.)';
        exit();
    }

    $money_gained = 0;

    // get god info
    $gods = $GLOBALS['database']->FetchMultipleBy('SELECT * FROM monster_gods', 'id');

    $money_spent = 0;
    $money_gained = 0;
    $money_donated = 0;
    $savings_diverted = 0;

    $hours = floor(($now - $house['lasthour']) / (60 * 60));

    $pets_checked = false;

    if($hours > 50)
    {
        $extra_hours = $hours - 50;
        $house['lasthour'] += $extra_hours * 60 * 60;

        $GLOBALS['database']->FetchNone('
			UPDATE monster_houses
			SET lasthour=lasthour+' . ($extra_hours * 60 * 60) . '
			WHERE idnum=' . $house['idnum'] . '
			LIMIT 1
		');

        $hours = 50;
    }
    else if($hours == 0)
        return '';

    if($hour_limit !== false && $hours > $hour_limit)
        $hours = $hour_limit;

    $max_pets = max_active_pets($myuser, $house);

    // load pets and projects
    $mypets = get_user_pets_for_simulation($myuser['user'], $max_pets);
    $myprojects = get_projects_byloc($userid);
    $petbulk = 0;

    $num_pets = count($mypets);
    $live_pets = $num_pets;

    $has_pets = (count($num_pets) > 0);

    if($has_pets)
    {
        // check for relevant House add-ons
        $addons = take_apart(',', $house['addons']);

        $online = in_array('Fiberoptic Link', $addons);
        $moat = in_array('Moat', $addons);
        $pool = in_array('Indoor Swimming Pool', $addons);

        $dungeon = get_dungeon_byuser($userid);
        if($dungeon === false)
            $monster_list = array();
        else
            $monster_list = take_apart(',', $dungeon['monsters']);

        $zoo = get_zoo_byuser($userid);
        if($zoo === false)
            $prey_list = array();
        else
            $prey_list = take_apart(',', $zoo['monsters']);

        $maproom = get_maproom_byuser($userid);
        if($maproom === false)
            $location_list = array();
        else
            $location_list = take_apart(',', $maproom['locations']);

        if(in_array('Fireplace', $addons))
        {
            $fireplace = get_fireplace_byuser($userid);
            $firepower = $fireplace['fire'] - 4;
        }
        else
        {
            $fireplace = false;
            $firepower = 0;
        }

        if(in_array('Farm', $addons))
            $farm = get_farm_if_exists($userid);
        else
            $farm = false;

        if(in_array('Aquarium', $addons))
            $aquarium = get_aquarium_if_exists($userid);
        else
            $aquarium = false;

        if(in_array('Game Room', $addons))
        {
            $game_room = get_game_room($userid);
            $game_room_games = get_game_room_games($userid);
        }
        else
        {
            $game_room = false;
            $game_room_games = array();
        }

        $old_list_size = count($monster_list);
        $old_zoo_size = count($prey_list);
        $old_location_size = count($location_list);

        $pet_friendliness = 0;
        $pet_protection = 0;

        // loop through each pet!
        foreach($mypets as $idnum=>$pet)
        {
            $petbulk += pet_size($pet);

            if($mypets[$idnum]['toolid'] > 0 && $pet['eggplant'] == 'no')
            {
                $tool = get_inventory_byid($mypets[$idnum]['toolid']);
                $tool_item = get_item_byname($tool['itemname']);

                $mypets[$idnum]['tool'] = $tool_item;
                $mypets[$idnum]['realtool'] = $tool;

                $mypets[$idnum]['AVALANCHE'] = ($tool['itemname'] == 'Unreasonably Large Sword');
                $mypets[$idnum]['Toadstool'] = ($tool['itemname'] == 'Mushroom Cap');
                $mypets[$idnum]['skill_golden_mushroom'] = ($tool_item['equip_goldenmushroom'] == 'yes' ? 1 : 0);
                $mypets[$idnum]['heal_quickly'] = ($tool_item['equip_healing'] == 'yes');
            }

            if($mypets[$idnum]['keyid'] > 0 && $pet['eggplant'] == 'no')
            {
                $key = get_inventory_byid($mypets[$idnum]['keyid']);
                $key_item = get_item_byname($key['itemname']);

                $mypets[$idnum]['key'] = $key_item;
            }

            $mypets[$idnum]['skill_extraverted']   = min(10, max(0, $mypets[$idnum]['extraverted']   + successes($tool_item['equip_extraverted'])));
            $mypets[$idnum]['skill_open']          = min(10, max(0, $mypets[$idnum]['open']          + successes($tool_item['equip_open'])));
            $mypets[$idnum]['skill_conscientious'] = min(10, max(0, $mypets[$idnum]['conscientious'] + successes($tool_item['equip_conscientious'])));
            $mypets[$idnum]['skill_playful']       = min(10, max(0, $mypets[$idnum]['playful']       + successes($tool_item['equip_playful'])));
            $mypets[$idnum]['skill_independent']   = min(10, max(0, $mypets[$idnum]['independent']   + successes($tool_item['equip_independent'])));

            $mypets[$idnum]['skill_str']           = max(0, $mypets[$idnum]['str']           + successes($tool_item['equip_str']));
            $mypets[$idnum]['skill_dex']           = max(0, $mypets[$idnum]['dex']           + successes($tool_item['equip_dex']));
            $mypets[$idnum]['skill_sta']           = max(0, $mypets[$idnum]['sta']           + successes($tool_item['equip_sta']));
            $mypets[$idnum]['skill_per']           = max(0, $mypets[$idnum]['per']           + successes($tool_item['equip_per']));
            $mypets[$idnum]['skill_int']           = max(0, $mypets[$idnum]['int']           + successes($tool_item['equip_int']));
            $mypets[$idnum]['skill_wit']           = max(0, $mypets[$idnum]['wit']           + successes($tool_item['equip_wit']));

            $mypets[$idnum]['skill_athletics']     = max(0, $mypets[$idnum]['athletics']     + successes($tool_item['equip_athletics']));
            $mypets[$idnum]['skill_stealth']       = max(0, $mypets[$idnum]['stealth']       + successes($tool_item['equip_stealth'])        + round($gods['rigzivizgi']['attitude'] / 30));

            $mypets[$idnum]['skill_mechanics']     = max(0, $mypets[$idnum]['mechanics']     + successes($tool_item['equip_mechanics'])      + round($gods['kirikashu']['attitude'] / 30));
            $mypets[$idnum]['skill_electronics']   = max(0, $mypets[$idnum]['eng']           + successes($tool_item['equip_electronics'])    + round($gods['kirikashu']['attitude'] / 30));
            $mypets[$idnum]['skill_hunting']       = max(0, $mypets[$idnum]['sur']           + successes($tool_item['equip_hunting']));
            $mypets[$idnum]['skill_gathering']     = max(0, $mypets[$idnum]['gathering']     + successes($tool_item['equip_gathering'])      + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_smithing']      = max(0, $mypets[$idnum]['smi']           + successes($tool_item['equip_smithing']));
            $mypets[$idnum]['skill_tailoring']     = max(0, $mypets[$idnum]['tai']           + successes($tool_item['equip_tailoring']));
            $mypets[$idnum]['skill_leatherworking']= max(0, $mypets[$idnum]['leather']       + successes($tool_item['equip_leather']));
            $mypets[$idnum]['skill_adventuring']   = max(0, $mypets[$idnum]['bra']           + successes($tool_item['equip_adventuring'])    + round($gods['rigzivizgi']['attitude'] / 30));
            $mypets[$idnum]['skill_crafting']      = max(0, $mypets[$idnum]['cra']           + successes($tool_item['equip_crafting'])       + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_painting']      = max(0, $mypets[$idnum]['painting']      + successes($tool_item['equip_painting'])       + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_carpentry']     = max(0, $mypets[$idnum]['carpentry']     + successes($tool_item['equip_carpentry'])      + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_sculpting']     = max(0, $mypets[$idnum]['sculpting']     + successes($tool_item['equip_sculpting'])      + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_jeweling']      = max(0, $mypets[$idnum]['jeweling']      + successes($tool_item['equip_jeweling'])       + round($gods['kirikashu']['attitude'] / 30));
            $mypets[$idnum]['skill_mining']        = max(0, $mypets[$idnum]['mining']        + successes($tool_item['equip_mining']));
            $mypets[$idnum]['skill_lumberjacking'] = max(0, $mypets[$idnum]['gathering']     + successes($tool_item['equip_lumberjacking']));
            $mypets[$idnum]['skill_fishing']       = max(0, $mypets[$idnum]['fishing']       + successes($tool_item['equip_fishing'])        + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_binding']       = max(0, $mypets[$idnum]['binding']       + successes($tool_item['equip_binding'])        + round(($gods['gijubi']['attitude'] + $gods['kirikashu']['attitude'] + $gods['rigzivizgi']['attitude']) / 60));
            $mypets[$idnum]['skill_chemistry']     = max(0, $mypets[$idnum]['chemistry']     + successes($tool_item['equip_chemistry'])      + round($gods['kirikashu']['attitude'] / 30));
            $mypets[$idnum]['skill_gardening']     = max(0, $mypets[$idnum]['gathering']     + successes($tool_item['equip_gardening'])      + round($gods['gijubi']['attitude'] / 30));
            $mypets[$idnum]['skill_music']         = max(0, $mypets[$idnum]['music']         + successes($tool_item['equip_music']));
            $mypets[$idnum]['skill_astronomy']     = max(0, $mypets[$idnum]['astronomy']     + successes($tool_item['equip_astronomy']));

            $mypets[$idnum]['pregnancy_increase']  = max(0, successes($tool_item['equip_fertility']));
            $mypets[$idnum]['dream_rate'] = ($tool_item['equip_more_dreams'] == 'yes' ? 3 : 5);

            if($pet['zombie'] == 'yes')
            {
                if(is_new_moon())
                {
                    $mypets[$idnum]['skill_adventuring'] += 2;
                    $mypets[$idnum]['skill_hunting'] += 2;
                }
                else if(is_full_moon())
                {
                    $mypets[$idnum]['skill_adventuring'] -= 2;
                    $mypets[$idnum]['skill_hunting'] -= 2;
                }
            }
        }

        $house_stats = get_housestats_byloc($myuser);

        $MATERIALS_LIST = &$house_stats['materials'];

        $house['curbulk'] = $house_stats['bulk'] + $petbulk;

        update_house_bulk($house['idnum'], $house['curbulk']);

        $house_size = $house['curbulk'];
        $max_size = $house['maxbulk'];
    }

    $cur_hours = 0;
    $start_time = $house['lasthour'];
    $simulated_time = $start_time;
    $update_duration = 0;
    $max_update_duration = 0;
    $break_early = false;

    for($i = 0; $i < $hours && $live_pets > 0; ++$i)
    {
        if(time() - $now + $max_update_duration >= 25)
        {
            $break_early = true;
            break;
        }

        $update_duration_start = time();

        if(mt_rand(1, 720 + $gods['rigzivizgi']['attitude']) == 1)
            $house['rats'] = 'yes';

        $GLOBALS['database']->FetchNone('
			UPDATE monster_houses
			SET
				lasthour=lasthour+3600,
				hoursearned=hoursearned+1
			WHERE idnum=' . $house['idnum'] . '
			LIMIT 1
		');

        $house['hoursearned']++;
        $house['lasthour'] += 3600;

        $simulated_time += (60 * 60);

        if($has_pets)
        {
            $live_pets = $num_pets;
            $full_moon = is_full_moon();

            foreach($mypets as $idnum=>$pet)
            {
                if($pet['dead'] != 'no')
                {
                    $live_pets--;
                    continue;
                }

                $pet_states = take_apart(',', $pet['state']);
                $do_hour = true;

                if($pet['lycanthrope'] == 'yes' && $pet['eggplant'] == 'no')
                {
                    if($full_moon && $pet['changed'] == 'no')
                    {
                        add_logged_event_cached($userid, $mypets[$idnum]['idnum'], $i + 1, 'hourly', 'lycanthrope', $pet['petname'] . ' looks upward and screams as ' . pronoun($pet['gender']) . ' changes form');
                        $do_hour = false;
                        $mypets[$idnum]['changed'] = 'yes';
                        $mypets[$idnum]['sleeping'] = 'no';
                    }
                    else if(!$full_moon && $pet['changed'] == 'yes')
                    {
                        add_logged_event_cached($userid, $mypets[$idnum]['idnum'], $i + 1, 'hourly', 'lycanthrope', $pet['petname'] . ' looks upward and screams, returning to ' . p_pronoun($pet['gender']) . ' normal form');
                        $do_hour = false;
                        $mypets[$idnum]['changed'] = 'no';
                        $mypets[$idnum]['sleeping'] = 'no';
                    }
                } // lycantrhope

                if($pet['gender'] == 'female' && $pet['prolific'] == 'yes' && $pet['zombie'] == 'no')
                {
                    if($mypets[$idnum]['pregnant_asof'] >= 30 * 24 + mt_rand(-12, 24))
                    {
                        if($mypets[$idnum]['pregnant_by'] == '')
                        {
                            $fatherid = 0;
                            $bloodtype2 = random_blood_type();
                            $possible_graphics = matching_graphics($pet['graphic']);
                        }
                        else
                        {
                            list($fatherid, $bloodtype2, $graphic2) = explode(',', $mypets[$idnum]['pregnant_by']);
                            $possible_graphics = matching_graphics2($pet['graphic'], $graphic2);

                            if($fatherid == 0)
                            {
                                $bloodtype2 = random_blood_type();
                                $possible_graphics = matching_graphics($pet['graphic']);
                            }
                            // if we have the father id, but nothing else, look up the father NOW
                            else if($bloodtype2 == '' || $graphic2 == '')
                            {
                                $father = get_pet_byid($fatherid);

                                if($father === false)
                                {
                                    $bloodtype2 = random_blood_type();
                                    $possible_graphics = matching_graphics($pet['graphic']);
                                }
                                else
                                {
                                    $bloodtype2 = $father['bloodtype'];
                                    $graphic2 = $father['graphic'];
                                }
                            }
                        }

                        $mypets[$idnum]['sleeping'] = 'no';
                        $mypets[$idnum]['pregnant_asof'] = 0;
                        $mypets[$idnum]['pregnant_by'] = '';

                        if($myuser['breeder'] == 'yes')
                            $num_babies = mt_rand(1, mt_rand(2, 5));
                        else
                            $num_babies = mt_rand(1, mt_rand(2, 3));

                        lose_stat($mypets[$idnum], 'energy', 4 * $num_babies);

                        $ids = array();

                        for($j = 0; $j < $num_babies; ++$j)
                        {
                            $ids[] = create_offspring($myuser['user'], $mypets[$idnum]['generation'] + 1, $possible_graphics, $pet['bloodtype'], $bloodtype2, true);
                            if(mt_rand(1, 101) == 1)
                                add_inventory_cached($myuser['user'], '', 'Silver Spoon', $LAST_NEW_PET_NAME . ' was born with this!', 'home');
                        }

                        $GLOBALS['database']->FetchNone('
							UPDATE monster_pets
							SET
								motherid=' . $idnum . ',
								fatherid=' . $fatherid . ',
								birthedtouser=' . $userid . '
							WHERE
								idnum ' . $GLOBALS['database']->In($ids) . '
							LIMIT ' . count($ids) . '
						');

                        set_pet_badge($mypets[$idnum], 'mother');

                        add_logged_event_cached($userid, $mypets[$idnum]['idnum'], $i + 1, 'hourly', 'birth', $mypets[$idnum]['petname'] . ' gave birth to ' . say_number($num_babies) . ($num_babies != 1 ? ' babies!' : ' baby!'), array('energy' => -8));
                        $do_hour = false;
                    }
                    else if($mypets[$idnum]['pregnant_asof'] > 0)
                        $mypets[$idnum]['pregnant_asof']++;
                } // prolific female

                if($mypets[$idnum]['park_event_hours'] < 24)
                    $mypets[$idnum]['park_event_hours']++;

                if($mypets[$idnum]['nasty_wound'] > 0 && $mypets[$idnum]['zombie'] != 'yes')
                    $mypets[$idnum]['nasty_wound']--;

                if($do_hour)
                {
                    if($pet['sleeping'] == 'yes' && !($pet['merit_sleep_walker'] == 'yes' && $pet['energy'] > 0 && mt_rand(1, 20) == 1))
                        hourly_pet_sleeping($mypets[$idnum], $myuser, $i + 1);
                    else
                        hourly_pet(
                            $mypets[$idnum],
                            $myprojects,
                            $myuser,
                            $i + 1,
                            $num_pets,
                            $house_size,
                            $max_size,
                            $house,
                            $monster_list,
                            $prey_list,
                            $location_list,
                            $game_room,
                            $game_room_games,
                            $farm,
                            ($aquarium['happy'] == 'yes'),
                            ($firepower > 0 && $firepower <= 20),
                            $online
                        );
                }
//            echo "done<br>\n";
            }

            if($live_pets == 0)
                $has_pets = false;
        }

        $firepower--;

        $cur_hours++;

        $update_duration = time() - $update_duration_start;
        if($update_duration > $max_update_duration)
            $max_update_duration = $update_duration;
    }

    if($cur_hours > 0)
    {
        foreach($mypets as $idnum=>$pet)
        {
            if($pet['dead_zombie'] == true) continue;

            save_pet($mypets[$idnum], array(
                'sleeping', 'asleep_time',
                'energy', 'food', 'safety', 'love', 'esteem', 'caffeinated', 'inspired',

                'ascend', 'ascend_adventurer', 'ascend_hunter', 'ascend_inventor',
                'ascend_artist', 'ascend_gatherer', 'ascend_smith', 'ascend_tailor', 'ascend_leather',
                'ascend_fisher', 'ascend_lumberjack', 'ascend_miner', 'ascend_carpenter',
                'ascend_jeweler', 'ascend_painter', 'ascend_sculptor', 'ascend_mechanic',
                'ascend_binder', 'ascend_chemist', 'ascend_vhagst',

                'toolid', 'keyid', 'costumed',

                'actions_since_last_level',
                'park_event_hours',
                'nasty_wound',

                'pregnant_asof', 'pregnant_by', 'dead', 'lycanthrope', 'changed', 'eggplant', 'sleepuntil',

                'str_count', 'dex_count', 'sta_count', 'per_count', 'int_count', 'wit_count', 'bra_count',
                'athletics_count', 'stealth_count', 'sur_count', 'gathering_count', 'fishing_count', 'mining_count',
                'cra_count', 'painting_count', 'carpentry_count', 'jeweling_count', 'sculpting_count',
                'eng_count', 'mechanics_count', 'chemistry_count', 'smi_count', 'tai_count', 'leather_count', 'binding_count',
                'pil_count',

                'str', 'dex', 'sta', 'per', 'int', 'wit', 'bra',
                'athletics', 'stealth', 'sur', 'gathering', 'fishing', 'mining',
                'cra', 'painting', 'carpentry', 'jeweling', 'sculpting',
                'eng', 'mechanics', 'chemistry', 'smi', 'tai', 'leather', 'binding',
                'pil',
            ));
        }

        save_house_status($house);

        if($dungeon !== false && count($monster_list) > $old_list_size)
        {
            $command = 'UPDATE psypets_dungeons SET monsters=' . quote_smart(implode(',', $monster_list)) . " WHERE userid=$userid LIMIT 1";
            $GLOBALS['database']->FetchNone($command, 'updating dungeon list');
        }

        if($zoo !== false && count($prey_list) > $old_zoo_size)
        {
            $command = 'UPDATE psypets_zoos SET monsters=' . quote_smart(implode(',', $prey_list)) . " WHERE userid=$userid LIMIT 1";
            $GLOBALS['database']->FetchNone($command, 'updating menagerie list');
        }

        if($maproom !== false && count($location_list) > $old_location_size)
        {
            $command = 'UPDATE psypets_maprooms SET locations=' . quote_smart(implode(',', $location_list)) . " WHERE userid=$userid LIMIT 1";
            $GLOBALS['database']->FetchNone($command, 'updating menagerie list');
        }

        if($fireplace['fire'] > 0)
        {
            $hours_burned = min($fireplace['fire'], $cur_hours);

            $new_hours = $fireplace['fireduration'] + $hours_burned;

            // fireplace rewards!
            if(floor($fireplace['fireduration'] / 120) < floor($new_hours / 120))
            {
                $items = array('The Fairy\'s Earrings', 'Firefae Staff');
                $item = $items[array_rand($items)];

                add_inventory_cached($myuser['user'], '', $item, 'This item mysteriously appeared in the house, covered in soot.', 'home');

                $event_time = $start_time + ($hours_burned - ($new_hours % 120)) * 60 * 60;

                log_fireplace_event($event_time, $myuser['idnum'], $item . ' was found on the hearth.');
            }
            else if(floor($fireplace['fireduration'] / 40) < floor($new_hours / 40))
            {
                $colors = array('Black', 'Yellow', 'Purple', 'Green', 'Red');
                $color = $colors[array_rand($colors)];

                add_inventory_cached($myuser['user'], '', $color . ' Stocking', 'This item mysteriously appeared in the house, covered in soot.', 'home');

                $event_time = $start_time + ($hours_burned - ($new_hours % 120)) * 60 * 60;

                add_db_message($userid, FLASH_MESSAGE_GENERAL_MESSAGE, 'A ' . $color . ' Stocking was found on the hearth.');
            }

            if($fireplace['fireduration'] >= 500 && $badges['fairyfriend'] == 'no')
            {
                set_badge($userid, 'fairyfriend');
                psymail_user($myuser['user'], 'thefae', 'Our thanks to you!', 'We have lived happily thanks to your continued efforts, Tall One.  We would give you the badge of the Fairies, as a token of our friendship.<br /><br />{i}(You received the Friend of the Firefae Badge.){/}');
                $badges['fairyfriend'] = 'yes';
            }
            else if($fireplace['fireduration'] >= 1000 && $badges['pyrophiliac'] == 'no')
            {
                set_badge($userid, 'pyrophiliac');
                psymail_user($myuser['user'], 'thefae', 'You have our respect and admiration!', 'Never before have we seen such devotion to flame in a Tall One as we see in you.  We bestow upon you this badge, a gift typically reserved for the Fire Elementals and their kin.<br /><br />{i}(You received the Pyrophiliac Badge.){/}');
                $badges['pyrophiliac'] = 'yes';
            }

            if($hours_burned == $fireplace['fire'])
            {
                $command = 'UPDATE psypets_fireplaces SET fire=0,fireduration=0 WHERE idnum=' . $fireplace['idnum'] . ' LIMIT 1';

                $event_time = $start_time + $hours_burned * 60 * 60;

                add_db_message($userid, FLASH_MESSAGE_GENERAL_MESSAGE, 'Your fireplace\'s fire has gone out.');
            }
            else
                $command = 'UPDATE psypets_fireplaces SET fire=fire-' . $hours_burned . ',fireduration=fireduration+' . $hours_burned . ' WHERE idnum=' . $fireplace['idnum'] . ' LIMIT 1';

            $GLOBALS['database']->FetchNone($command, 'updating fireplace hours');
        }

        recount_house_bulk($myuser, $house);
    }

    if($house['hoursearned'] >= 24)
    {
        $days_past = floor($house['hoursearned'] / 24);
        $house['hoursearned'] -= $days_past * 24;

        if($live_pets > 0)
        {
            $allowance_value = value_with_inflation(50);
            $donation_value = $allowance_value * 4;

            $allowance = 0;

            if($myuser['allowance'] == 'standard')
            {
                $allowance = $days_past * $allowance_value;
                $money_gained += $allowance;

                for($i = 0; $i < $days_past; $i++)
                {
                    add_inventory_cached($myuser['user'], '', '12-hour Food Box', 'daily food', 'storage/incoming');
                    add_inventory_cached($myuser['user'], '', 'Debris', 'daily resources', 'storage/incoming');
                }

                flag_new_incoming_items($myuser['user']);
            }
            else if($myuser['allowance'] == 'resources')
            {
                for($i = 0; $i < $days_past; $i++)
                {
                    add_inventory_cached($myuser['user'], '', 'Debris', 'daily resources', 'storage/incoming');
                    add_inventory_cached($myuser['user'], '', 'Debris', 'daily resources', 'storage/incoming');
                }

                flag_new_incoming_items($myuser['user']);
            }
            else if($myuser['allowance'] == 'food')
            {
                for($i = 0; $i < $days_past; $i++)
                {
                    add_inventory_cached($myuser['user'], '', '12-hour Food Box', 'daily food', 'storage/incoming');
                    add_inventory_cached($myuser['user'], '', '12-hour Food Box', 'daily food', 'storage/incoming');
                    add_inventory_cached($myuser['user'], '', '12-hour Food Box', 'daily food', 'storage/incoming');
                }

                flag_new_incoming_items($myuser['user']);
            }
            else if($myuser['allowance'] == 'rizivizi')
            {
                $donation = $days_past * $donation_value;
                $money_donated += $donation;
                $donateto = 'rigzivizgi';
                $name = 'Rizi Vizi';
            }
            else if($myuser['allowance'] == 'gizubi')
            {
                $donation = $days_past * $donation_value;
                $money_donated += $donation;
                $donateto = 'gijubi';
                $name = 'Gizubi-daera';
            }
            else if($myuser['allowance'] == 'kaera')
            {
                $donation = $days_past * $donation_value;
                $money_donated += $donation;
                $donateto = 'kirikashu';
                $name = 'Kaera Ki Ri Kashu';
            }

            if($donation > 0)
            {
                $GLOBALS['database']->FetchNone('
					UPDATE monster_gods
					SET
						contributions=contributions+' . $donation . ',
						currentvalue=currentvalue+' . $donation . '
					WHERE id=' . quote_smart($donateto) . '
					LIMIT 1
				');

                require_once 'commons/templelib.php';

                donate_to_temple($myuser, $donation);
            }

            if($allowance > 0)
            {
                give_money($myuser, $allowance, 'Daily allowance');
                $myuser['money'] += $allowance;
            }

            $new_savings = $myuser['savings'];

            for($i = 0; $i < $days_past; ++$i)
            {
                $interest = $new_savings * interest_rate();
                if($interest > $allowance_value)
                    $interest = $allowance_value;

                $new_savings += $interest;

                if($interest != floor($interest))
                    $report_detail = 'More precisely: ' . round($interest, 2) . '<span class="money">m</span>';
                else
                    $report_detail = '';

                add_transaction($myuser['user'], $now, 'Bank interest', $interest, $report_detail);
            }

            $command = "UPDATE monster_users SET savings='$new_savings',adoptedtoday='no' WHERE idnum=$userid LIMIT 1";
            $GLOBALS['database']->FetchNone($command, 'check_pets');
        } // if we have live petes

        $free_storage_space = 1000;

        $addons = take_apart(',', $house['addons']);
        if(array_search('Attic', $addons) !== false)
            $free_storage_space += 500;

        $storage_bulk = storage_bulk($myuser['user']);

        if($storage_bulk > $free_storage_space)
        {
            if(array_search('Colossus', $addons) !== false)
                $pay_for = 60;
            else
                $pay_for = 50;

            $storage_fee = $days_past * storage_fees($storage_bulk - $free_storage_space, $pay_for);

            if(array_search(date('F j'), $FREE_STORAGE_DAYS) !== false)
                $storage_fee = 0;

            if($storage_fee > $myuser['money'])
            {
                if($myuser['savings_pay_storage'] == 'no' || $storage_fee > $myuser['money'] + $myuser['savings'] + 1)
                {
                    add_db_message($userid, FLASH_MESSAGE_ALLOWANCE, 'You were not able to pay your Storage rent.  The items you had in Storage have been seized!  (Check your Storage for instructions on how to retreive them.)');
                    $command = 'UPDATE monster_inventory SET forsale=0,location=\'seized\',changed=' . $simulated_time . ' WHERE location LIKE \'storage%\' AND location!=\'storage/outgoing\' AND user=' . quote_smart($myuser['user']);
                    $GLOBALS['database']->FetchNone($command, 'checkpet.php/check_pets()');
                }
                else
                {
                    $cost_for_savings = $storage_fee - $myuser['money'] + 1;

                    $savings_diverted += $cost_for_savings;

                    $myuser['savings'] -= $cost_for_savings;

                    $command = "UPDATE monster_users SET savings='$new_savings' WHERE idnum=$userid LIMIT 1";
                    $GLOBALS['database']->FetchNone($command, 'deducting money from savings');

                    $money_spent += $storage_fee;

                    give_money($myuser, $cost_for_savings, 'Diverted from Savings');
                    take_money($myuser, 1, "Bank fee for diverting money from Savings");
                    take_money($myuser, $storage_fee, "Storage rent");

                    $myuser['money'] = 0;
                }
            }
            else
            {
                $money_spent += $storage_fee;

                $myuser['money'] -= $storage_fee;

                take_money($myuser, $storage_fee, "Storage rent");
            }
        }

        $command = 'UPDATE monster_houses SET hoursearned=hoursearned-' . ($days_past * 24) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
        $GLOBALS['database']->FetchNone($command, 'updating allowance time');

    } // if we earned a day or more

    process_cached_inventory();
    process_pet_log_cache();

    if($savings_diverted > 0)
        add_db_message($userid, FLASH_MESSAGE_ALLOWANCE, 'Diverted ' . $savings_diverted . '<span class="money">m</span> from Savings in order to pay fees.');

    if($money_spent > 0)
        add_db_message($userid, FLASH_MESSAGE_ALLOWANCE, 'Paid ' . $money_spent . '<span class="money">m</span> in Storage fees.');

    if($money_gained > 0)
        add_db_message($userid, FLASH_MESSAGE_ALLOWANCE, 'Collected ' . $money_gained . '<span class="money">m</span> from allowance.');

    if($money_donated > 0)
        add_db_message($userid, FLASH_MESSAGE_ALLOWANCE, 'A donation of ' . $money_donated . '<span class="money">m</span> was made to ' . $name . ' in your name.');

    if(count($AWARD_BADGE) > 0)
        $badges = get_badges_byuserid($userid);

    if($AWARD_BADGE['AVALANCHE'] === true)
    {
        if($badges['materia'] == 'no')
        {
            set_badge($userid, 'materia');
            add_db_message($userid, FLASH_MESSAGE_BADGE, 'You were awarded the AVALANCHE badge!');
        }
    }

    if($AWARD_BADGE['Toadstool'] === true)
    {
        if($badges['toadstool'] == 'no')
        {
            set_badge($userid, 'toadstool');
            add_db_message($userid, FLASH_MESSAGE_BADGE, 'You were awarded the Toadstool badge!');
        }
    }

    if($break_early)
    {
        if(strlen($_GET['goto']) == 0)
            $_GET['goto'] = urlencode($_SERVER['PHP_SELF']);

        header('Location: /updatepets.php?goto=' . $_GET['goto'] . '&informonly');
        exit();
    }

    if($myuser['storeclosed'] == 'yes' && $cur_hours > 0)
    {
        $myuser['storeclosed'] = 'no';
        $myuser['openstore'] = 'yes';

        $GLOBALS['database']->FetchNone('
			UPDATE monster_users
			SET
				storeclosed=\'no\',
				openstore=\'yes\'
			WHERE idnum=' . $myuser['idnum'] . '
			LIMIT 1
		');

        global $user;

        $user['storeclosed'] = 'yes';
    }
}

function hourly_pet_sleeping(&$mypet, &$myuser, $hour)
{
    $light_sleeper_modifier = ($mypet['merit_light_sleeper'] == 'yes' ? 10 : 0);

    if($mypet['caffeinated'] > 0)
        $mypet['caffeinated']--;

    if($mypet['inspired'] > 0)
        $mypet['inspired']--;

    gain_energy($mypet, rand(2 + floor($mypet['sta'] / 2), 3 + $mypet['sta']));
    $mypet['asleep_time']++;

    // negative loss is not counted; this is a hacky way to randomize loss while sleeping
    lose_stat($mypet, 'food', mt_rand(-2, ($preg_days >= 14 ? 2 : 1)));

    $energy_percent = $mypet['energy'] / max_energy($mypet);

    $energy_percent *= $energy_percent * $energy_percent * $energy_percent * 100;

    if($mypet['food'] <= 3)
    {
        $desire = -($mypet['food'] - 4);
        $hungry = ceil($desire * $desire);
    }
    else
        $hungry = 0;

    if(rand(1, 100) <= $energy_percent + $hungry + $light_sleeper_modifier)
    {
        //add_pet_feeling($mypet['idnum'], 'energy', $mypet['asleep_time'] * 100, 100, 'Slept for ' . $mypet['asleep_time'] . ' hours.');
        $mypet['sleeping'] = 'no';
        $mypet['asleep_time'] = 0;
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', $mypet['petname'] . ' woke up.');

        if(mt_rand(1, $mypet['dream_rate']) == 1)
        {
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', dream_description($mypet));
            record_pet_stat($mypet['idnum'], 'Remembered a Dream', 1);
        }
    }
    else
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep');

//  simulate_pet_feelings($mypet['idnum'], true);
}

function electrical_engineering_dice(&$mypet)
{
    $dice = floor($mypet['skill_int'] * .75 + $mypet['skill_wit'] / 2 + $mypet['skill_per'] / 4 + $mypet['skill_electronics'] * 1.25)  + $mypet['knack_electronics'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_lightning_calculator'] == 'yes')
        $dice++;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function mechanical_engineering_dice(&$mypet)
{
    $dice = floor($mypet['skill_int'] * .75 + $mypet['skill_wit'] * .75 + $mypet['skill_per'] / 4) + $mypet['skill_mechanics'] + $mypet['knack_mechanics'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function chemistry_dice(&$mypet)
{
    $dice = floor($mypet['skill_int'] * .75 + $mypet['skill_wit'] / 2 + $mypet['skill_per'] / 4) + $mypet['skill_chemistry'] * 1.25 + $mypet['knack_chemistry'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function smithing_dice(&$mypet)
{
    $dice = floor($mypet['skill_str'] * .75 + $mypet['skill_sta'] / 2 + $mypet['skill_int'] / 2) + $mypet['skill_smithing'] + $mypet['knack_smithing'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_tough_hide'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function crafting_dice(&$mypet)
{
    $dice = floor($mypet['skill_per'] * .75 + $mypet['skill_int'] / 2 + $mypet['skill_dex'] / 2) + $mypet['skill_crafting'] + $mypet['knack_crafting'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function tailoring_dice(&$mypet)
{
    $dice = floor($mypet['skill_dex'] * .75 + $mypet['skill_per'] / 2 + $mypet['skill_int'] / 2) + $mypet['skill_tailoring'] + $mypet['knack_tailory'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function leatherworking_dice(&$mypet)
{
    $dice = floor($mypet['skill_dex'] * .75 + $mypet['skill_per'] / 2 + $mypet['skill_int'] / 2) + $mypet['skill_leather'] + $mypet['knack_leather'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function painting_dice(&$mypet)
{
    $dice = floor($mypet['skill_per'] * .75 + $mypet['skill_int'] / 3 + $mypet['skill_dex'] / 3 + $mypet['skill_wit'] / 3) + $mypet['skill_painting'] + $mypet['knack_painting'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function sculpting_dice(&$mypet)
{
    $dice = floor($mypet['skill_per'] * .75 + ($mypet['skill_dex'] / 3) * 2 + $mypet['skill_int'] / 3) + $mypet['skill_sculpting'] + $mypet['knack_sculpting'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function gardening_dice(&$mypet)
{
    $dice = floor($mypet['skill_int'] * .75 + $mypet['skill_per'] / 2 + $mypet['skill_sta'] / 2) + $mypet['skill_gardening'] + $mypet['knack_gradening'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    return $dice;
}

function carpentry_dice(&$mypet)
{
    $dice = floor($mypet['skill_dex'] * .75 + $mypet['skill_per'] / 2 + $mypet['skill_str'] / 2) + $mypet['skill_carpentry'] + $mypet['knack_carpentry'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function jeweling_dice(&$mypet)
{
    $dice = floor($mypet['skill_per']       + $mypet['skill_dex'] / 2 + $mypet['skill_int'] / 4) + $mypet['skill_jeweling'] + $mypet['knack_jeweling'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function gathering_dice(&$mypet)
{
    $dice = floor($mypet['skill_per'] * .75 + $mypet['skill_int'] / 2 + $mypet['skill_sta'] / 2) + $mypet['skill_gathering'] + $mypet['knack_gathering'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_acute_senses'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function mining_dice(&$mypet)
{
    $dice = floor($mypet['skill_str'] * .75 + $mypet['skill_sta'] / 2 + $mypet['skill_per'] / 2) + $mypet['skill_mining'] + $mypet['knack_mining'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function lumberjacking_dice(&$mypet)
{
    $dice = floor($mypet['skill_str'] * .75 + $mypet['skill_sta'] / 2 + $mypet['skill_per'] / 2) + $mypet['skill_lumberjacking'] + $mypet['knack_lumberjacking'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function fishing_dice(&$mypet)
{
    $dice = floor($mypet['skill_dex'] * .75 + ($mypet['skill_per'] / 3) * 2 + $mypet['skill_stealth'] / 3) + $mypet['skill_fishing'] + $mypet['knack_fishing'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['merit_steady_hands'] == 'yes')
        $dice++;

    if($mypet['merit_acute_senses'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function vhagst_dice(&$mypet)
{
    $dice = floor($mypet['skill_wit'] * .75 + $mypet['skill_dex'] / 2 + $mypet['skill_int'] / 2) + $mypet['skill_stealth'] + $mypet['knack_videogames'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice--;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function hunting_dice(&$mypet)
{
    $dice = floor(($mypet['skill_athletics'] / 3) * 2 + $mypet['skill_str'] / 2 + $mypet['skill_per'] / 2 + $mypet['skill_stealth'] / 3) + $mypet['skill_hunting'] + $mypet['knack_hunting'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_acute_senses'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // only moonkin should be applied AFTER nasty wound and other multipliers
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function adventuring_dice(&$mypet)
{
    $dice = floor($mypet['skill_str'] / 2 + $mypet['skill_athletics'] / 2 + $mypet['skill_sta'] / 2 + $mypet['skill_stealth'] / 4) + $mypet['skill_adventuring'] + $mypet['knack_adventuring'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_berserker'] == 'yes')
        $dice++;

    if($mypet['merit_tough_hide'] == 'yes')
        $dice++;

    if($mypet['merit_catlike_balance'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // moonkin is applied AFTER nasty wound and other multipliers - nothing else should be, though!
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function binding_dice(&$mypet)
{
    $dice = floor($mypet['skill_int'] * .75 + $mypet['skill_sta'] / 2 + $mypet['skill_wit'] / 2) + $mypet['skill_binding'] + $mypet['knack_binding'];

    if($mypet['merit_lucky'] == 'yes' && mt_rand(1, 7) == 7)
        $dice += 2;

    if($mypet['merit_medium'] == 'yes')
        $dice++;

    if($mypet['nasty_wound'] > 0)
        $dice = ceil($dice * 0.75);

    if($mypet['merit_moonkin'] == 'yes') // moonkin is applied AFTER nasty wound and other multipliers - nothing else should be, though!
        $dice += moon_phase_power(time());

    if($dice < 1) $dice = 1;

    return $dice;
}

function hourly_pet(&$mypet, &$myprojects, &$myuser, $hour, $num_pets, &$house_size, $max_size, &$house, &$monster_list, &$prey_list, &$location_list, &$game_room, &$game_room_games, &$farm, $open_aquarium, $open_fire, $online)
{
    // dead pets... you know... are dead.
    if($mypet['dead'] != 'no')
        return;

    global $simulated_time;

    $HALLOWEEN = (date('M d', $simulated_time) == 'Oct 31' || date('M d', $simulated_time) == 'Oct 30');

    $effective_max_size = min(max_house_size(), $max_size);

    $newpet = $mypet;

    $preg_days = $mypet['pregnant_asof'];

    lose_stat($newpet, 'food',   ($preg_days >= 14 ? 2 : 1));
    lose_stat($newpet, 'energy', rand(0, ($preg_days >= 14 ? rand(1, 2) : 1)));

    if($newpet['caffeinated'] > 0)
        $newpet['caffeinated']--;

    if($newpet['inspired'] > 0)
        $newpet['inspired']--;

    lose_stat($newpet, 'safety', 1);
    lose_stat($newpet, 'love',   1);
    lose_stat($newpet, 'esteem', 1);

    //simulate_pet_feelings($mypet['idnum']);

    // decide what the pet wants to do
    $todo = array();

    if($preg_days >= 14)
        $active_penalty = ($preg_days - 13);
    else
        $active_penalty = 0;

    if($preg_days >= 21)
        $gather_penalty = ($preg_days - 20);
    else
        $gather_penalty = 0;

    if($mypet['energy'] <= -12)
    {
        $newpet['sleeping'] = 'yes';
        $newpet['caffeinated'] = 0;
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', $mypet['petname'] . ' passed out.');

        $mypet = $newpet;
        return;
    }

    if($mypet['food'] <= -12)
    {
        $newpet['dead'] = 'starved';
        $newpet['pregnant_asof'] = 0;

        $mypet = $newpet;
        return;
    }
    else if($mypet['food'] <= 5 && $mypet['zombie'] == 'no')
    {
        $desire = 6 - $mypet['food'];
        $todo['eat'] += ceil($desire * $desire) + $mypet['skill_conscientious'];
    }

    if($mypet['zombie'] == 'no' && $mypet['food'] > 0 && $mypet['energy'] > 0)
    {
        if($mypet['safety'] <= 10)
        {
            $desire = 11 - $mypet['safety'];
            $todo['safety'] += ceil($desire * $desire) + $mypet['skill_conscientious'];
            $todo['hangout'] += ceil($desire * $desire) + 1 + mt_rand(0, $mypet['skill_extraverted']) + mt_rand(0, $mypet['skill_conscientious']);
        }

        if($mypet['love'] <= 10 && $mypet['safety'] > 0)
        {
            $desire = 11 - $mypet['love'];
            $todo['love'] += ceil($desire * $desire) + $mypet['skill_conscientious'];
            $todo['hangout'] += ceil($desire * $desire) + 1 + mt_rand(0, $mypet['skill_extraverted']) + mt_rand(0, $mypet['skill_conscientious']);
        }

        if($mypet['esteem'] <= 10 && $mypet['safety'] > 0 && $mypet['love'] > 0)
        {
            $desire = 11 - $mypet['esteem'];
            $todo['esteem'] += ceil($desire * $desire) + $mypet['skill_conscientious'];
            $todo['hangout'] += ceil($desire * $desire) + 1 + mt_rand(0, $mypet['skill_extraverted']) + mt_rand(0, $mypet['skill_conscientious']);
        }
    }

    if($mypet['food'] > 0 && $mypet['caffeinated'] == 0 && $mypet['sleeping'] == 'no')
    {
        if($mypet['energy'] + $todo['eat'] < 0)
        {
            $newpet['sleeping'] = 'yes';
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', $mypet['petname'] . ' passed out.');

            $mypet = $newpet;
            return;
        }
        else if($mypet['energy'] < 6)
        {
            $chance = (6 - $mypet['energy']) / 6;
            $chance *= $chance * 100;
            $chance += $mypet['skill_conscientious'] * 2;

            if(rand(1, 100) + $todo['eat'] <= $chance)
            {
                $newpet['sleeping'] = 'yes';

                if($open_fire)
                {
                    gain_love($newpet, 2);
                    gain_safety($newpet, 2);

                    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', $mypet['petname'] . ' fell asleep by the fire.', array('love' => 2, 'safety' => 2));
                }
                else
                    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'sleep', $mypet['petname'] . ' fell asleep.');

                $mypet = $newpet;
                return;
            }
        }
    }

    // exclusions for werepets...

    if($mypet['changed'] == 'yes' || $mypet['zombie'] == 'yes')
    {
        // DON'T DO these things WHEN a werecreature
        unset($todo['hangout']);
    }
    else
    {
        // DO these things WHEN NOT a werecreature
        $todo['engineering']   += successes(electrical_engineering_dice($mypet)) + $mypet['tool']['equip_electronics'];
        $todo['mechanics']     += successes(mechanical_engineering_dice($mypet)) + $mypet['tool']['equip_mechanics'];
        $todo['chemistry']     += successes(chemistry_dice($mypet)) + $mypet['tool']['equip_chemistry'];

        $todo['smithing']      += successes(smithing_dice($mypet)) - $active_penalty + $mypet['tool']['equip_smithing'];

        $todo['binding']       += successes(binding_dice($mypet)) + $mypet['tool']['equip_binding'];

        $todo['crafting']      += successes(crafting_dice($mypet)) + $mypet['tool']['equip_crafting'];
        $todo['tailoring']     += successes(tailoring_dice($mypet)) + $mypet['tool']['equip_tailoring'];
        $todo['leatherworking']+= successes(leatherworking_dice($mypet)) + $mypet['tool']['equip_leather'];
        $todo['painting']      += successes(painting_dice($mypet)) + $mypet['tool']['equip_painting'];
        $todo['sculpting']     += successes(sculpting_dice($mypet)) + $mypet['tool']['equip_sculpting'];
        $todo['carpentry']     += successes(carpentry_dice($mypet)) - $gather_penalty + $mypet['tool']['equip_carpentry'];
        $todo['jeweling']      += successes(jeweling_dice($mypet)) + $mypet['tool']['equip_jeweling'];

        $todo['gathering']     += successes(gathering_dice($mypet)) - $gather_penalty + $mypet['tool']['equip_gathering'];
        $todo['mining']        += successes(mining_dice($mypet)) - $active_penalty + $mypet['tool']['equip_mining'];
        $todo['lumberjacking'] += successes(lumberjacking_dice($mypet)) - $active_penalty + $mypet['tool']['equip_lumberjacking'];

        $todo['fishing']       += successes(fishing_dice($mypet)) - $gather_penalty + $mypet['tool']['equip_fishing'];

        $todo['gardening']     += successes(gardening_dice($mypet)) + $mypet['tool']['equip_gardening'];

        if($online)
            $todo['hacking'] += successes(vhagst_dice($mypet)) + $mypet['tool']['equip_hacking'];
    }

    $todo['hunting']     += successes(hunting_dice($mypet)) - $active_penalty + $mypet['tool']['equip_hunting'];
    $todo['adventuring'] += successes(adventuring_dice($mypet)) - $active_penalty + $mypet['tool']['equip_adventuring'];

    if($HALLOWEEN === true && mt_rand(1, 2) != 1)
    {
        $todo['fightaliens'] = max($todo['adventuring'], $todo['hunting'], $todo['gathering'], $todo['fishing'], $todo['mining'], $todo['lumberjacking']) + mt_rand(-1, 1);
    }

    $break_tool = 0;

    $house_is_full = ($house_size >= $effective_max_size);

    if($house_is_full)
    {
        unset($todo['gathering']);
        unset($todo['hunting']);
        unset($todo['fishing']);
        unset($todo['adventuring']);
        unset($todo['mining']);
        unset($todo['lumberjacking']);
        unset($todo['fightaliens']);
    }

    $activitying = 'nothing';
    $hungry = false;

    do
    {
        $finding_something_to_do = false;
        /*
    if($house['rats'] == 'yes')
    {
      if(mt_rand(1, 8) == 1)
      {
        $nothing = 100000;
        if($hunting >= 4 || $advneturing > 4)
        {
          add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $mypet['petname'] . ' saw a rat in the house and caught it.');
          lose_stat($newpet, 'energy', 1);
          $house['rats'] = 'no';
        }
        else if(($hunting < $crafting || $hunting < $engineering) && ($advneturing < $crafting || $advneturing < $engineering))
        {
          add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $mypet['petname'] . ' saw a rat in the house, and scrambled up the furniture to get away from it.');
          lose_stat($newpet, 'safety', 1);
        }
        else
        {
          add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $mypet['petname'] . ' saw a rat in the house and chased it, but it got away.');
          lose_stat($newpet, 'energy', 1);
        }
      }
    }
*/
        if(count($todo) == 0)
        {
            $action = 'nothing';
            break;
        }
        else
        {
            $keys = array_keys($todo, max($todo));
            $action = $keys[array_rand($keys)];
        }

        if($newpet['nasty_wound'] > 0 && mt_rand(1, 70) <= 10 + $newpet['conscientious'] && $newpet['changed'] != 'yes' && $newpet['zombie'] != 'yes')
        {
            go_rest_up($newpet, $myuser, $hour);
        }
        else if($action == 'eat')
        {
            if(!eat_something($newpet, $hour))
            {
                // if there's not something to eat
                $todo['begging'] += $todo['eat'];
//        $todo['gardening'] += floor($todo['eat'] / 2); // gardening doesn't get pets food

                if(!$house_is_full)
                {
                    $todo['gathering'] += $todo['eat'];
                    $todo['hunting'] += $todo['eat'];
                    $todo['fishing'] += $todo['eat'];
                }

                $hungry = true;

                unset($todo['eat']);

                $finding_something_to_do = true;
            }
        }
        else if($action == 'safety')
        {
            if(!safety_something($newpet, $hour))
            {
                unset($todo['safety']);
                $finding_something_to_do = true;
            }
        }
        else if($action == 'love')
        {
            if(!love_something($newpet, $hour, $open_aquarium))
            {
                unset($todo['love']);
                $finding_something_to_do = true;
            }
        }
        else if($action == 'esteem')
        {
            if(!esteem_something($newpet, $hour, $open_aquarium))
            {
                unset($todo['esteem']);
                $finding_something_to_do = true;
            }
        }
        else if($action == 'begging')
        {
            go_begging($newpet, $myuser, $hour);
            $break_tool = 0;
            $activitying = 'begging for food';
        }
        else if($action == 'hangout')
        {
            if(go_hang_out($newpet, $myuser, $hour))
            {
                $break_tool = 0;
                $activity = 'hanging out with a friend';
            }
            else
            {
                unset($todo['hangout']);
                $finding_something_to_do = true;
            }
        }
        else if($action == 'fightaliens')
        {
            if(fight_aliens($newpet, $myuser, $hour))
            {
                $break_tool = mt_rand(mt_rand(1, 3), 4);
                $activitying = 'fighting aliens';
            }
            else
            {
                unset($todo['fightaliens']);
                $finding_something_to_do = true;
            }
        }
        else if(!$hungry && $game_room !== false && $game_room['money'] > 0 && mt_rand(0, 10) <= $newpet['playful'] - $newpet['conscientious'])
        {
            play_game_room($newpet, $myuser, $hour, $house_size, $game_room, $game_room_games);
            $break_tool = 0;
            $activitying = 'playing in the Game Room';
            lose_stat($newpet, 'energy', mt_rand(0, 1));
        }
        else if($action == 'gathering')
        {
            try_gathering('gather', $newpet, $myuser, $hour, $house_size, $location_list);
            $break_tool = mt_rand(1, 2);
            $activitying = 'gathering';
            lose_stat($newpet, 'energy', 1);
        }
        else if($action == 'mining')
        {
            try_gathering('mine', $newpet, $myuser, $hour, $house_size, $location_list);
            $break_tool = mt_rand(mt_rand(1, 3), 4);
            $activitying = 'mining';
            lose_stat($newpet, 'energy', mt_rand(1, 2));
        }
        else if($action == 'lumberjacking')
        {
            try_gathering('lumberjack', $newpet, $myuser, $hour, $house_size, $location_list);
            $break_tool = mt_rand(mt_rand(1, 2), 3);
            $activitying = 'lumberjacking';
            lose_stat($newpet, 'energy', mt_rand(1, 2));
        }
        else if($action == 'hunting')
        {
            try_hunting('hunt', $newpet, $myuser, $hour, $prey_list);
            lose_stat($newpet, 'food', 1);
            $break_tool = mt_rand(mt_rand(1, 3), 3);
            $activitying = 'hunting';
            lose_stat($newpet, 'energy', mt_rand(1, 2));
        }
        else if($action == 'fishing')
        {
            try_hunting('fish', $newpet, $myuser, $hour, $prey_list);
            $break_tool = mt_rand(1, mt_rand(1, 3));
            $activitying = 'fishing';
            lose_stat($newpet, 'energy', mt_rand(0, 1));
        }
        else if($action == 'adventuring')
        {
            try_adventuring($newpet, $myuser, $hour, $monster_list);
            lose_stat($newpet, 'food', 1);
            $break_tool = mt_rand(mt_rand(1, 3), 4);
            $activitying = 'adventuring';
            lose_stat($newpet, 'energy', mt_rand(1, 2));
        }
        else if(
            $action == 'crafting' ||
            $action == 'painting' ||
            $action == 'sculpting' ||
            $action == 'jeweling' ||
            $action == 'carpentry' ||
            $action == 'tailoring' ||
            $action == 'leatherworking' ||
            $action == 'binding' ||
            $action == 'gardening'
        )
        {
            $backup_activity = false;

            if($action == 'crafting')
            {
                $project_type = 'craft';
                $activitying = 'crafting';
            }
            else if($action == 'painting')
            {
                $project_type = 'paint';
                $activitying = 'painting';
                $backup_activity = mt_rand(1, 2) == 1 ? 'fishing' : 'gathering';
            }
            else if($action == 'sculpting')
            {
                $project_type = 'sculpture';
                $activitying = 'sculpting';
                $backup_activity = 'mining';
            }
            else if($action == 'jeweling')
            {
                $project_type = 'jewel';
                $activitying = 'jeweling';
                $backup_activity = 'mining';
            }
            else if($action == 'carpentry')
            {
                $project_type = 'carpenter';
                $activitying = 'carpentering';
                $backup_activity = 'lumberjacking';
            }
            else if($action == 'tailoring')
            {
                $project_type = 'tailor';
                $activitying = 'tailoring';
            }
            else if($action == 'leatherworking')
            {
                $project_type = 'leatherwork';
                $activitying = 'working with leather';
                $backup_activity = 'hunt';
            }
            else if($action == 'binding')
            {
                $project_type = 'binding';
                $activitying = 'binding';
            }
            else if($action == 'gardening')
            {
                $project_type = 'gardening';
                $activitying = 'gardening';
                $backup_activity = 'gathering';
            }

            $worked = false;

            // find an already-existing crafting project to work on
            if(count($myprojects) > 0)
            {
                foreach($myprojects as $localid=>$project)
                {
                    if($project['type'] == $project_type)
                        $work_on_it = true;
                    else if($project['type'] == 'construct' && $project_type == 'carpenter')
                        $work_on_it = true;
                    else
                        $work_on_it = false;

                    if($work_on_it && $project['complete'] == 'no' && works_on_project($newpet['skill_conscientious'], $newpet['skill_independent'], $project['priority']))
                    {
                        $finished = work_on_project($myprojects[$localid], $newpet, $myuser, $hour, $house_size);

                        if($finished === true)
                            unset($myprojects[$localid]);

                        $worked = true;
                        $break_tool = mt_rand(mt_rand(1, 2), 2);

                        lose_stat($newpet, 'energy', mt_rand(0, 1));

                        break;
                    }
                }
            }

            // if you didn't work on a project, make a new one
            if($worked === false)
            {
                if($action == 'gardening')
                {
                    if($farm !== false && $farm['field_active'] == 'yes')
                    {
                        do_farming($myuser['idnum'], $newpet, $farm, $hour);
                        $break_tool = mt_rand(mt_rand(1, 2), 3);
                    }
                    else
                        $finding_something_to_do = true;

                    unset($todo[$action]);
                }
                else if(start_project($project_type, $newpet, $myuser, $hour, $house_size))
                {
                    load_user_projects($myuser, $myprojects);
                    $break_tool = mt_rand(mt_rand(1, 2), 2);

                    lose_stat($newpet, 'energy', rand(0, 1));
                }
                else
                {
                    $finding_something_to_do = true;
                    if($backup_activity !== false)
                        $todo[$backup_activity] += mt_rand(1, max(2, $todo[$action]));
                    unset($todo[$action]);
                }
            }
        }
        else if($action == 'engineering' || $action == 'mechanics' || $action == 'chemistry')
        {
            if($action == 'engineering')
            {
                $project_type = 'engineer';
                $activitying = 'designing electronics';
            }
            else if($action == 'mechanics')
            {
                $project_type = 'mechanical';
                $activitying = 'designing a contraption';
            }
            else if($action == 'chemistry')
            {
                $project_type = 'chemistry';
                $activitying = 'working with chemicals';
            }

            $worked = false;

            // find an already-existing engineering project to work on
            if(count($myprojects) > 0)
            {
                foreach($myprojects as $localid=>$project)
                {
                    if($project['type'] == $project_type && $project['complete'] == 'no' && works_on_project($newpet['skill_conscientious'], $newpet['skill_independent'], $project['priority']))
                    {
                        $finished = work_on_project($myprojects[$localid], $newpet, $myuser, $hour, $house_size);

                        if($finished === true)
                            unset($myprojects[$localid]);

                        $worked = true;
                        $break_tool = mt_rand(mt_rand(1, 2), 2);

                        lose_stat($newpet, 'energy', rand(0, 1));

                        break;
                    }
                }
            }

            // if you didn't work on a project, make a new one
            if($worked === false)
            {
                if(start_project($project_type, $newpet, $myuser, $hour, $house_size))
                {
                    load_user_projects($myuser, $myprojects);
                    $break_tool = mt_rand(mt_rand(1, 2), 2);

                    lose_stat($newpet, 'energy', rand(0, 1));
                }
                else
                {
                    $finding_something_to_do = true;
                    unset($todo[$action]);
                }
            }
        }
        else if($action == 'smithing')
        {
            $worked = false;

            // find an already-existing engineering project to work on
            if(count($myprojects) > 0)
            {
                foreach($myprojects as $localid=>$project)
                {
                    if($project['type'] == 'smith' && $project['complete'] == 'no' && works_on_project($newpet['skill_conscientious'], $newpet['skill_independent'], $project['priority']))
                    {
                        $finished = work_on_project($myprojects[$localid], $newpet, $myuser, $hour, $house_size);

                        if($finished === true)
                            unset($myprojects[$localid]);

                        $worked = true;
                        $break_tool = mt_rand(mt_rand(1, 3), 3);
                        $activitying = 'smithing';

                        lose_stat($newpet, 'energy', 1);

                        break;
                    }
                }
            }

            // if you didn't work on a project, make a new one
            if($worked === false)
            {
                if(start_project('smith', $newpet, $myuser, $hour, $house_size))
                {
                    load_user_projects($myuser, $myprojects);
                    $break_tool = mt_rand(mt_rand(1, 3), 3);
                    $activitying = 'smithing';

                    lose_stat($newpet, 'energy', 1);
                }
                else
                {
                    $finding_something_to_do = true;
                    $todo['mining'] += floor($todo['smithing'] / 2);
                    unset($todo['smithing']);
                }
            }
        }
        else if($action == 'hacking')
        {
//      mail('ben@telkoth.net', 'Virtual Hide-and-go-Seek Tag', $newpet['petname'] . ' will give it a try!', 'From: sender@telkoth.net' . "\n");

            if(online_activity($newpet, $myuser, $hour))
            {
                $break_tool = 1;
                $activitying = 'playing Virtual Hide-and-go-Seek Tag';
                lose_stat($newpet, 'energy', mt_rand(1, 3) == 1 ? 0 : 1);
            }
            else
            {
                $finding_something_to_do = true;
                unset($todo['hacking']);
            }
        }

    } while($finding_something_to_do);

    if($activitying == 'nothing' && $newpet['Toadstool'] === true)
    {
        global $AWARD_BADGE;
        $AWARD_BADGE['Toadstool'] = true;
    }

    if($newpet['zombie'] == 'yes' && mt_rand(1, 15) <= $break_tool)
        deteriorate_zombie($newpet, $myuser, $hour);

    if($break_tool > 0 && $newpet['merit_careful_with_equipment'] == 'yes' && mt_rand(1, 4) == 1)
        $break_tool--;

    if($newpet['tool'] !== false && $break_tool > 0 && $newpet['tool']['durability'] > 0)
    {
        $newpet['realtool']['health'] -= $break_tool;

        if($newpet['realtool']['health'] <= 0)
        {
            record_pet_stat($mypet['idnum'], 'Broke a Tool', 1);
            record_pet_stat($mypet['idnum'], 'Broke a Tool While ' . ucfirst($activitying), 1);

            $q_message = quote_smart('This ' . $newpet['tool']['itemname'] . " was ruined while $activitying.");
            $command = "UPDATE monster_inventory SET itemname='Ruins',message=$q_message WHERE idnum=" . (int)$newpet['toolid'] . ' LIMIT 1';
            $GLOBALS['database']->FetchNone($command, 'hourly_pet');

            $netpet['costumed'] = 'no';
            $newpet['tool'] = false;
            $newpet['realtool'] = false;
        }
        else
        {
            $GLOBALS['database']->FetchNone('
				UPDATE monster_inventory
				SET health=health-' . $break_tool . '
				WHERE idnum=' . (int)$newpet['toolid'] . '
				LIMIT 1
			');
        }
    }

    if($newpet['safety'] < -24)
        $newpet['safety'] = -24;
    if($newpet['love'] < -36)
        $newpet['love'] = -36;
    if($newpet['esteem'] < -48)
        $newpet['esteem'] = -48;

    $mypet = $newpet;
}

function delete_project($idnum)
{
    $GLOBALS['database']->FetchNone('
		DELETE FROM monster_projects
		WHERE idnum=' . quote_smart($idnum) . '
		LIMIT 1
	');
}

function work_on_homeimprovement(&$project, &$mypet, &$myuser, $hour)
{
    $bonus_dice = equipment_specific_bonus($mypet['tool'], $project);

    $success_dice = successes(carpentry_dice($mypet) + $bonus_dice);

    if($success_dice < 1)
        return false;

    $command = 'SELECT * FROM psypets_homeimprovement WHERE idnum=' . $project['itemid'] . ' LIMIT 1';
    $improvement = fetch_single($command, 'fetching home improvement data');

    $project['progress'] += $success_dice;
    $project['notes'] .= "\n" . $mypet['petname'] . ' worked on this construction.';

    $esteem_gain = gain_esteem($mypet, successes($success_dice));

    $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

    if($project['progress'] >= $improvement['requirement'])
    {
        $house = get_house_byuser($myuser['idnum']);
        if($house === false)
        {
            echo "Failed to load your house.<br />\n";
            exit();
        }

        $addons = take_apart(',', $house['addons']);
        $addons[] = $improvement['name'];
        $new_addons = implode(',', $addons);

        $command = "UPDATE monster_houses SET `addons`=" . quote_smart($new_addons) . " WHERE idnum=" . $house['idnum'] . " LIMIT 1";
        $GLOBALS['database']->FetchNone($command, 'adding house add-on');

        delete_project($project['idnum']);

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'construction_success', '<span class="success">' . $mypet['petname'] . ' finished the ' . $improvement['name'] . ' construction project.</span>', array('esteem' => $esteem_gain));

        $mypet['actions_since_last_level']++;

        train_pet($mypet, 'carpentry', ceil($success_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'dex', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'per', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'str', ceil($success_dice / 4) + $bonus_exp, $hour);

        return true;
    }
    else
    {
        $GLOBALS['database']->FetchNone('
			UPDATE monster_projects
			SET
				progress=progress+' . ($success_dice) . ',
				notes=' . quote_smart($project['notes']) . '
			WHERE idnum=' . $project['idnum'] . '
			LIMIT 1
		');

        $mypet['actions_since_last_level']++;

        train_pet($mypet, 'carpentry', ceil($success_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'dex', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'per', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'str', ceil($success_dice / 4) + $bonus_exp, $hour);

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'construction_success', '<span class="progress">' . $mypet['petname'] . ' worked on the ' . $improvement['name'] . ' construction project.</span>', array('esteem' => $esteem_gain));

        return true;
    }

    return false;
}

// returns TRUE if the project is done and should be deleted
// returns FALSE any other time
function work_on_project(&$project, &$mypet, &$myuser, $hour, &$house_size)
{
    global $MATERIALS_LIST;

    $special_actions = array();
    $main_training = false;
    $training = array();

    if($project['type'] == 'construct')
    {
        return work_on_homeimprovement($project, $mypet, $myuser, $hour);
    }
    else if($project['type'] == 'craft')
    {
        $dice = crafting_dice($mypet);
        $actiontype = 'handicrafts';
        $details = get_craft_byid($project['projectid']);

        $main_training = 'cra';
        $training[] = 'per';
        $training[] = 'int';
        $training[] = 'dex';
    }
    else if($project['type'] == 'paint')
    {
        $dice = painting_dice($mypet);
        $actiontype = 'painting';
        $details = get_painting_byid($project['projectid']);

        $main_training = 'painting';
        $training[] = 'per';
        $training[] = 'int';
        $training[] = 'dex';
        $training[] = 'wit';
    }
    else if($project['type'] == 'engineer')
    {
        $dice = electrical_engineering_dice($mypet);
        $actiontype = 'electronics';
        $details = get_invention_byid($project['projectid']);

        $main_training = 'eng';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($project['type'] == 'smith')
    {
        $dice = smithing_dice($mypet);
        $actiontype = 'smithing';
        $details = get_smith_byid($project['projectid']);

        if($mypet['special_firebreathing'] == 'yes' && mt_rand(1, 2) == 1)
        {
            $special_actions[] = '<strong>breathing fire</strong>';
            $dice += 2;
        }

        $main_training = 'smi';
        $training[] = 'str';
        $training[] = 'sta';
        $training[] = 'int';
    }
    else if($project['type'] == 'tailor')
    {
        $dice = tailoring_dice($mypet);
        $actiontype = 'tailoring';
        $details = get_tailor_byid($project['projectid']);

        $main_training = 'tai';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'int';
    }
    else if($project['type'] == 'leatherwork')
    {
        $dice = leatherworking_dice($mypet);
        $actiontype = 'leatherworking';
        $details = get_leatherworking_byid($project['projectid']);

        $main_training = 'leather';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'int';
    }
    else if($project['type'] == 'mechanical')
    {
        $dice = mechanical_engineering_dice($mypet);
        $actiontype = 'mechanics';
        $details = get_mechanics_byid($project['projectid']);

        $main_training = 'mechanics';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($project['type'] == 'sculpture')
    {
        $dice = sculpting_dice($mypet);
        $actiontype = 'sculpting';
        $details = get_sculpture_byid($project['projectid']);

        $main_training = 'sculpting';
        $training[] = 'per';
        $training[] = 'dex';
        $training[] = 'int';
    }
    else if($project['type'] == 'carpenter')
    {
        $dice = carpentry_dice($mypet);
        $actiontype = 'carpentry';
        $details = get_carpentry_byid($project['projectid']);

        $main_training = 'carpentry';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'str';
    }
    else if($project['type'] == 'jewel')
    {
        $dice = jeweling_dice($mypet);
        $actiontype = 'jewelry';
        $details = get_jewelry_byid($project['projectid']);

        $main_training = 'jeweling';
        $training[] = 'per';
        $training[] = 'dex';
        $training[] = 'int';
    }
    else if($project['type'] == 'binding')
    {
        $dice = binding_dice($mypet);
        $actiontype = 'binding';
        $details = get_binding_byid($project['projectid']);

        $main_training = 'binding';
        $training[] = 'int';
        $training[] = 'sta';
        $training[] = 'wit';
    }
    else if($project['type'] == 'chemistry')
    {
        $dice = chemistry_dice($mypet);
        $actiontype = 'chemistry';
        $details = get_chemistry_byid($project['projectid']);

        $main_training = 'chemistry';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($project['type'] == 'gardening')
    {
        $dice = gardening_dice($mypet);
        $actiontype = 'gardening';
        $details = get_gardening_byid($project['projectid']);

        $main_training = 'gathering';
        $training[] = 'int';
        $training[] = 'per';
        $training[] = 'sta';
    }
    else
    {
        echo '
      work_on_project(...):
      \'' . $project['type'] . '\' is not a recognized project type.
    ';
        exit();
    }

    if($details['idnum'] != $project['projectid'])
        return false;

    if($details['solo'] == 'yes' && $project['creator'] != 'p:' . $mypet['idnum'])
    {
        $esteem_gain = gain_esteem($mypet, 8);
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'esteem', '<span class="failure">' . $mypet['petname'] . ' admired the ' . $details['makes'] . ' ' . $actiontype . ' project.</span>', array('esteem' => $esteem_gain));
        return true;
    }

    $dice += equipment_specific_bonus($mypet['tool'], $details);

    $success_dice = successes($dice);

    if($success_dice < 1)
    {
        train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        if(mt_rand(1, 900) == 1)
        {
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $project['type'] . '_failure', '<span class="failure">' . $mypet['petname'] . ' considered working on the ' . $details['makes'] . ' ' . $actiontype . ' project, but suffered a wound and was unable to make any progress!</span>');
            $mypet['nasty_wound'] = max(168, $mypet['nasty_wound'] + mt_rand(12, 72));
            record_pet_stat($mypet['idnum'], 'Suffered a Wound', 1);
        }
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $project['type'] . '_failure', '<span class="failure">' . $mypet['petname'] . ' considered working on the ' . $details['makes'] . ' ' . $actiontype . ' project, but couldn\'t come up with anything useful to add.</span>');

        return true;
    }

    $project['progress'] += $success_dice;
    $project['notes'] .= "\n" . $mypet['petname'] . " worked on this $actiontype project.";

    if(count($special_actions) == 0)
        $special = '';
    else
        $special = ' - ' . implode(', ', $special_actions) . ' -';

    if($dice - 5 < $details['difficulty'])
    {
        $esteem_gain = mt_rand(1, 4);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);
    }
    else if($dice - 10 < $details['difficulty'])
    {
        $esteem_gain = mt_rand(1, 2);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);
    }
    else
        $esteem_gain = 0;

    if($details['makes'] == 'Vase' || $details['makes'] == 'Ornate Vase' || $details['makes'] == 'Midheaven Vase')
    {
        set_pet_badge($mypet, 'potter');
    }
    else if($details['makes'] == 'Spring Bloom' || $details['makes'] == 'Death\'s Head')
    {
        set_pet_badge($mypet, 'illuminator');
    }

    $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

    if($project['progress'] >= $details['complexity'])
    {
        add_inventory_cached($myuser['user'], $project['creator'], $details['makes'], $mypet['petname'] . " completed this $actiontype project.", $project['destination']);

        if($project['destination']{5} != '$')
            $MATERIALS_LIST[$details['makes']]++;

        $item = get_item_byname($details['makes']);

        $house_size   += $item['bulk'];

        delete_project($project['idnum']);

        if($project['destination'] == 'home')
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $project['type'] . '_success', '<span class="success">' . $mypet['petname'] . $special . ' finished the ' . $item['itemname'] . ' ' . $actiontype . ' project.</span>', array('esteem' => $esteem_gain));
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $project['type'] . '_success', '<span class="success">' . $mypet['petname'] . $special . ' finished the ' . $item['itemname'] . ' ' . $actiontype . ' project and placed it in ' . say_room_of_house($project['destination']) . '.</span>', array('esteem' => $esteem_gain));

        if(
            $details['makes'] == 'Portrait of Adele Bloch-Bauer I' ||
            $details['makes'] == 'Nude, Green Leaves and Bust'
        )
        {
            $mypet['ascend'] = 'yes';
            $mypet['ascend_painter'] = 'yes';
            set_pet_badge($mypet, 'masterpainter');
        }

        $mypet['actions_since_last_level']++;

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);

        return true;
    }
    else
    {
        $GLOBALS['database']->FetchNone('
			UPDATE monster_projects
			SET
				progress=progress+' . ($success_dice) . ',
				notes=' . quote_smart($project['notes']) . '
			WHERE idnum=' . $project['idnum'] . '
			LIMIT 1
		');

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $project['type'] . '_success', '<span class="progress">' . $mypet['petname'] . $special . ' worked on the ' . $details['makes'] . ' ' . $actiontype . ' project.</span>', array('esteem' => $esteem_gain));

        $mypet['actions_since_last_level']++;

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);

        return true;
    }

    return false;
}

// returns TRUE if the project is started without internal errors
// returns FALSE otherwise
function start_project($act, &$mypet, &$myuser, $hour, &$house_size)
{
    global $MATERIALS_LIST, $now_month, $now_day;

    if($act == 'construction')
    {
        $construction = true;

        switch(mt_rand(1, 3))
        {
            case 1:
                $act = 'carpentry';
                break;
            case 2:
                $act = 'smith';
                break;
            case 3:
                $act = 'sculpture';
                break;
        }

        $extra = 'AND addon=\'yes\'';
    }
    else
        $extra = '';

    $special_actions = array();
    $training = array();

    if($act == 'craft')
    {
        $max = crafting_dice($mypet);
        $table = 'psypets_crafts';
        $actiontype = 'handicraft';
        $descripted = 'crafted';
        $descript = 'craft';
        $an_action = 'a handicraft';

        $main_training = 'cra';
        $training[] = 'per';
        $training[] = 'int';
        $training[] = 'dex';
    }
    else if($act == 'paint')
    {
        $max = painting_dice($mypet);
        $table = 'psypets_paintings';
        $actiontype = 'painting';
        $descripted = 'painted';
        $descript = 'paint';
        $an_action = 'a painting';

        $main_training = 'painting';
        $training[] = 'per';
        $training[] = 'int';
        $training[] = 'dex';
        $training[] = 'wit';
    }
    else if($act == 'sculpture')
    {
        $max = sculpting_dice($mypet);
        $table = 'psypets_sculptures';
        $actiontype = 'sculpture'; // <petname> started X (project)
        $descripted = 'sculpted';  // <petname> X <itemname>
        $descript = 'sculpt';      // <petname> wanted to X <itemname>
        $an_action = 'a sculpture';// <petname> considered starting X project

        $main_training = 'sculpting';
        $training[] = 'per';
        $training[] = 'dex';
        $training[] = 'int';
    }
    else if($act == 'jewel')
    {
        $max = jeweling_dice($mypet);
        $table = 'psypets_jewelry';
        $actiontype = 'jewelry';   // <petname> started X (project)
        $descripted = 'bejeweled'; // <petname> X <itemname>
        $descript = 'bejewel';     // <petname> wanted to X <itemname>
        $an_action = 'a jewelry';  // <petname> considered starting X project

        $main_training = 'jeweling';
        $training[] = 'per';
        $training[] = 'dex';
        $training[] = 'int';
    }
    else if($act == 'engineer')
    {
        $max = electrical_engineering_dice($mypet);
        $table = 'psypets_inventions';
        $actiontype = 'electrical engineering';   // <petname> started X (project)
        $descripted = 'electrically engineered';  // <petname> X <itemname>
        $descript = 'electrically engineer';      // <petname> wanted to X <itemname>
        $an_action = 'an electrical engineering'; // <petname> considered starting X project

        $main_training = 'eng';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($act == 'mechanical')
    {
        $max = mechanical_engineering_dice($mypet);
        $table = 'psypets_mechanics';
        $actiontype = 'mechanical engineering';   // <petname> started X (project)
        $descripted = 'mechanically engineered';  // <petname> X <itemname>
        $descript = 'mechanically engineer';      // <petname> wanted to X <itemname>
        $an_action = 'a mechanical engineering';  // <petname> considered starting X project

        $main_training = 'mechanics';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($act == 'chemistry')
    {
        $max = chemistry_dice($mypet);
        $table = 'psypets_chemistry';
        $actiontype = 'chemistry';             // <petname> started X (project)
        $descripted = 'chemically engineered'; // <petname> X <itemname>
        $descript = 'chemically engineer';     // <petname> wanted to X <itemname>
        $an_action = 'a chemistry';            // <petname> considered starting X project

        $main_training = 'chemistry';
        $training[] = 'int';
        $training[] = 'wit';
        $training[] = 'per';
    }
    else if($act == 'smith')
    {
        $max = smithing_dice($mypet);
        $table = 'psypets_smiths';
        $actiontype = 'smithing';
        $descripted = 'smithed';
        $descript = 'smith';
        $an_action = 'a smithing';

        if($mypet['special_firebreathing'] == 'yes' && mt_rand(1, 2) == 1)
        {
            $special_actions[] = '<strong>breathing fire</strong>';
            $max += 2;
        }

        $main_training = 'smi';
        $training[] = 'str';
        $training[] = 'sta';
        $training[] = 'int';
    }
    else if($act == 'tailor')
    {
        $max = tailoring_dice($mypet);
        $table = 'psypets_tailors';
        $actiontype = 'tailory';
        $descripted = 'tailored';
        $descript = 'tailor';
        $an_action = 'a tailoring';

        $main_training = 'tai';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'int';
    }
    else if($act == 'leatherwork')
    {
        $max = leatherworking_dice($mypet);
        $table = 'psypets_leatherworks';
        $actiontype = 'leatherworking';
        $descripted = 'leatherworked';
        $descript = 'leatherwork';
        $an_action = 'a leatherworking';

        $main_training = 'leather';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'int';
    }
    else if($act == 'carpenter')
    {
        $max = carpentry_dice($mypet);
        $table = 'psypets_carpentry';
        $actiontype = 'carpentry';   // <petname> started X (project)
        $descripted = 'carpentered'; // <petname> X <itemname>
        $descript = 'carpenter';     // <petname> wanted to X <itemname>
        $an_action = 'a carpentry';  // <petname> considered starting X project

        $main_training = 'carpentry';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'str';
    }
    else if($act == 'binding')
    {
        $max = binding_dice($mypet);
        $table = 'psypets_bindings';
        $actiontype = 'magic-binding';   // <petname> started X (project)
        $descripted = 'magically bound'; // <petname> X <itemname>
        $descript = 'magically bind';    // <petname> wanted to X <itemname>
        $an_action = 'a magic-binding';  // <petname> considered starting X project

        $main_training = 'binding';
        $training[] = 'int';
        $training[] = 'sta';
        $training[] = 'wit';
    }
    else
    {
        echo "error in start_project: '$act' is not a recognized action.<br>\n";
        return false;
    }

    $considerations = $GLOBALS['database']->FetchMultiple('
    SELECT * FROM (
      SELECT *
      FROM ' . $table . '
      WHERE
        difficulty>=1
        AND difficulty<=' . $max . '
        AND min_month<=' . $now_month . '
        AND ' . $now_month . '<=max_month
        AND ' . ($mypet['skill_open'] + mt_rand(0, mt_rand(1, 9))) . '>=min_openness
        AND ' . ($mypet['skill_open'] - mt_rand(0, mt_rand(1, 9))) . '<=max_openness
        AND ' . ($mypet['skill_playful'] + mt_rand(0, mt_rand(1, 9))) . '>=min_playful
        AND ' . ($mypet['skill_playful'] - mt_rand(0, mt_rand(1, 9))) . '<=max_playful
        AND ' . $mypet['skill_music'] . '>=min_music
        AND ' . $mypet['skill_astronomy'] . '>=min_astronomy
        ' . $extra . '
      ORDER BY RAND()
      LIMIT 15
    ) AS t1
    ORDER BY priority DESC
  ');

    if(count($considerations) == 0)
    {
        train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="obstacle">' . $mypet['petname'] . " couldn't come up with anything to $descript.</span>");
        return false;
    }

    $item_found = false;
    $items_attempted = array();

    foreach($considerations as $consider_item)
    {
        // don't remember secret crafts; we don't want to report them to the player!
        if($consider_item['secret'] != 'yes')
            $items_attempted[] = $consider_item['makes'];

        if(resources_available($consider_item['ingredients']) === true)
        {
            $item_found = $consider_item;
            break;
        }
    }

    if($item_found === false)
    {
        $attempt_item = $items_attempted[array_rand($items_attempted)];
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_unable', '<span class="obstacle">' . $mypet['petname'] . " wanted to $descript $attempt_item, but could not find enough materials.</span>");

        return false;
    }

    $max += equipment_specific_bonus($mypet['tool'], $item_found);

    $success_dice = successes($max);

    if($success_dice == 0)
    {
        train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        if($construction === true)
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . ' considered starting ' . $an_action . ' project for construction, but couldn\'t come up with anything.</span>');
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . ' considered starting ' . $an_action . ' project, but couldn\'t come up with anything.</span>');

        return true;
    }

    if(expend_resources($item_found['ingredients'], $myuser['user']) === false)
        return false;

    if($max - 5 < $item_found['difficulty'])
    {
        $esteem_gain = mt_rand(1, 4);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);
    }
    else if($max - 10 < $item_found['difficulty'])
    {
        $esteem_gain = mt_rand(1, 2);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);
    }
    else
        $esteem_gain = 0;

    if($item_found['makes'] == 'Spear of Destiny')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_smith'] = 'yes';
        set_pet_badge($mypet, 'mastersmith');
    }
    else if($item_found['makes'] == 'PSYCHE')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_inventor'] = 'yes';
        set_pet_badge($mypet, 'masterinvention');
    }
    else if($item_found['makes'] == 'Solar Sail')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_mechanic'] = 'yes';
        set_pet_badge($mypet, 'mastermechanics');
    }
    else if($item_found['makes'] == 'Ivory Giraffe Figurine')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_artist'] = 'yes';
        set_pet_badge($mypet, 'mastercraft');
    }
    else if($item_found['makes'] == 'Unreasonably Large Hoard of Unreasonably Large Swords')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_sculptor'] = 'yes';
        set_pet_badge($mypet, 'mastersculptor');
    }
    else if($item_found['makes'] == 'Silk Cloth' || $item_found['makes'] == 'Nanotube Weave')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_tailor'] = 'yes';
        set_pet_badge($mypet, 'mastertailor');
    }
    else if($item_found['makes'] == 'Small Greek Trireme' || $item_found['makes'] == 'White Amber Stick')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_carpenter'] = 'yes';
        set_pet_badge($mypet, 'mastercarpenter');
    }
    else if($item_found['makes'] == 'Imperial Scepter')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_jeweler'] = 'yes';
        set_pet_badge($mypet, 'masterjeweler');
    }
    else if($item_found['makes'] == 'Love')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_chemist'] = 'yes';
        set_pet_badge($mypet, 'masterchemist');
    }
    else if($item_found['makes'] == 'Hungry Cap')
    {
        $mypet['ascend'] = 'yes';
        $mypet['ascend_binder'] = 'yes';
        set_pet_badge($mypet, 'masterbinder');
    }

    if($item_found['makes'] == 'Vase' || $item_found['makes'] == 'Ornate Vase' || $item_found['makes'] == 'Midheaven Vase')
        set_pet_badge($mypet, 'potter');
    else if($item_found['makes'] == 'Spring Bloom' || $item_found['makes'] == 'Death\'s Head')
        set_pet_badge($mypet, 'illuminator');

    if(count($special_actions) == 0)
        $special = '';
    else
        $special = ' - ' . implode(', ', $special_actions) . ' -';

    $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

    if($success_dice >= $item_found['complexity'])
    {
        add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $item_found['makes'], $mypet['petname'] . $special . ' ' . $descripted . ' this item.', 'home');
        $MATERIALS_LIST[$item_found['makes']]++;

        $item = get_item_byname($item_found['makes']);

        $house_size   += $item['bulk'];

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_success', '<span class="success">' . $mypet['petname'] . $special . ' ' . $descripted . ' ' . $item_found['makes'] . '.</span>', array('esteem' => $esteem_gain));

        $mypet['actions_since_last_level']++;

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);

        return true;
    }
    else
    {
        $creator = 'p:' . $mypet['idnum'];

        $command = 'INSERT INTO monster_projects ' .
            '(`type`, `userid`, `creator`, `projectid`, `progress`, `notes`) ' .
            "VALUES ('$act', " . $myuser['idnum'] . ', ' . quote_smart($creator) . ', ' . $item_found['idnum'] . ", '$success_dice', " . quote_smart($mypet['petname'] . " started this $actiontype project.") . ')';
        $GLOBALS['database']->FetchNone($command, 'rpgfunctions.php/start_project()');

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_success', '<span class="progress">' . $mypet['petname'] . $special . ' started ' . $an_action . ' project: ' . $item_found['makes'] . '.</span>', array('esteem' => $esteem_gain));

        $mypet['actions_since_last_level']++;

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);

        return true;
    }
}

function try_gathering($act, &$mypet, &$myuser, $hour, &$house_size, &$location_list)
{
    global $MATERIALS_LIST, $PSYPETS_BIRTHDAY, $EASTER;
    global $now_day, $now_month;

    $training = array();

    if($act == 'gather')
    {
        $dice = gathering_dice($mypet);
        $gather_type_sql = '`type`=\'gather\'';
        $desc = 'gathered';
        $action = 'gather';

        $main_training = 'gathering';
        $training[] = 'int';
        $training[] = 'sta';
        $training[] = 'per';
    }
    else if($act == 'mine')
    {
        $dice = mining_dice($mypet);
        $gather_type_sql = '`type`=\'mine\'';
        $desc = 'mined';
        $action = 'mine';

        $main_training = 'mining';
        $training[] = 'str';
        $training[] = 'sta';
        $training[] = 'per';
    }
    else if($act == 'lumberjack')
    {
        $dice = lumberjacking_dice($mypet);
        $gather_type_sql = '`type`=\'lumberjack\'';
        $desc = 'lumberjacked';
        $action = 'lumberjack';

        $main_training = 'gathering';
        $training[] = 'str';
        $training[] = 'sta';
        $training[] = 'per';
    }
    else
    {
        echo "error in try_gathering: '$act' is not a recognized action.<br />\n";
        return false;
    }

    if($dice < 1)
        $dice = 1;

    $success_dice = successes($dice);

    $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

    if($success_dice > 0)
    {
        $where = array('in', 'near', 'around');
        $where_exactly = $where[array_rand($where)];

        $command = '
      (SELECT *
      FROM psypets_locations
      WHERE
        ' . $gather_type_sql . '
        AND level<=' . $success_dice . '
        AND min_month<=' . $now_month . '
        AND ' . $now_month . '<=max_month
        AND (needs_key=0 OR needs_key=' . (int)$mypet['key']['key_id'] . ')
      ORDER BY RAND()
      LIMIT 5)
      ORDER BY level DESC
      LIMIT 1
    ';
        $location = fetch_single($command, 'fetching gather location');

        if($location['needs_key'] > 0)
        {
            delete_inventory_byid($mypet['keyid']);
            $mypet['keyid'] = 0;
            unset($mypet['key']);
            unset($mypet['realkey']);
        }

        if($location['level'] == 0)
        {
            train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<font class="failure">' . $mypet['petname'] . " went out to $action, but couldn't find a good place to do it.</font>");
        }
        else
        {
            $prizes = array();
            $treasures = explode(',', $location['prizes']);

            if($act == 'gather')
            {
                if($now_month == 10 && $now_day == 11)
                {
                    $treasures[] = '20|Rainbow Cake';
                }
                else if($PSYPETS_BIRTHDAY)
                {
                    $treasures[] = '12|Red Party Hat';
                    $treasures[] = '12|Large Blue Balloon';
                    $treasures[] = '12|Large Yellow Balloon';
                    $treasures[] = '12|Slice of Birthday Cake';
                }
                else if($EASTER > 0)
                    $treasures[] = $EASTER . '|Plastic Egg';
            }

            foreach($treasures as $drop)
            {
                $rate = explode('|', $drop);

                if(mt_rand(1, 1000) <= $rate[0])
                {
                    if($EASTER > 0 && $rate[1] == 'Bird Nest')
                        $prizes[] = 'Weird Bird Nest';
                    else
                        $prizes[] = $rate[1];
                }
            }

            if($now_month == 11 && $act == 'gather')
            {
                $new_world_foods = array(
                    'Tomato', 'Whole Pumpkin', 'Corn', 'Potato', 'Peanuts', 'Baking Chocolate',
                    'Pineapple', 'Mango', 'Banana', 'Pamplemousse', 'Prickly Green', 'Pecans',
                    'Avocado'
                );

                if(mt_rand(1, 15) == 1)
                    $prizes[] = $new_world_foods[array_rand($new_world_foods)];
            }

            if($now_month == 12 && $now_day == 14 && ($act == 'gather' || $act == 'mine'))
            {
                record_pet_stat($mypet['idnum'], 'Found a Geminid', 1);

                if(mt_rand(1, 15) == 1)
                    $prizes[] = 'Small Geminid';
            }

            if(count($prizes) > 0)
            {
                foreach($prizes as $prize)
                {
                    if(($prize == 'Black Lotus' || $prize == 'Strange Tome') && $mypet['ascend_gatherer'] == 'no')
                    {
                        $mypet['ascend'] = 'yes';
                        $mypet['ascend_gatherer'] = 'yes';
                        set_pet_badge($mypet, 'mastergather');
                    }
                    else if($prize == 'Yggdrasil Branch' && $mypet['ascend_lumberjack'] == 'no')
                    {
                        $mypet['ascend'] = 'yes';
                        $mypet['ascend_lumberjack'] = 'yes';
                        set_pet_badge($mypet, 'masterlumberjack');
                    }
                    else if($prize == 'Mithryl' && $mypet['ascend_miner'] == 'no')
                    {
                        $mypet['ascend'] = 'yes';
                        $mypet['ascend_miner'] = 'yes';
                        set_pet_badge($mypet, 'masterminer');
                    }

                    add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $prize, $mypet['petname'] . ' ' . $desc . ' this ' . $where_exactly . ' ' . $location['name'] . '.', 'home');
                    $MATERIALS_LIST[$prize]++;
                }

                if($dice - 5 < $location['level'])
                {
                    $esteem_gain = mt_rand(1, 3 + count($prizes));
                    $esteem_gain = gain_esteem($mypet, $esteem_gain);
                }
                else if($dice - 10 < $location['level'])
                {
                    $esteem_gain = mt_rand(1, floor((3 + count($prizes)) / 2));
                    $esteem_gain = gain_esteem($mypet, $esteem_gain);
                }
                else
                    $esteem_gain = 0;

                add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_success', '<span class="success">' . $mypet['petname'] . ' ' . $desc . ' ' . $where_exactly . ' ' . $location['name'] . ' and found ' . implode(', ', $prizes) . '.</span>', array('esteem' => $esteem_gain));
            }
            else
            {
                train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

                if(mt_rand(1, 700) == 1)
                {
                    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . ' went ' . $where_exactly . ' ' . $location['name'] . ' to ' . $action . ', but suffered a wound and had to come back!</span>');
                    $mypet['nasty_wound'] = max(168, $mypet['nasty_wound'] + mt_rand(12, mt_rand(72, 96)));
                    record_pet_stat($mypet['idnum'], 'Suffered a Wound', 1);
                }
                else
                    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . ' went ' . $where_exactly . ' ' . $location['name'] . ' to ' . $action . ', but couldn\'t find anything.</span>');
            }

            if(array_search($location['idnum'], $location_list) === false)
                $location_list[] = $location['idnum'];
        }

        $mypet['actions_since_last_level']++;

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);
    }
    else
    {
        train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        // bias results, just for fun/to mess with people
        if(mt_rand(1, 4) == 1)
            $a = $mypet['idnum'] % 3 + 1;
        else if(mt_rand(1, 5) == 1)
            $a = $myuser['idnum'] % 3 + 1;
        else
            $a = mt_rand(1, 3);

        switch($a)
        {
            case 1:
                record_pet_stat($mypet['idnum'], 'Got Lost in the Wilderness', 1);
                add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . " went out to $action, but got lost on the way.</span>");
                break;

            case 2:
                record_pet_stat($mypet['idnum'], 'Couldn\'t Find Anything in the Wilderness', 1);
                add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . " went out to $action, but couldn\'t find a good spot.</span>");
                break;

            case 3:
                record_pet_stat($mypet['idnum'], 'Got Distracted in the Wilderness', 1);
                add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $act . '_failure', '<span class="failure">' . $mypet['petname'] . " went out to $action, but got distracted.</span>");
                break;
        }
    }

    return true;
}

function award_badges_by_quarry(&$pet, &$opponent)
{
    if($opponent['type'] == 'demon')
        set_pet_badge($pet, 'demonslayer');
    else if($opponent['type'] == 'dragon')
        set_pet_badge($pet, 'dragonslayer');
    else if($opponent['type'] == 'alien')
        set_pet_badge($pet, 'paranormal');
    else if($opponent['type'] == 'fish' && $opponent['level'] >= 15)
        set_pet_badge($pet, 'diver');
}

function fight_aliens(&$mypet, &$myuser, $hour)
{
    global $MATERIALS_LIST;

    $thirds = 2 / 3;
    // 1 + .75 + .66 = 2.41
    $kill_dice = floor($mypet['skill_bra'] + $mypet['skill_str'] * .75 + $mypet['skill_sta'] * $thirds);
    if($kill_dice < 1) $kill_dice = 1;

    $sneak_dice = floor($mypet['skill_stealth'] + $mypet['skill_dex'] * .75 + $mypet['skill_per'] * $thirds);
    if($sneak_dice < 1) $sneak_dice = 1;

    $disable_dice = floor($mypet['skill_eng'] + $mypet['skill_int'] * .75 + $mypet['skill_wit'] * $thirds);
    if($disable_dice < 1) $disable_dice = 1;

    $items = array('Candy Corn', 'Chocolate Twists', 'Crispy Crunchy Chocolate Chew', 'Kompeito', 'Red Lollipop', 'Green Taffy', 'Coconut Milk', 'Paper Hat', 'Fangs', 'Alien Taser', 'Alien Taser', 'Alien Taser');
    $itemname = $items[array_rand($items)];

    $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

    if($kill_dice >= $sneak_dice && $kill_dice >= $disable_dice && $kill_dice > 0)
    {
        record_pet_stat($mypet['idnum'], 'Defeated a Crop Circle Alien', 1);

        $esteem_gain = mt_rand(1, 4);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'aliens_defeat', '<span class="success">' . $mypet['petname'] . ' engaged in mortal combat with a Crop Circle Alien and won, claiming its ' . $itemname . '.</span>', array('esteem' => $esteem_gain));

        add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $itemname, $mypet['petname'] . ' fought a Crop Circle Alien, won, and claimed this item.', 'home');
        $MATERIALS_LIST[$itemname]++;

        train_pet($mypet, 'bra', ceil($kill_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'str', ceil($kill_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'sta', ceil($kill_dice / 4) + $bonus_exp, $hour);
    }
    else if($sneak_dice >= $kill_dice && $sneak_dice >= $disable_dice && $sneak_dice > 0)
    {
        record_pet_stat($mypet['idnum'], 'Pickpocketed a Crop Circle Alien', 1);

        $esteem_gain = mt_rand(1, 4);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'aliens_steal', '<span class="success">' . $mypet['petname'] . ' snuck up behind a Crop Circle Alien, and stole its ' . $itemname . '.</span>', array('esteem' => $esteem_gain));

        add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $itemname, $mypet['petname'] . ' snuck up behind a Crop Circle Alien and stole this item.', 'home');
        $MATERIALS_LIST[$itemname]++;

        train_pet($mypet, 'stealth', ceil($sneak_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'dex', ceil($sneak_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'per', ceil($sneak_dice / 4) + $bonus_exp, $hour);
    }
    else if($disable_dice >= $kill_dice && $disable_dice >= $sneak_dice && $disable_dice > 0)
    {
        record_pet_stat($mypet['idnum'], 'Sabotaged a Crop Circle Alien', 1);

        $esteem_gain = mt_rand(1, 4);
        $esteem_gain = gain_esteem($mypet, $esteem_gain);

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'aliens_sabotage', '<span class="success">' . $mypet['petname'] . ' sabotaged a Crop Circle Alien\'s weapon, causing it to backfire, then took its ' . $itemname . '.</span>', array('esteem' => $esteem_gain));

        add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $itemname, $mypet['petname'] . ' sabotaged a Crop Circle Alien\'s weapon, and claimed this item.', 'home');
        $MATERIALS_LIST[$itemname]++;

        train_pet($mypet, 'eng', ceil($disable_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'int', ceil($disable_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'wit', ceil($disable_dice / 4) + $bonus_exp, $hour);
    }
    else
    {
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'aliens_failure', '<span class="failure">' . $mypet['petname'] . ' wanted to fight Crop Circle Aliens, but couldn\'t come up with a good plan.</span>');
        return false;
    }

    return true;
}

function equipment_specific_bonus($tool, $target)
{
    $bonus = 0;

    if($target['is_vampire'] == 'yes' && $tool['equip_vampire_slayer'] == 'yes')
        $bonus += 2;

    if($target['is_werecreature'] == 'yes' && $tool['equip_were_killer'] == 'yes')
        $bonus += 2;

    if($target['is_berries'] == 'yes' && $tool['equip_berry_craft'] == 'yes')
        $bonus += 2;

    if($target['is_flying'] == 'yes' && $tool['equip_flight'] == 'yes')
        $bonus += 2;

    if($target['is_burny'] && $tool['equip_fire_immunity'] == 'yes')
        $bonus += 2;

    if($target['is_sensitive_to_cold'] == 'yes' && $tool['equip_chill_touch'] == 'yes')
        $bonus += 2;

    if($target['is_in_space'] == 'yes' && $tool['equip_pressurized'] == 'yes')
        $bonus += 2;

    if($target['is_deep_sea'] == 'yes' && $tool['equip_pressurized'] == 'yes')
        $bonus += 2;

    return $bonus;
}

function try_adventuring(&$mypet, &$myuser, $hour, &$monster_list)
{
    global $MATERIALS_LIST, $PSYPETS_BIRTHDAY, $EASTER;
    global $now_day, $now_month, $simulated_time;

    $dice = adventuring_dice($mypet);

    $difficulty = $dice;

    if(mt_rand(1, 3) == 1)
    {
        $difficulty++;
        $more_difficult = true;
    }
    else
        $more_difficult = false;

    $opponent = false;

    while($opponent === false)
    {
        $opponent = fetch_single('
      (SELECT *
      FROM `monster_monsters` WHERE
        level<=' . $difficulty . '
        AND min_month<=' . $now_month . '
        AND ' . $now_month . '<=max_month
        AND ' . $mypet['skill_stealth'] . '>=min_stealth
        AND ' . $mypet['skill_sta'] . '>=min_stamina
        AND ' . $mypet['athletics'] . '>=min_athletics
        AND ' . $mypet['wit'] . '>=min_wits
        AND (needs_key=0 OR needs_key=' . (int)$mypet['key']['key_id'] . ')
      ORDER BY RAND()
      LIMIT 5)
      ORDER BY level DESC
      LIMIT 1
    ');

        // in case we have to loop around
        $difficulty++;
    }
    $difficulty--;

    $monsterdifficulty = $opponent['level'];

    if($opponent['lycanthrope'] != '' && moon_phase($simulated_time) == 'full moon')
    {
        $monster_name = $opponent['lycanthrope'];
        $opponent['is_werecreature'] = 'yes';

        if(mt_rand(1, 100) == 1 && $mypet['zombie'] == 'no')
            $mypet['lycanthrope'] = 'yes';
    }
    else
        $monster_name = $opponent['name'];

    if($now_month == 4 && $now_day == 4) // 4/4 is act like a t-rex day
        $monster_extra = ' acting like a t-rex';
    else
        $monster_extra = '';

    $special_actions = array();

    if($mypet['special_firebreathing'] == 'yes' && mt_rand(1, 2) == 1)
    {
        $special_actions[] = '<strong>breathing fire</strong>';
        $dice += 2;
    }

    if($opponent['needs_key'] > 0)
    {
        delete_inventory_byid($mypet['keyid']);
        $mypet['keyid'] = 0;
        unset($mypet['key']);
        unset($mypet['realkey']);
    }

    $dice += equipment_specific_bonus($mypet['tool'], $opponent);

    $success_dice = successes($dice);

    if($success_dice > successes($monsterdifficulty))
    {
        $ran_away = (successes($monsterdifficulty) > successes($dice + 4));

        if($ran_away)
        {
            $adjective = 'chased off';
            $got_stuff = ', who dropped';
            $multiplier = 0.75;
        }
        else
        {
            award_badges_by_quarry($mypet, $opponent);

            $adjective = 'defeated';
            $got_stuff = ' and claimed';
            $multiplier = 1;

            if($monster_name == 'Kundrav' || $monster_name == 'Leviathan')
            {
                record_pet_stat($mypet['idnum'], 'Defeated Mighty ' . $monster_name, 1);

                $mypet['ascend'] = 'yes';
                $mypet['ascend_adventurer'] = 'yes';
                set_pet_badge($mypet, 'masteradventure');
            }
        }

        if(count($special_actions) > 0)
            $adjective = '- ' . implode(', ', $special_actions) . ' - ' . $adjective;

        if($dice - 5 < $monsterdifficulty)
        {
            $safety_gain = mt_rand(0, 3);
            $esteem_gain = mt_rand(1, 4);

            $safety_gain = gain_safety($mypet, $safety_gain);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else if($dice - 10 < $monsterdifficulty)
        {
            $safety_gain = mt_rand(0, mt_rand(1, 2));
            $esteem_gain = mt_rand(1, 2);

            $safety_gain = gain_safety($mypet, $safety_gain);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else
        {
            $esteem_gain = 0;
            $safety_gain = 0;
        }

        $prizes = array();
        $treasures = take_apart(',', $opponent['prizes']);

        if($now_month == 10 && $now_day == 11)
        {
            $treasures[] = '20|Rainbow Cake';
        }
        else if($PSYPETS_BIRTHDAY)
        {
            $treasures[] = '10|Red Party Hat';
            $treasures[] = '10|Large Blue Balloon';
            $treasures[] = '10|Large Yellow Balloon';
            $treasures[] = '10|Slice of Birthday Cake';
        }
        else if($EASTER > 0)
        {
            $treasures[] = $EASTER . '|Plastic Egg';
        }

        foreach($treasures as $drop)
        {
            $rate = explode('|', $drop);
            if(mt_rand(1, 1000) <= ceil((int)$rate[0] * $multiplier))
                $prizes[] = $rate[1];
        }

        foreach($prizes as $prize)
        {
            if(substr($prize, -7) == ' moneys')
            {
                $money = (int)substr($prize, 0, strlen($prize) - 7);
                give_money($myuser, $money, $mypet['petname'] . ' defeated ' . $monster_name . '.');
                $myuser['money'] += $money;

                record_pet_stat($mypet['idnum'], 'Money Earned Adventuring', $money);
            }
            else
            {
                add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $prize, $mypet['petname'] . ' ' . $adjective . ' ' . $monster_name . $monster_extra . $got_stuff . ' this treasure.', 'home');
                $MATERIALS_LIST[$prize]++;
            }
        }

        if(count($prizes) > 0)
        {
            $prize_desc = list_nice($prizes);

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'adventure_success', '<span class="success">' . $mypet['petname'] . ' ' . $adjective . ' ' . $monster_name . $monster_extra . ' and claimed its ' . $prize_desc . '.</span>', array('safety' => $safety_gain, 'esteem' => $esteem_gain));
        }
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'adventure_success', '<span class="progress">' . $mypet['petname'] . ' ' . $adjective . ' ' . $monster_name . $monster_extra . ', but didn\'t find any treasure.</span>', array('safety' => $safety_gain, 'esteem' => $esteem_gain));

        if(array_search($opponent['idnum'], $monster_list) === false)
            $monster_list[] = $opponent['idnum'];

        if($mypet['AVALANCHE'] === true)
        {
            global $AWARD_BADGE;
            $AWARD_BADGE['AVALANCHE'] = true;
        }

        $mypet['actions_since_last_level']++;

        $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

        train_pet($mypet, 'bra', ceil($success_dice / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'str', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'athletics', ceil($success_dice / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'sta', ceil($success_dice / 4) + $bonus_exp, $hour);

        if($opponent['min_athletics'] > 0)
            train_pet($mypet, 'athletics', $opponent['min_athletics'] * 2 + $bonus_exp);

        if($opponent['min_stealth'] > 0)
            train_pet($mypet, 'stealth', $opponent['min_stealth'] * 2 + $bonus_exp);

        if($opponent['min_wits'] > 0)
            train_pet($mypet, 'wit', $opponent['min_wits'] * 2 + $bonus_exp);

        if($opponent['min_stamina'] > 0)
            train_pet($mypet, 'wit', $opponent['min_stamina'] * 2 + $bonus_exp);

        return true;
    }
    // got enough successes to beat the monster
    else
    {
        train_pet($mypet, 'bra', $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        record_pet_stat($mypet['idnum'], 'Was Forced to Retreat from Battle', 1);

        if(mt_rand(1, 600) == 1)
        {
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'adventure_failure', '<span class="failure">' . $mypet['petname'] . ' did battle with ' . $monster_name . $monster_extra . ', but suffered a wound and was forced to retreat!</span>');
            $mypet['nasty_wound'] = max(168, $mypet['nasty_wound'] + mt_rand(12, mt_rand(72, 96)));
            record_pet_stat($mypet['idnum'], 'Suffered a Wound', 1);
        }
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'adventure_failure', '<span class="failure">' . $mypet['petname'] . ' did battle with ' . $monster_name . $monster_extra . ', but was forced to retreat.</span>');

        return true;
    }

    return false;
} // adventuring

function try_hunting($type, &$mypet, &$myuser, $hour, &$prey_list)
{
    global $MATERIALS_LIST, $PSYPETS_BIRTHDAY, $EASTER;
    global $now_day, $now_month;

    $training = array();

    if($type == 'hunt')
    {
        $dice = hunting_dice($mypet);
        $past_tense = 'hunted';

        $main_training = 'sur';
        $training[] = 'athletics';
        $training[] = 'str';
        $training[] = 'per';
        $training[] = 'stealth';
    }
    else if($type == 'fish')
    {
        $dice = fishing_dice($mypet);
        $past_tense = 'fished';

        $main_training = 'fishing';
        $training[] = 'dex';
        $training[] = 'per';
        $training[] = 'stealth';
    }

    if($dice < 1)
        $dice = 1;

    $difficulty = $dice;

    if(rand(1, 3) == 1)
    {
        $difficulty++;
        $more_difficult = true;
    }
    else
        $more_difficult = false;

    $opponent = false;

    while($opponent === false)
    {
        $opponent = fetch_single('
      (SELECT *
      FROM monster_prey
      WHERE
        level<=' . $difficulty . '
        AND activity=\'' . $type . '\'
        AND min_month<=' . $now_month . '
        AND ' . $now_month . '<=max_month
        AND ' . $mypet['stealth'] . '>=min_stealth
        AND ' . $mypet['sta'] . '>=min_stamina
        AND ' . $mypet['athletics'] . '>=min_athletics
        AND ' . $mypet['wit'] . '>=min_wits
        AND (needs_key=0 OR needs_key=' . (int)$mypet['key']['key_id'] . ')
      ORDER BY RAND()
      LIMIT 7)
      ORDER BY level DESC
      LIMIT 1
    ');

        // in case we have to loop around
        $difficulty++;
    }
    $difficulty--;

    $monsterdifficulty = $opponent['level'];

    if($mypet['special_chameleon'] == 'yes' && mt_rand(1, 3) == 1)
    {
        $special = ' - <strong>camouflaged</strong> -';
        $dice++;
    }
    else
        $special = '';

    if($opponent['needs_key'] > 0)
    {
        delete_inventory_byid($mypet['keyid']);
        $mypet['keyid'] = 0;
        unset($mypet['key']);
        unset($mypet['realkey']);
    }

    $dice += equipment_specific_bonus($mypet['tool'], $opponent);

    $success_dice = successes($dice);

    if($success_dice > successes($monsterdifficulty))
    {
        award_badges_by_quarry($mypet, $opponent);

        $prizes = array();
        $treasures = take_apart(',', $opponent['prizes']);

        if($now_month == 10 && $now_day == 11)
        {
            $treasures[] = '20|Rainbow Cake';
        }
        else if($PSYPETS_BIRTHDAY)
        {
            $treasures[] = '10|Red Party Hat';
            $treasures[] = '10|Large Blue Balloon';
            $treasures[] = '10|Large Yellow Balloon';
            $treasures[] = '10|Slice of Birthday Cake';
        }
        else if($EASTER > 0)
        {
            $treasures[] = $EASTER . '|Plastic Egg';
        }

        if($now_month == 11 && $type == 'hunt')
        {
            if($opponent['name'] == 'a Churkey')
            {
                $treasures[] = '10|Cornucopia Blueprint';
                $treasures[] = '1|Churkey Egg';
            }
        }

        foreach($treasures as $drop)
        {
            $rate = explode('|', $drop);

            if(rand(1, 1000) <= (int)$rate[0])
                $prizes[] = $rate[1];
        }

        foreach($prizes as $prize)
        {
            $extra_descript = '';

            if($prize == 'White Leather' && $type == 'hunt')
            {
                if($mypet['ascend_hunter'] != 'yes')
                {
                    $mypet['ascend'] = 'yes';
                    $mypet['ascend_hunter'] = 'yes';
                    set_pet_badge($mypet, 'masterhunt');
                    $extra_descript .= ', earning the title Master Hunter';
                }
            }
            else if($now_month == 1 && $now_day == 26)
            {
                if($prize == 'Steak' || $prize == 'Fish' || $prize == 'Chicken')
                {
                    $prize .= ' BBQ';
                    $extra_descript = ' for Australia Day';
                }
            }

            add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $prize, $mypet['petname'] . ' ' . $past_tense . ' ' . $opponent['name'] . ' and retrieved this item' . $extra_descript . '.', 'home');
            $MATERIALS_LIST[$prize]++;
        }

        if($opponent['name'] == 'a White Whale' && $mypet['ascend_fisher'] != 'yes')
        {
            record_pet_stat($mypet['idnum'], 'Successfully Hunted a White Whale', 1);

            $mypet['ascend'] = 'yes';
            $mypet['ascend_fisher'] = 'yes';
            set_pet_badge($mypet, 'masterfish');
            $extra_descript .= ', earning the title Master Fisher';
        }

        if($dice - 5 < $monsterdifficulty)
        {
            if($type != 'fish')
            {
                $safety_gain = mt_rand(0, 2);
                $safety_gain = gain_safety($mypet, $safety_gain);
            }
            else
                $safety_gain = 0;

            $esteem_gain = mt_rand(1, 4);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else if($dice - 10 < $monsterdifficulty)
        {
            if($type != 'fish')
            {
                $safety_gain = mt_rand(0, 1);
                $safety_gain = gain_safety($mypet, $safety_gain);
            }
            else
                $safety_gain = 0;

            $esteem_gain = mt_rand(1, 2);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else
        {
            $safety_gain = 0;
            $esteem_gain = 0;
        }

        if(count($prizes) > 0)
        {
            $prize_desc = list_nice($prizes);

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $type . '_success', '<font class="success">' . $mypet['petname'] . $special . ' ' . $past_tense . ' ' . $opponent['name'] . " for its $prize_desc.</font>", array('safety' => $safety_gain, 'esteem' => $esteem_gain));
        }
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $type . '_success', '<span class="progress">' . $mypet['petname'] . $special . ' ' . $past_tense . ' ' . $opponent['name'] . ', but couldn\'t recover anything.</span>', array('safety' => $safety_gain, 'esteem' => $esteem_gain));

        if(array_search($opponent['idnum'], $prey_list) === false)
            $prey_list[] = $opponent['idnum'];

        $mypet['actions_since_last_level']++;

        $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

        train_pet($mypet, $main_training, ceil($success_dice / 2) + $bonus_exp, $hour);
        foreach($training as $this_stat)
            train_pet($mypet, $this_stat, ceil($success_dice / 4) + $bonus_exp, $hour);

        if($opponent['min_athletics'] > 0)
            train_pet($mypet, 'athletics', $opponent['min_athletics'] * 2 + $bonus_exp);

        if($opponent['min_stealth'] > 0)
            train_pet($mypet, 'stealth', $opponent['min_stealth'] * 2 + $bonus_exp);

        if($opponent['min_wits'] > 0)
            train_pet($mypet, 'wit', $opponent['min_wits'] * 2 + $bonus_exp);

        if($opponent['min_stamina'] > 0)
            train_pet($mypet, 'wit', $opponent['min_stamina'] * 2 + $bonus_exp);

        return true;
    }
    else
    {
        train_pet($mypet, $main_training, $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        if(mt_rand(1, 700) == 1)
        {
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $type . '_failure', '<span class="failure">' . $mypet['petname'] . $special . ' stalked ' . $opponent['name'] . ', but suffered a wound and failed to catch it!</span>');
            $mypet['nasty_wound'] = max(168, $mypet['nasty_wound'] + mt_rand(12, 72));
            record_pet_stat($mypet['idnum'], 'Suffered a Wound', 1);
        }
        else
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', $type . '_failure', '<span class="failure">' . $mypet['petname'] . $special . ' stalked ' . $opponent['name'] . ', but failed to catch it.</span>');

        return true;
    }

    return false;
} // hunting

function online_activity(&$mypet, &$myuser, $hour)
{
    global $simulated_time;

    $my_skill = vhagst_dice($mypet);

    // only fight pets who were online within the last 12 hours
    $enemy = fetch_single('
		SELECT petid,skill,RAND() AS sortkey
		FROM psypets_wired
		WHERE
			lastplay>' . ($simulated_time - 43200) . ' AND
			petid!=' . $mypet['idnum'] . '
		ORDER BY sortkey
		LIMIT 1
	');

    $command = 'INSERT INTO psypets_wired (petid, skill, lastplay) VALUES ' .
        '(' . $mypet['idnum'] . ', ' . $my_skill . ', ' . $simulated_time . ') ' .
        'ON DUPLICATE KEY UPDATE skill=' . $my_skill . ',lastplay=' . $simulated_time;
    $GLOBALS['database']->FetchNone($command, 'updating pet record');

    if($enemy === false)
    {
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'online_unable', '<font class="failure">' . $mypet['petname'] . ' wanted to play Virtual Hide-and-go-seek Tag, but no one else is playing.</font>');
        return false;
    }

    $enemy_score = successes($enemy['skill']);
    $my_score = successes($my_skill);
    $opponent = get_pet_byid($enemy['petid'], 'petname,gender');

    if($my_score >= $enemy_score)
    {
        $pixels = (int)max(1, log($enemy['skill'], 2));
        $max_exp = $pixels * 4 + mt_rand(-2, 2);

        if($pixels == 1)
            $pixel_note = '1 Pixel';
        else
            $pixel_note = $pixels . ' Pixels';

        $GLOBALS['database']->FetchNone('
			UPDATE monster_users
			SET pixels=pixels+' . $pixels . '
			WHERE idnum=' . $myuser['idnum'] . '
			LIMIT 1
		');

        record_pet_stat($mypet['idnum'], 'Pixels Earned', $pixels);

        if($my_skill - 5 < $enemy['skill'])
        {
            $esteem_gain = mt_rand(1, 4);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else if($my_skill - 10 < $enemy['skill'])
        {
            $esteem_gain = mt_rand(1, 2);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else
            $esteem_gain = 0;

        if($enemy_score >= 25)
        {
            $mypet['ascend'] = 'yes';
            $mypet['ascend_vhagst'] = 'yes';
            set_pet_badge($mypet, 'mastervhagst');
        }

        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'online_success', '<font class="success">' . $mypet['petname'] . ' stalked <a href="/petprofile.php?petid=' . $enemy['petid'] . '">' . $opponent['petname'] . '</a> in Virtual Hide-and-go-Seek Tag, and caught ' . t_pronoun($opponent['gender']) . ', earning ' . $pixel_note . '!</font>', array('esteem' => $esteem_gain));

        $mypet['actions_since_last_level']++;

        $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

        train_pet($mypet, 'stealth', ceil($my_score / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'wit', ceil($my_score / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'dex', ceil($my_score / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'int', ceil($my_score / 4) + $bonus_exp, $hour);

        return true;
    }
    else
    {
        train_pet($mypet, 'stealth', $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'online_failure', '<font class="failure">' . $mypet['petname'] . ' stalked <a href="/petprofile.php?petid=' . $enemy['petid'] . '">' . $opponent['petname'] . '</a> in Virtual Hide-and-go-Seek Tag, but failed to catch ' . t_pronoun($opponent['gender']) . ', earning 1 Pixel.</font>');

        $GLOBALS['database']->FetchNone('
			UPDATE monster_users
			SET pixels=pixels+1
			WHERE idnum=' . $myuser['idnum'] . '
			LIMIT 1
		');

        record_pet_stat($mypet['idnum'], 'Pixels Earned', 1);

        return true;
    }

    return false;
}

function go_begging(&$mypet, &$myuser, $hour)
{
    $descriptions = array(
        'rummages through the garbage and finds',
        'begs for food and receives',
    );

    $foods = array(
        'a half-eaten hamburger',
        'a jar of peanut butter',
        'some stale bread',
        'a banana peel',
        'a discarded box of cake mix',
    );

    $descript = $descriptions[array_rand($descriptions)] . ' ' . $foods[array_rand($foods)];

    $esteem_loss = mt_rand(1, 2);
    lose_stat($mypet, 'esteem', $esteem_loss);

    $food_gain = mt_rand(4, 8);
    $food_gain = gain_food($mypet, $food_gain);
    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'beg', '<font class="eating">' . $mypet['petname'] . ' ' . $descript . '.</font>', array('food' => $food_gain, 'esteem' => -$esteem_loss));

    return false;
}

function eat_something(&$mypet, $hour)
{
    global $MATERIALS_LIST, $FLAVORS;

    $this_user = get_user_byuser($mypet['user'], 'idnum');

    $max_food = max_food($mypet);

    if(count($MATERIALS_LIST) > 0)
    {
        $item_list = ashuffle($MATERIALS_LIST);

        $ate = array();
        $food_gain = 0;
        $esteem_gain = 0;

        $extra_note = '.';

        $effective_conscientious = $mypet['skill_conscientious'] + mt_rand(-1, 1);
        if($mypet['esteem'] < 5) $effective_conscientious--;
        if($mypet['love']   < 5) $effective_conscientious--;
        if($mypet['safety'] < 5) $effective_conscientious--;

        // if we're starving, we'll eat anything!
        if($mypet['food'] <= 0)
            $effective_conscientious = 0;
        else if($mypet['food'] < 5)
            $effective_conscientious = floor($effective_conscientious / 2);

        $favorite_flavor = $FLAVORS[$mypet['likes_flavor']];

        foreach($item_list as $itemname=>$quantity)
        {
            $item_item = get_item_byname($itemname);

            $made_of = take_apart(',', $item_item['recycle_for']);

            if(
                $item_item['ediblefood'] > 0 &&
                ($food_gain == 0 || $item_item['ediblefood'] + $mypet['food'] <= $max_food + 4) &&
                (
                    $item_item['max_conscientiousness'] == 0 ||                        // if there is no max conscientious (0 max conscientious)
                    $effective_conscientious <= $item_item['max_conscientiousness'] || // if the pet's (effective) conscientious does not exceed the item's max conscientious
                    $favorite_flavor == $itemname ||                                   // if the item is the pet's favorite flavor
                    in_array($favorite_flavor, $made_of)                               // if the item contains the pet's favorite flavor
                )
            )
            {
                delete_inventory_fromhome($mypet['user'], $itemname, 1);

                $MATERIALS_LIST[$itemname]--;
                if($MATERIALS_LIST[$itemname] == 0)
                    unset($MATERIALS_LIST[$itemname]);

                $ate[] = $itemname;

                gain_caffeine($mypet, $item_item['ediblecaffeine']);
                $food_gain += gain_food($mypet, $item_item['ediblefood']);
                $esteem_gain += gain_esteem($mypet, $item_item['edibleesteem']);

                if($itemname == 'Eggplant' && mt_rand(1, 1000) == 1 && $mypet['eggplant'] == 'no')
                {
                    $mypet['eggplant'] = 'yes';
                    $extra_note = ', and contracted the Eggplant Curse!';
                }

                if($mypet['food'] >= $max_food || count($ate) > 3)
                    break;
            }
        }

        if(count($ate) > 0)
        {
            add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'eat', '<span class="eating">' . $mypet['petname'] . ' ate ' . implode(', ', $ate) . $extra_note . '</span>', array('food' => $food_gain, 'esteem' => $esteem_gain));

            return true;
        }
    }

    add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'eat_unable', '<span class="obstacle">' . $mypet['petname'] . ' was hungry, but couldn\'t find anything in the house.</span>');

    return false;
}

function safety_something(&$mypet, $hour)
{
    global $MATERIALS_LIST;

    $this_user = get_user_byuser($mypet['user'], 'idnum');

    if(count($MATERIALS_LIST) > 0)
    {
        $item_list = ashuffle($MATERIALS_LIST);

        $ate = array();
        $food_gain = 0;
        $safety_gain = 0;
        $love_gain = 0;
        $esteem_gain = 0;

        foreach($item_list as $itemname=>$quantity)
        {
            $item_item = get_item_byname($itemname);

            if($item_item['hourlysafety'] > 0)
            {
                $ate[] = $itemname;

                $food_gain += gain_food($mypet, $item_item['hourlyfood']);
                $safety_gain += gain_safety($mypet, $item_item['hourlysafety'] * 2);
                $love_gain += gain_love($mypet, $item_item['hourlylove']);
                $esteem_gain += gain_esteem($mypet, $item_item['hourlyesteem']);

                if($item_item['hourlystat'] != '')
                {
                    $mypet['actions_since_last_level']++;
                    train_pet($mypet, $item_item['hourlystat'], 2, $hour);
                }

                if($mypet['safety'] >= max_safety($mypet) || count($ate) > 3)
                    break;
            }
        }

        if(count($ate) > 0)
        {
            add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'safety', '<span class="eating">' . $mypet['petname'] . ' was calmed by ' . implode(', ', $ate) . '.</span>', array('food' => $food_gain, 'safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

            return true;
        }
    }

    add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'safety_unable', '<span class="obstacle">' . $mypet['petname'] . ' doesn\'t feel safe, but couldn\'t find anything in the house to be calmed by.</span>');

    return false;
}

function love_something(&$mypet, $hour, $open_aquarium = false)
{
    global $MATERIALS_LIST;

    $this_user = get_user_byuser($mypet['user'], 'idnum');

    if($open_aquarium)
    {
        $safety_gain = gain_love($mypet, 1);
        $love_gain = gain_love($mypet, 2);
        add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'love', '<span class="eating">' . $mypet['petname'] . ' was comforted by entertainers from The Merkingdom.</span>', array('safety' => $safety_gain, 'love' => $love_gain));
    }

    if(count($MATERIALS_LIST) > 0)
    {
        $item_list = ashuffle($MATERIALS_LIST);

        $ate = array();
        $food_gain = 0;
        $safety_gain = 0;
        $love_gain = 0;
        $esteem_gain = 0;

        foreach($item_list as $itemname=>$quantity)
        {
            $item_item = get_item_byname($itemname);

            if($item_item['hourlylove'] > 0)
            {
                $ate[] = $itemname;

                $food_gain += gain_food($mypet, $item_item['hourlyfood']);
                $safety_gain += gain_safety($mypet, $item_item['hourlysafety']);
                $love_gain += gain_love($mypet, $item_item['hourlylove'] * 2);
                $esteem_gain += gain_esteem($mypet, $item_item['hourlyesteem']);

                if($item_item['hourlystat'] != '')
                {
                    $mypet['actions_since_last_level']++;
                    train_pet($mypet, $item_item['hourlystat'], 2, $hour);
                }

                if($mypet['love'] >= max_love($mypet) || count($ate) > 3)
                    break;
            }
        }

        if(count($ate) > 0)
        {
            add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'love', '<span class="eating">' . $mypet['petname'] . ' was comforted by ' . implode(', ', $ate) . '.</span>', array('food' => $food_gain, 'safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

            return true;
        }
    }

    if($open_aquarium)
        return true;
    else
    {
        add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'love_unable', '<span class="obstacle">' . $mypet['petname'] . ' doesn\'t feel loved, but couldn\'t find anything in the house to be comforted by.</span>');
        return false;
    }
}

function esteem_something(&$mypet, $hour, $open_aquarium = false)
{
    global $MATERIALS_LIST;

    $this_user = get_user_byuser($mypet['user'], 'idnum');

    if($open_aquarium)
    {
        $safety_gain = gain_love($mypet, 1);
        $love_gain = gain_love($mypet, 1);
        $esteem_gain = gain_love($mypet, 1);
        add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'love', '<span class="eating">' . $mypet['petname'] . ' was reassured by entertainers from The Merkingdom.</span>', array('safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));
    }

    if(count($MATERIALS_LIST) > 0)
    {
        $item_list = ashuffle($MATERIALS_LIST);

        $ate = array();
        $food_gain = 0;
        $safety_gain = 0;
        $love_gain = 0;
        $esteem_gain = 0;

        foreach($item_list as $itemname=>$quantity)
        {
            $item_item = get_item_byname($itemname);

            if($item_item['hourlyesteem'] > 0)
            {
                $ate[] = $itemname;

                $food_gain += gain_food($mypet, $item_item['hourlyfood']);
                $safety_gain += gain_safety($mypet, $item_item['hourlysafety']);
                $love_gain += gain_love($mypet, $item_item['hourlylove']);
                $esteem_gain += gain_esteem($mypet, $item_item['hourlyesteem'] * 2);

                if($item_item['hourlystat'] != '')
                {
                    $mypet['actions_since_last_level']++;
                    train_pet($mypet, $item_item['hourlystat'], 2, $hour);
                }

                if($mypet['esteem'] >= max_esteem($mypet) || count($ate) > 3)
                    break;
            }
        }

        if(count($ate) > 0)
        {
            add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'esteem', '<span class="eating">' . $mypet['petname'] . ' was reassured by ' . implode(', ', $ate) . '.</span>', array('food' => $food_gain, 'safety' => $safety_gain, 'love' => $love_gain, 'esteem' => $esteem_gain));

            return true;
        }
    }

    if($open_aquarium)
        return true;
    else
    {
        add_logged_event_cached($this_user['idnum'], $mypet['idnum'], $hour, 'hourly', 'esteem_unable', '<span class="obstacle">' . $mypet['petname'] . ' doesn\'t feel esteemed, but couldn\'t find anything in the house to be reassured by.</span>');
        return false;
    }
}

function works_on_project($conscientious, $independent, $priority)
{
    $chance = 30 + $conscientious * 2 + (10 - $independent);

    if($priority == 'yes')
        $chance += 25;

    return(mt_rand(1, 100) <= $chance);
}

function go_hang_out(&$mypet, &$myuser, $hour)
{
    $friends = fetch_multiple('
		SELECT
			b.idnum,
			a.rejected,
			a.forbidden,
      a.hangouts_to_ignore,
			a.intimacy,a.passion,a.commitment,
			(a.intimacy+a.passion+a.commitment)*RAND() AS total_feeling,
			b.user,
			b.petname,
      b.graphic,b.bloodtype,
			b.dead,b.changed,b.zombie,
			b.motherid,b.fatherid,b.gender,b.prolific,
			b.safety,b.love,b.esteem,
      b.pregnant_asof,b.pregnant_by,
			b.extraverted,b.open,b.playful,b.conscientious
		FROM
			psypets_pet_relationships AS a
			LEFT JOIN monster_pets AS b ON a.friendid=b.idnum
		WHERE
			a.petid=' . $mypet['idnum'] . ' AND
			b.sleeping=\'no\'
		ORDER BY total_feeling DESC
	');

    if(count($friends) == 0)
    {
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out, but doesn\'t really know anyone.</span>');
        return false;
    }

    $hang_out_with = false;

    foreach($friends as $friend)
    {
        if($friend['hangouts_to_ignore'] > 0)
        {
            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET hangouts_to_ignore=hangouts_to_ignore-1 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            continue;
        }
        else if($friend['dead'] != 'no')
        {
            $pet_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';

            $effects = array(
                'safety' => -2,
                'love' => -2,
                'esteem' => -2,
            );

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out with ' . $pet_link . ', but ' . $pet_link . ' is dead :(</span>', $effects);

            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET intimacy=intimacy-1,passion=passion-1,commitment=commitment-1 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            return false; // doesn't count as an action
        }
        else if($friend['forbidden'] == 'yes')
        {
            $pet_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';

            $effects = array(
                'love' => -2,
                'esteem' => -1,
            );

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out with ' . $pet_link . ', but you\'ve forbidden it!</span>', $effects);

            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET intimacy=intimacy-1,passion=passion-1,commitment=commitment-1,hangouts_to_ignore=2 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            return false; // doesn't count as an action
        }
        else if($friend['rejected'] == 'yes')
        {
            $pet_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';

            $effects = array(
                'love' => -1,
                'esteem' => -2,
            );

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out with ' . $pet_link . ', but has been rejected :(</span>', $effects);

            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET intimacy=intimacy-1,passion=passion-1,commitment=commitment-1,hangouts_to_ignore=2 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            return false; // doesn't count as an action
        }
        else if($friend['zombie'] == 'yes')
        {
            $pet_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';

            $effects = array(
                'safety' => -2,
                'love' => -2,
                'esteem' => -2,
            );

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out with ' . $pet_link . ', but ' . $pet_link . ' is a zombie!?</span>', $effects);

            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET intimacy=intimacy-1,passion=passion-1,commitment=commitment-1,hangouts_to_ignore=2 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            return false; // doesn't count as an action
        }
        else if($friend['changed'] == 'yes')
        {
            $pet_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';

            $effects = array('safety' => -3);

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to hang out with ' . $pet_link . ', but ' . $pet_link . ' is a werecreature!</span>', $effects);

            $GLOBALS['database']->FetchNone('UPDATE psypets_pet_relationships SET intimacy=intimacy-1,passion=passion-1,commitment=commitment-1,hangouts_to_ignore=4 WHERE petid=' . $mypet['idnum'] . ' AND friendid=' . $friend['idnum'] . ' LIMIT 1');

            return true; // counts as an action!
        }
        else
        {
            $effects = array();
            $hang_out_with = $friend;
            break;
        }
    }

    if($hang_out_with === false)
    {
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout_unable', '<span class="obstacle">' . $mypet['petname'] . ' thought about hanging out with a friend, but couldn\'t think of anyone to hang out with.</span>', $effects);
        return false; // doesn't count as an action
    }

    do_friendly_hang_out($hour, $mypet, $hang_out_with);

    // make sure to save any changes to the other pet
    save_pet(
        $hang_out_with,
        array(
            'safety', 'love', 'esteem',
            'pregnant_asof', 'pregnant_by',
        )
    );

    // lose a random stat for all the friends we _didn't_ hang out with...

    $triangle = array('intimacy', 'passion', 'commitment');
    $stat = $triangle[array_rand($triangle)];

    $GLOBALS['database']->FetchNone('
		UPDATE psypets_pet_relationships
		SET
			' . $stat . '=' . $stat . '-1
		WHERE
			petid=' . $mypet['idnum'] . ' AND
			friendid!=' . $hang_out_with['idnum'] . ' AND
			' . $stat . '>0
		LIMIT ' . (count($friends) - 1) . '
	');

    $GLOBALS['database']->FetchNone('DELETE FROM psypets_pet_relationships WHERE petid=' . $mypet['idnum'] . ' AND (intimacy+passion+commitment)<5');

    return true;
}

function do_farming($userid, &$mypet, &$farm, $hour)
{
    $skill = gardening_dice($mypet);
    $amount = work_at_farm($farm, $skill);

    if($amount === false)
        add_logged_event_cached($userid, $mypet['idnum'], $hour, 'hourly', 'farming_unable', '<span class="obstacle">' . $mypet['petname'] . ' wanted to work at the Farm, but the Silo is full!</span>');
    else if($amount == 0)
    {
        train_pet($mypet, 'gathering', $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);

        if(mt_rand(1, 800) == 1)
        {
            add_logged_event_cached($userid, $mypet['idnum'], $hour, 'hourly', 'farming_failure', '<span class="failure">' . $mypet['petname'] . ' worked at The Farm, but suffered a wound and didn\'t get much done!</span>');
            $mypet['nasty_wound'] = max(168, $mypet['nasty_wound'] + mt_rand(12, 72));
            record_pet_stat($mypet['idnum'], 'Suffered a Wound', 1);
        }
        else
            add_logged_event_cached($userid, $mypet['idnum'], $hour, 'hourly', 'farming_failure', '<span class="failure">' . $mypet['petname'] . ' worked at The Farm, but didn\'t get much done.</span>');
    }
    else
    {
        $esteem_gain = successes((int)($amount * 2.5));
        $esteem_gain = gain_esteem($mypet, $esteem_gain);

        $mypet['actions_since_last_level']++;

        $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

        train_pet($mypet, 'gathering', ceil($amount / 2) + $bonus_exp, $hour);
        train_pet($mypet, 'int', ceil($amount / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'per', ceil($amount / 4) + $bonus_exp, $hour);
        train_pet($mypet, 'sta', ceil($amount / 4) + $bonus_exp, $hour);

        add_logged_event_cached(
            $userid,
            $mypet['idnum'],
            $hour,
            'hourly',
            'farming_success',
            '<span class="success">' . $mypet['petname'] . ' worked at The Farm and harvested ' . $amount . ' ' . $farm['field_crop'] . '.</span>',
            array('esteem' => $esteem_gain)
        );
    }
}

function go_rest_up(&$mypet, &$myuser, $hour)
{
    record_pet_stat($mypet['idnum'], 'Rested Due To a Wound', 1);

    add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'restup', $mypet['petname'] . ' rests up.', array());

    $mypet['nasty_wound'] -= 2;

    if($mypet['heal_quickly'])
        $mypet['nasty_wound'] = max(0, $mypet['nasty_wound'] - mt_rand(1, 4));
}

function play_game_room(&$mypet, &$myuser, $hour, $house_size, &$game_room, &$game_room_games)
{
    $difficulty = min(max(1, ceil(pet_level($mypet) / 2)), $game_room['money']);

    $game = fetch_single('
    SELECT *
    FROM `psypets_arcadegames` WHERE
      level<=' . $difficulty . '
    ORDER BY RAND()
    LIMIT 1
  ');

    $gamedifficulty = $game['level'];
    $gamename = $game['name'];

    $special_actions = array();

    charge_game_room($myuser['idnum'], $gamedifficulty);
    $game_room['money'] -= $gamedifficulty;

    if(!array_key_exists($game['idnum'], $game_room_games))
    {
        $game_room_games[$game['idnum']] = true;
        add_game_room_game($myuser['idnum'], $game['idnum']);
    }

    if($game['type'] == 'puzzle')
    {
        $skill = $mypet['int'] + $mypet['per'] + $mypet['wit'];
        $training = array('int', 'per', 'wit');
    }
    else if($game['type'] == 'music')
    {
        $skill = $mypet['per'] + $mypet['dex'] + $mypet['sta'] + ceil($mypet['music'] / 3);
        lose_stat($mypet, 'energy', 1);
        $training = array('per', 'dex', 'sta');
    }
    else if($game['type'] == 'fighter')
    {
        $skill = $mypet['dex'] + $mypet['int'] + $mypet['wit'];
        $training = array('dex', 'int', 'wit');
    }
    else
    {
        $skill = $mypet['per'] + $mypet['dex'] + $mypet['wit'];
        $training = array('per', 'dex', 'wit');
    }

    $skill = floor($skill * 4 / 3) + $mypet['knack_videogames'];

    if($mypet['merit_lucky'] == 'yes')
        $max_drop_chance = 950;
    else
        $max_drop_chance = 1000;

    $dice = equipment_specific_bonus($mypet['tool'], $game);

    $success_dice = successes($skill + $dice);

    if($success_dice > successes($gamedifficulty))
    {
        $prizes = array();
        $treasures = take_apart(',', $game['prizes']);

        foreach($treasures as $drop)
        {
            $rate = explode('|', $drop);
            if(mt_rand(1, $max_drop_chance) <= $rate[0])
                $prizes[] = $rate[1];
        }

        if($skill - 5 < $gamedifficulty && count($prizes) > 0)
        {
            $love_gain = mt_rand(1, 2);
            $esteem_gain = mt_rand(1, 4);

            $love_gain = gain_love($mypet, $love_gain);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else if($skill - 10 < $gamedifficulty && count($prizes) > 0)
        {
            $love_gain = 1;
            $esteem_gain = mt_rand(1, 2);

            $love_gain = gain_love($mypet, $love_gain);
            $esteem_gain = gain_esteem($mypet, $esteem_gain);
        }
        else
        {
            $love_gain = 1;
            $esteem_gain = 0;
        }

        if(count($prizes) > 0)
        {
            foreach($prizes as $prize)
                add_inventory_cached($myuser['user'], 'p:' . $mypet['idnum'], $prize, $mypet['petname'] . ' played ' . $gamename . ' and won this ticket.', 'home');

            $prize_desc = list_nice($prizes);

            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'game_room_success', '<span class="success">' . $mypet['petname'] . ' played ' . $gamename . ', winning ' . $prize_desc . '.</span>', array('love' => $love_gain, 'esteem' => $esteem_gain));

            $bonus_exp = ($mypet['inspired'] > 0 ? 1 : 0);

            foreach($training as $this_stat)
                train_pet($mypet, $this_stat, ceil($success_dice / 3) + $bonus_exp, $hour);
        }
        else
        {
            train_pet($mypet, $training[array_rand($training)], $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);
            add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'game_room_failure', '<span class="failure">' . $mypet['petname'] . ' played ' . $gamename . ', but didn\'t win any tickets.</span>', array('love' => $love_gain));
        }

        return true;
    }
    // got enough successes to beat the game
    else
    {
        train_pet($mypet, $training[array_rand($training)], $mypet['skill_golden_mushroom'] ? 2 : 1, $hour);
        add_logged_event_cached($myuser['idnum'], $mypet['idnum'], $hour, 'hourly', 'game_room_failure', '<span class="failure">' . $mypet['petname'] . ' played ' . $gamename . ', but couldn\'t figure it out.</span>', array('love' => 1));
        return true;
    }

    return false;
}

function deteriorate_zombie(&$zombie, &$owner, $hour)
{
    global $PET_SKILLS;

    $available_skills = array();
    $level = 0;

    foreach($PET_SKILLS as $skill)
    {
        if($zombie[$skill] > 0)
        {
            $available_skills[] = $skill;
            $level += $zombie[$skill];
        }
    }

    // zombie is dead if it only has one level left
    if($level == 1)
    {
        delete_pet($zombie);
        $zombie['dead_zombie'] = true;
        add_db_message($owner['idnum'], FLASH_MESSAGE_GENERAL_MESSAGE, $zombie['petname'] . ' has wasted away!');
    }
    else
    {
        $eroded_skill = $available_skills[array_rand($available_skills)];

        $zombie[$eroded_skill]--;

        add_logged_event_cached($owner['idnum'], $zombie['idnum'], $hour, 'hourly', false, '<span class="progress">' . $zombie['petname'] . ' is wasting away...</span>');
    }
}
