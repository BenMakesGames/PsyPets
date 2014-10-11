<?php
require_once 'commons/flavorlib.php';
require_once 'commons/checkpet.php';

function dream_description(&$pet)
{
    $topic_total = 0;

    $topic_total += 30 - $pet['playful'] * 2;
    $topics[] = array(30 - $pet['playful'] * 2, 'hourly activities');

    $topic_total += 50;
    $topics[] = array(50, 'random story');

    $topic_total += 20;
    $topics[] = array(20, 'scary house items');

    if(date('n') == 10 || date('n') == 9)
    {
        $topic_total += 30;
        $topics[] = array(30, 'aliens');
    }

    $a = mt_rand(1, $topic_total);

    foreach($topics as $topic)
    {
        if($a <= $topic[0])
        {
            $this_topic = $topic[1];
            break;
        }

        $a -= $topic[0];
    }

    switch($this_topic)
    {
        case 'hourly activities':
            $description = dream_description_hourly_activities($pet);

            if($description !== false)
                break;

        // if scary house items fails (no scary items in house), roll over to Random Story
        case 'scary house items':
            $description = dream_description_scary_house_items($pet);

            if($description !== false)
                break;

        case 'random story':
            $description = dream_description_random_story($pet);
            break;

        case 'aliens':
            $description = dream_description_aliens($pet);
            break;
    }

    return $description;
}

function dream_description_hourly_activities(&$pet)
{
    $targets = array('the most-amazing thing', 'something that would change the world', 'something that seemed to be from another world', 'something... important', 'a long-lost item');
    $target = $targets[array_rand($targets)];

    $dreams = array('had a dream', 'dreamed');
    $dreamed = $dreams[array_rand($dreams)];

    switch(mt_rand(1, 2))
    {
        case 1:
            $actions = array();

            if($pet['food'] <= 4)
                $actions[] = 'was looking around the house for something to eat, but instead found';

            if($pet['love'] <= 4 || $pet['esteem'] <= 4 || $pet['safety'] <= 4)
                $actions[] = 'was looking for you, but found';

            if($pet['conscientious'] >= 8 && $pet['extraverted'] <= 5)
                $actions[] = 'was writing in ' . his_her($pet['gender']) . ' journal, and wrote about';

            if(mt_rand(mt_rand(0, 8), 10) <= $pet['extraverted'])
            {
                $friend = get_random_pet_friend($pet);

                if($friend !== false)
                    $actions[] = 'was going to go hang out with <a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>, when ' . he_she($pet['gender']) . ' saw';
            }

            $activities = array(
                'was out gathering, and discovered' => gathering_dice($pet),
                'was out mining, and discovered' => mining_dice($pet),
                'was out lumberjacking, and discovered' => lumberjacking_dice($pet),
                'was out fishing, and discovered' => fishing_dice($pet),
                'was playing Virtual Hide-and-go-Seek Tag online, and saw' => vhagst_dice($pet),
                'was fighting a monster who had' => adventuring_dice($pet),
                'was out hunting, and discovered' => hunting_dice($pet),
                'was magic-binding' => binding_dice($pet),
                'was creating some jewelery;' => jeweling_dice($pet),
                'was making something out of wood;' => carpentry_dice($pet),
                'was out gardening, and found' => gardening_dice($pet),
                'was sculpting' => sculpting_dice($pet),
                'was painting' => painting_dice($pet),
                'was making something leather;' => leatherworking_dice($pet),
                'was sewing something...' => tailoring_dice($pet),
                'was making something...' => crafting_dice($pet),
                'was forging something...' => smithing_dice($pet),
                'was working in the lab and discovered' => chemistry_dice($pet),
                'was inventing' => mechanical_engineering_dice($pet),
                'was programming' => electrical_engineering_dice($pet)
            );

            $total = array_sum($activities);
            $average = $total / count($activities);

            foreach($activities as $description=>$value)
            {
                if($value >= $average * 1.5)
                    $actions[] = $description;
            }

            if(count($actions) == 0)
                return false;

            return $pet['petname'] . ' ' . $dreamed . ' ' . he_she($pet['gender']) . ' ' . $actions[array_rand($actions)] . ' ' . $target . '... but ' . he_she($pet['gender']) . ' can\'t remember what it was...';

        case 2:
            $transportation = array(
                'flying an airplane', 'flying a rocket ship', 'riding a giraffe', 'riding a dragon', 'riding a field mouse', 'in a submarine',
                'teleporting from place to place', 'desperately'
            );

            if($pet['conscientious'] <= 3)
                $transportation[] = 'aimlessly';

            if(mt_rand(mt_rand(0, 8), 10) <= $pet['extraverted'])
            {
                $friend = get_random_pet_friend($pet);

                if($friend !== false)
                    $actions[] = 'with <a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>,';
            }

            return $pet['petname'] . ' ' . $dreamed . ' ' . he_she($pet['gender']) . ' was ' . $transportation[array_rand($transportation)] . ' looking for ' . $target . '.';
    }
}

function dream_description_scary_house_items(&$pet)
{
    $scary_item = fetch_single('
        SELECT monster_inventory.itemname
        FROM
          monster_inventory
          LEFT JOIN monster_items
            ON monster_inventory.itemname=monster_items.itemname
        WHERE
          monster_inventory.user=' . quote_smart($pet['owner']) . '
          AND monster_inventory.location LIKE \'home%\'
          AND monster_items.hourlysafety<0
        ORDER BY RAND()
        LIMIT 1
    ');

    if($scary_item === false)
        return false;
    else
    {
        $animated = array('a', 'a living', 'an animated', 'a shadowy');
        $attacked = array('attacked', 'chased', 'eaten', 'hunted', 'betrayed', 'tricked');

        if($pet['extraverted'] <= 5 && $pet['independent'] <= 5)
            $attacked[] = 'relentlessly taunted';

        return $pet['petname'] . ' had a dream about being ' . $attacked[array_rand($attacked)] . ' by ' . $animated[array_rand($animated)] . ' ' . $scary_item['itemname'] . '!';
    }
}

function dream_description_random_story(&$pet)
{
    global $FLAVORS;

    $dreams = array('had a dream', 'dreamed');
    $dreamed = $dreams[array_rand($dreams)];

    if(mt_rand(mt_rand(0, 8), 10) <= $pet['extraverted'])
    {
        $friend = get_random_pet_friend($pet);

        if($friend !== false)
            $friend_link = '<a href="/petprofile.php?petid=' . $friend['idnum'] . '">' . $friend['petname'] . '</a>';
    }
    else
        $friend = false;

    switch(mt_rand(1, 19))
    {
        case 1:
            $locations = array('the city', 'New York', 'London', 'Tokyo', 'Madagascar', 'your home town');
            $disasters = array('disaster struck', 'a plague broke out in', 'snake-people attacked', 'a computer-virus wiped all computer records in');

            return $pet['petname'] . ' ' . $dreamed . ' that ' . $disasters[array_rand($disasters)] . ' ' . $locations[array_rand($locations)] . ', and that ' . he_she($pet['gender']) . ' had super-powers, which ' . he_she($pet['gender']) . ' used to help survivors.';

        case 2:
            $clothing = array('naked', 'wearing only a tie', 'wearing only socks', 'wearing only a belt', 'naked, but holding a gun', 'naked, except for a pair of goggles');
            $outsides = array('town', 'the park', 'a racetrack', 'outside', 'downtown', 'in the subways', 'the neighborhood');
            $weather = array('cold', 'heat', 'wind', 'snow', 'sleet', 'explosions');
            $locations = array('in a church', 'in a school', 'in a hollow tree', 'in a large burrow', 'in an abandoned office building', 'under a parked airplane', 'in a dark corner', 'in the basement of a house');


            if($pet['likes_flavor'] > 0)
                $locations[] = $FLAVORS[$pet['likes_flavor']] . ' ' . (mt_rand(1, 2) == 1 ? 'farm' : 'factory');

            if($pet['dislikes_flavor'] > 0)
                $locations[] = $FLAVORS[$pet['dislikes_flavor']] . ' ' . (mt_rand(1, 2) == 1 ? 'farm' : 'factory');

            if($friend === false || mt_rand(1, 3) == 1)
                $with = 'another pet';
            else
                $with = $friend_link;

            return $pet['petname'] . ' ' . $dreamed . ' about running around ' . $outsides[array_rand($outsides)] . ' ' . $clothing[array_rand($clothing)] . ', trying to find shelter from the ' . $weather[array_rand($weather)] . '.  ' . ucfirst(he_she($pet['gender'])) . ' finally took refuge ' . $locations[array_rand($locations)] . ', where ' . he_she($pet['gender']) . ' ended up huddling together with ' . $with . '...';

        case 3:
            $game_types = array('an action-adventure video game', 'a board game', 'a low-budget RPG');
            $bosses = array('the end boss', 'the evil wizard', 'the king of the snake-people');

            if($pet['likes_color'] != 'none')
                $bosses[] = 'a super-intelligent shade of ' . $pet['likes_color'];

            if($pet['esteem'] < max_esteem($pet) / 3)
                $victories = array('lost', 'narrowly lost', 'almost won');
            else
                $victories = array('won', 'narrowly won', 'almost lost');

            return $pet['petname'] . ' ' . $dreamed . ' about designing ' . $game_types[array_rand($game_types)] . ', but then the game became real, and ' . he_she($pet['gender']) . ' fought ' . $bosses[array_rand($bosses)] . ', and ' . $victories[array_rand($victories)] . '!';

        case 4:
            return $pet['petname'] . ' ' . $dreamed . ' about standing in line at a fast food restaurant.';

        case 5:
            $results = array('but life wasn\'t so bad', 'and he summoned an army of dragons', 'but he was defeated by the snake-people', 'and turned out to be a spy');

            if($pet['likes_flavor'] > 0)
                $locations[] = 'and he gave everyone a life-time supply of ' . $FLAVORS[$pet['likes_flavor']];

            if($pet['dislikes_flavor'] > 0)
                $locations[] = 'and he gave everyone a life-time supply of ' . $FLAVORS[$pet['dislikes_flavor']];

            $game_types = array('card', 'board', 'video', 'role-playing', 'dice');

            return $pet['petname'] . ' ' . $dreamed . ' about playing a medieval-themed ' . $game_types[array_rand($game_types)] . ' game where you try to make a prince into a king, but as the game went on, it came to life!  Eventually the other player\'s prince became king, ' . $results[array_rand($results)] . '.';

        case 6:
            $opponents = array('dinosaurs', 'zombies', 'man-eating plants', 'aliens', 'the police', 'robots');
            if($pet['dislikes_flavor'] > 0)
                $opponents[] = $FLAVORS[$pet['dislikes_flavor']] . '-people';

            if($friend !== false)
                $opponents[] = $friend_link;

            return $pet['petname'] . ' dreamed that ' . he_she($pet['gender']) . ' was a ' . ($pet['gender'] == 'male' ? 'girl' : 'boy') . ', and was hiding from ' . $opponents[array_rand($opponents)] . '!';

        case 7:
            $monsters = array(
                'a mummy', 'a naga', 'a little girl', 'a little boy', 'an old man', 'an old woman',
                'a gift basket', 'the president', 'a security guard', 'a nazi', 'the Duke of Cetgueli',
            );

            if($friend !== false)
                $monsters[] = $friend_link;

            $treasures = array(
                'a lost map',
                'an Unreasonably Large Sword',
                'a computer program written by the Aztecs',
                'evidence of alien contact',
            );

            if($friend !== false && $pet['love'] < 10)
                $treasures[] = $friend_link;

            if($pet['food'] < 10)
                $treasures[] = $FLAVORS[array_rand($FLAVORS)];

            $activities = array(
                ' by swinging along a Great Wall on vines!',
                ', and discovering Atlantis!',
                ', disguised as ' . $monsters[array_rand($monsters)],
                ', looking for ' . $treasures[array_rand($treasures)],
            );

            if($pet['food'] < 10)
                $activities[] = ' while eating ' . $FLAVORS[array_rand($FLAVORS)] . '...';

            if($pet['safety'] < 10)
                $activities[] = ' while being chased by ' . $monsters[array_rand($monsters)];

            $locations = array(
                'ancient ruins',
                'a sunken ship',
                'a labyrinth hidden beneath the house',
                'the woods',
                'Ganymede',
                'an abandoned factory',
                'a cave in the side of a cliff',
                'sewer tunnels',
            );

            return $pet['petname'] . ' ' . $dreamed . ' about exploring ' . $locations[array_rand($locations)] . $activities[array_rand($activities)];

        case 8:
            $needs = array('Fret sheets', 'plans for a space station', 'homework notes', 'a guide on how to run a moose ranch');

            if($pet['likes_flavor'] > 0)
                $needs[] = $FLAVORS[$pet['likes_flavor']] . ' recipes';

            if($pet['dislikes_flavor'] > 0)
                $needs[] = $FLAVORS[$pet['dislikes_flavor']] . ' recipes';

            if($friend === false)
                $this_friend = 'one of ' . his_her($pet['gender']) . ' friends';
            else
                $this_friend = $friend_link;

            return $pet['petname'] . ' ' . $dreamed . ' that ' . $this_friend . ' needed ' . $needs[array_rand($needs)] . '.  ' . $pet['petname'] . ' printed some off from a web-site.';

        case 9:
            $targets = array('subway station', 'moose ranch', 'nail polish factory', 'brewery');
            $distractions = array('were separated', 'got lost', 'were chased by the authorities', 'encountered snake-people', 'sprung a trap');

            if($friend === false)
                $partner = his_her($pet['gender']) . ' partner-in-crime';
            else
                $partner = $friend_link . ' - ' . his_her($pet['gender']) . ' partner-in-crime -';

            return $pet['petname'] . ' ' . $dreamed . ' about an elaborate ' . $targets[array_rand($targets)] . ' heist!  ' . $pet['petname'] . ' and ' . $partner . ' ' . $distractions[array_rand($distractions)] . ', but each got away with their riches.';

        case 10:
            $golems = array('golems', 'robot fairies', 'stuffed animals');

            return $pet['petname'] . ' ' . $dreamed . ' that ' . he_she($pet['gender']) . ' had died, but it was OK: dead people bought ' . $golems[array_rand($golems)] . ' to inhabit in the real world.';

        case 11:
            $sizes = array('mile-long', 'kilometer-long', '12-foot-wide');
            $states = array('nearly dead', 'surprisingly peaceful', 'smaller than initially guessed');

            if($pet['food'] < 10)
                $states[] = 'hungry';

            if($pet['likes_color'] != 'none')
                $sizes[] = 'huge, ' . $pet['likes_color'];

            return $pet['petname'] . ' ' . $dreamed . ' that a ' . $sizes[array_rand($sizes)] . ' worm had broken up from underground and into the streets of the city.  Scientists were going crazy studying the thing, which seemed to be ' . $states[array_rand($states)] . '.';

        case 12:
            $evil = array('an evil wizard', 'a mad scientist', 'a time-traveller', 'an evil AI');
            $things = array('zombie' => 'zombies', 'plant' => 'plants', 'dragon' => 'dragons', 'robot' => 'robots', 'tree' => 'trees', 'candy' => 'candies');

            if($friend !== false)
                $evil[] = $friend_link . ', who was';

            $thing_i = array_rand($things, 2);
            $thing1 = $things[$thing_i[0]];
            $thing2 = $things[$thing_i[1]];

            return $pet['petname'] . ' ' . $dreamed . ' about fighting ' . $evil[array_rand($evil)] . ' bent on turning the population into ' . $thing1 . ', or ' . $thing2 . ', or ' . $thing_i[0] . '-' . $thing2 . ', or something like that.';

        case 13:
            $event = array('a fancy-dress party', 'a scientific conference', 'a wedding', 'a convention');

            return $pet['petname'] . ' ' . $dreamed . ' about trying to apply for a job, but the building the interview was in was hosting ' . $event[array_rand($event)] . ', and ' . $pet['petname'] . ' was not allowed in.';

        case 14:
            $things = array('temples', 'dragons', 'evil marionettes', 'aliens', 'living statues', 'skyscrapers', 'goblins', 'lost treasure', 'microscopic bees', 'friendly alligator-people', 'new pants', 'walking home');

            if($pet['likes_flavor'] > 0)
                $things[] = 'eating ' . $FLAVORS[$pet['likes_flavor']];

            if($pet['dislikes_flavor'] > 0)
                $things[] = 'eating ' . $FLAVORS[$pet['dislikes_flavor']];

            $thing_i = array_rand($things, 3);

            return $pet['petname'] . ' ' . $dreamed . ' about ' . $things[$thing_i[0]] . ', ' . $things[$thing_i[1]] . ', and ' . $things[$thing_i[2]] . '.';

        case 15:
            $locations = array('a house', 'a mansion', 'a tower', 'a dungeon');
            $regions = array('on a small island', 'in the middle of the jungle', 'deep underground', 'on an astroid', 'on the bottom of the ocean');

            return $pet['petname'] . ' ' . $dreamed . ' about exploring ' . $locations[array_rand($locations)] . ' that was built into a volcano ' . $regions[array_rand($regions)] . '.';

        case 16:
            $coverings = array('plants', 'wires', 'electronics', 'pipes', 'gears');

            if($pet['safety'] < 10)
            {
                $coverings[] = 'gore';
                $coverings[] = 'limbs';
                $coverings[] = 'eyeballs';
            }

            $disguises = array('wearing a trench coat', 'wearing a weird hat', 'wearing a necklace', 'walking around naked', 'rolling around in mud', 'pretending to be a weird cat');

            $cover = array_rand($coverings, 2);

            return $pet['petname'] . ' ' . $dreamed . ' that the entire world was covered in ' . $coverings[$cover[0]] . ' and ' . $coverings[$cover[1]] . '!  ' . ucfirst(he_she($pet['gender'])) . ' had to disguise ' . him_her($pet['gender']) . 'self by ' . $disguises[array_rand($disguises)] . '.';

        case 17:
            $foods = array('tea', 'sea-salt');

            if($pet['likes_flavor'] > 0)
                $foods[] = $FLAVORS[$pet['likes_flavor']];

            if($pet['dislikes_flavor'] > 0)
                $foods[] = $FLAVORS[$pet['dislikes_flavor']];

            $food = $foods[array_rand($foods)];

            $tastes = array('good', 'really good', 'really weird', 'gross', 'strangely sticky', 'hot', 'cold', 'really fancy');

            return $pet['petname'] . ' ' . $dreamed . ' about getting ' . $food . ' at a fancy sit-down ' . $food . ' restaurant.  It was ' . $tastes[array_rand($tastes)] . '!';

        case 18:
            $vehicles = array('taxi', 'bus', 'helicopter', 'train', 'boat');

            if($pet['likes_color'] != 'none' && mt_rand(1, 3) == 1)
                $bus = 'a ' . $pet['likes_color'] . ' ' . $vehicles[array_rand($vehicles)];
            else
                $bus = 'a ' . $vehicles[array_rand($vehicles)];

            if($friend !== false)
                $with = ' with ' . $friend_link;
            else
                $with = '';

            return $pet['petname'] . ' ' . $dreamed . ' about waiting for ' . $bus . $with . '.';

        case 19:
            return $pet['petname'] . ' ' . $dreamed . ' about hiding a mysterious number in the Mysterious Shop\'s HTML source code.';

    }
}

function dream_description_aliens(&$pet)
{
    $dreams = array('had a dream', 'dreamed');
    $dreamed = $dreams[array_rand($dreams)];

    $intro = $pet['petname'] . ' ' . $dreamed . ' about aliens attacking Earth!';

    if(mt_rand(1, 8) == 1)
    {
        switch(mt_rand(1, 4))
        {
            case 1: return $intro . '  They enslaved the entire planet!';
            case 2: return $intro . '  They stripped the planet of its natural resources, and left!';
            case 3: return $intro . '  In the end, Earth was destroyed!';
            case 4: return $intro . '  But then an Ice Age suddenly struck, freezing everyone: aliens, humans, and PsyPets!';
        }
    }
    else
    {
        switch(mt_rand(1, 10))
        {
            case 1: return $intro . '  The governments of the world united to drive them away!';
            case 2: return $intro . '  In the dream, you protected ' . him_her($pet['gender']) . ' from the aliens!';
            case 3: return $intro . '  ' . $pet['petname'] . ' discovered that water is poisonous to them, saving the planet!';
            case 4: return $intro . '  In the end, the aliens are defeated by the common cold!';
            case 5: return $intro . '  ' . $pet['petname'] . ' wrote a computer virus that shut down the mothership, allowing us to destroy them!';
            case 6: return $intro . '  But the aliens depended on Crop Circles for their navigation, so everyone ruined all the Crop Circles, and defeated them!';
            case 7: return $intro . '  But then another alien race came and fought them off; apparently the aliens that attacked us were a group of wanted criminals in the galactic empire!';
            case 8: return $intro . '  During the height of the attack, the aliens mysteriously left!';
            case 9: return $intro . '  But a massive solar flare disabled their largest ships, allowing us to win!';
            case 10: return $intro . '  It turned out that HERG had been secretly developing a weapon to defeat them, and saved Earth!';
        }
    }
}

function get_random_pet_friend(&$pet)
{
    return fetch_single('
        SELECT b.petname,b.gender,b.idnum
        FROM
          psypets_pet_relationships AS a
          LEFT JOIN monster_pets AS b
            ON a.friendid=b.idnum
        WHERE petid=' . $pet['idnum'] . '
        ORDER BY RAND()
        LIMIT 1
    ');
}
