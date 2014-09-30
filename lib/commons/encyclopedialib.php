<?php
require_once 'commons/museumlib.php';
require_once 'commons/petlib.php';
require_once 'commons/blimplib.php';

$CUSTOM_DESC = array(
    'no' => 'Common',
    'yes' => 'Custom',
    'limited' => 'Limited',
    'monthly' => 'Erstwhile',
    'recurring' => 'Favor',
    'x-game' => 'Cross-game',
);


function wiki_link($itemname)
{
    global $SETTINGS;

    $link = str_replace(array('#', '&', '+', '"'), array('no. ', ' and ', ' ', '%22'), $itemname);
    $link = trim($link);
    //$link = link_safe($link);

    return '<a href="http://' . $SETTINGS['wiki_domain'] . '/' . $link . '">View PsyHelp entry</a>';
}

function get_profile_item_ranking($userid, $itemid)
{
    return fetch_single('SELECT * FROM psypets_profile_treasures WHERE userid=' . (int)$userid . ' AND itemid=' . (int)$itemid . ' LIMIT 1');
}

function RenderEncyclopediaItem(&$item, &$user, &$pets)
{
    global $CUSTOM_DESC, $PET_STAT_DESCRIPTIONS, $now;

    $is_edible = ($item['is_edible'] == 'yes');
    $is_equip = ($item['is_equipment'] == 'yes');
    $is_key = ($item['key_id'] > 0);
    $is_useable = (strlen($item['action']) > 0);
    $is_toy = (strlen($item['playdesc']) > 0);
    $is_hourly = ($item['hourlyfood'] != 0 || $item['hourlysafety'] != 0 || $item['hourlylove'] != 0 || $item['hourlyesteem'] != 0);
    $is_recyclable = ($item['can_recycle'] == 'yes');
    $can_pawn_with = ($item['can_pawn_with'] == 'yes');
    $can_pawn_for = ($item['can_pawn_for'] == 'yes');

    if($is_useable > 0)
        $action = explode(';', $item['action']);

    if($is_equip)
        $equip_level = EquipLevel($item);

    $durability = durability_group($item['durability']);

    $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=' . quote_smart($item['itemname']);
    $data = fetch_single($command, 'encyclopedia2.php');
    $number_in_game = (int)$data['COUNT(*)'];

    $command = 'SELECT SUM(quantity) FROM psypets_basement WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
    $data = fetch_single($command, 'encyclopedia2.php');
    $number_in_basements = (int)$data['SUM(quantity)'];

    $number_in_museum = get_museum_item_count($item['idnum']);

    $total_in_existance = $number_in_game + $number_in_basements;

    if(substr($item['itemtype'], 0, 10) == 'print/book')
    {
        $books_in_libraries = fetch_single('
      SELECT SUM(quantity) AS q
      FROM psypets_libraries
      WHERE itemid=' . $item['idnum'] . '
    ');

        $total_in_existance += (int)$books_in_libraries['q'];
    }

    $highbid = get_highbid_byitem($item['itemname']);

    if(strlen($user['user']) > 0 && $item['custom'] == 'no' && $user['museumcount'] > 0)
    {
        require_once 'commons/museumlib.php';
        $museum_item = get_museum_item($user['idnum'], $item['idnum']);
        if($museum_item === false)
            $museum_note = true;
        else
            $donated_to_museum = $museum_item['timestamp'];
    }
    else
        $donated_to_museum = false;

    $item_types = take_apart('/', $item['itemtype']);
    $these_types = array();
    $linked_item_types = array();

    foreach($item_types as $type)
    {
        $these_types[] = $type;
        $linked_item_types[] = '<a href="/encyclopedia.php?submit=Search&itemtype=' . implode('/', $these_types) . '">' . $type . '</a>';
    }
    ?>
    <ul>
        <li><?= wiki_link($item['itemname']) ?></li>
    </ul>
    <table class="nomargin">
<tr>
    <td valign="top"><img src="/gfx/items/<?= $item['graphic'] ?>" height="32" /></td>
<td>
    <p><?= $item['itemname'] ?><br /><?= implode('/', $linked_item_types) ?></p>
    <?php
    echo '<p style="font-style: italic">';

    if($total_in_existance > 0)
    {
        echo $total_in_existance . ' exist' . ($total_in_existance == 1 ? 's' : '') . ' in the game';

        if($number_in_museum > 0)
            echo '; ', ($number_in_museum > 1 ? $number_in_museum . ' more have' : '1 more has'), ' been donated to <a href="/museum/donators.php?item=' . $item['idnum'] . '">The Museum</a>';
    }
    else
    {
        echo '0 exist in the game';

        if($number_in_museum > 0)
            echo '; ', ($number_in_museum > 1 ? $number_in_museum . ' have' : '1 has'), ' been donated to <a href="/museum/donators.php?item=' . $item['idnum'] . '">The Museum</a>';
    }

    echo '.</p>';

    echo '</td></tr></table>';

    if($museum_note)
        echo '<p><i>You have not donated one to <a href="/museum/">The Museum</a> yet.</i></p>';

    if(strlen($item['enc_entry']) > 0)
    {
        echo '<hr />' .
            '<p>' . format_text($item['enc_entry']) . '</p>';
    }

    echo '<hr />';
    ?>
<table>
    <tr><th>Size / Weight</th><td><?= ($item['bulk'] / 10) . ' / ' . ($item['weight'] / 10) ?></td></tr>
    <?php
    if($item['durability'] > 0 || $is_equip)
        echo '<tr><th>Durability</th><td>' . $durability . ($item['norepair'] == 'yes' ? '; cannot be repaired' : '') . '</td></tr>';

    if($user['idnum'] > 0)
    {
        echo '<tr><th>Profile Display<a href="/help/profile_items.php" class="help">?</a></th><td>';

        $profile_display = get_profile_item_ranking($user['idnum'], $item['idnum']);

        echo '<select id="ranking">';
        for($x = 1000; $x > 0; $x -= 100)
        {
            $label = ($x / 100);
            if($x == 1000)
                $label .= ' - highest';
            else if($x == 100)
                $label .= ' - lowest';

            if($x == $profile_display['ranking'])
                echo '<option value="' . $x . '" selected>' . $label . '</option>';
            else
                echo '<option value="' . $x . '">' . $label . '</option>';
        }

        if($profile_display === false)
            echo '<option value="0" selected>None</option>';
        else
            echo '<option value="0">None</option>';

        echo '</select> <span id="enc_throbber" style="display:none;"><img src="/gfx/throbber.gif" alt="waiting..." height="16" weight="16" /></span>';
        ?>
        <script type="text/javascript">
            $(function() {
                $('#ranking').change(function() {
                    $('#ranking').attr('disabled', 'disabled');
                    $('#enc_throbber').show();

                    $.post(
                        '/ajax_profile_item_ranking.php',
                        {
                            'itemid': <?= $item['idnum'] ?>,
                            'ranking': $('#ranking').val()
                        },
                        function(data)
                        {
                            $('#ranking').val(parseInt(data));
                            $('#ranking').removeAttr('disabled');
                            $('#enc_throbber').hide();
                        }
                    );
                });
            });
        </script>
    <?php
    } // if logged in

    echo '</td></tr>',
    '<tr><th>Availability<a href="/help/item_availability.php" class="help">?</a></th><td>', $CUSTOM_DESC[$item['custom']], '</td></tr>',
    '</table>';

    if($is_recyclable) $properties[] = '<li>Can be <a href="/recycling.php">Recycled</a></li>';
    if($is_edible) $properties[] = '<li>Is edible' . ($user['idnum'] > 0 ? ' (see below)' : '') . '</li>';
    if($is_equip) $properties[] = '<li>Is a tool for pets<a href="/help/equipment.php" class="help">?</a> (equipment details below)</li>';
    if($is_key) $properties[] = '<li>Is a key for pets<a href="/help/equipment.php" class="help">?</a></li>';
    if($is_useable) $properties[] = '<li>Can be used: "' . $action[0] . '"</li>';
    if($is_toy)
    {
        $property = '<li>Is a toy: "' . $item['playdesc'] . '"<ul class="nomargin">';

        if($item['playbed'] == 'yes')
            $property .= '<li>Is a bed</li>';
        if($item['playfood'] > 0)
            $property .= '<li>Makes pets less hungry</li>';
        else if($item['playfood'] < 0)
            $property .= '<li class="failure">Makes pets more hungry</li>';

        if($item['playsafety'] > 0)
            $property .= '<li>Makes pets feel safe</li>';
        else if($item['playsafety'] < 0)
            $property .= '<li class="failure">Makes pets feel less safe</li>';

        if($item['playlove'] > 0)
            $property .= '<li>Makes pets feel loved</li>';
        else if($item['playlove'] < 0)
            $property .= '<li class="failure">Makes pets feel less loved</li>';

        if($item['playesteem'] > 0)
            $property .= '<li>Makes pets feel esteemed</li>';
        else if($item['playesteem'] < 0)
            $property .= '<li class="failure">Makes pets feel less esteemed</li>';

        if($item['playstat'] != '')
        {
            if(array_key_exists($item['playstat'], $PET_STAT_DESCRIPTIONS))
                $property .= '<li>Teaches ' . $PET_STAT_DESCRIPTIONS[$item['playstat']] . '</li>';
            else
                $property .= '<li class="failure">Bugged play action.  Please let ' . $SETTINGS['author_resident_name'] . ' know.</li>';
        }

        $property .= '</ul></li>';

        $properties[] = $property;
    }
    if($is_hourly)
    {
        $property = '<li>Has hourly effects on pets:<ul class="nomargin">';

        if($item['hourlyfood'] > 0)
            $property .= '<li>Makes pets less hungry</li>';
        else if($item['hourlyfood'] < 0)
            $property .= '<li class="failure">Makes pets more hungry</li>';

        if($item['hourlysafety'] > 0)
            $property .= '<li>Makes pets feel safe</li>';
        else if($item['hourlysafety'] < 0)
            $property .= '<li class="failure">Makes pets feel less safe</li>';

        if($item['hourlylove'] > 0)
            $property .= '<li>Makes pets feel loved</li>';
        else if($item['hourlylove'] < 0)
            $property .= '<li class="failure">Makes pets feel less loved</li>';

        if($item['hourlyesteem'] > 0)
            $property .= '<li>Makes pets feel esteemed</li>';
        else if($item['hourlyesteem'] < 0)
            $property .= '<li class="failure">Makes pets feel less esteemed</li>';

        if($item['hourlystat'] != '')
        {
            if(array_key_exists($item['hourlystat'], $PET_STAT_DESCRIPTIONS))
                $property .= '<li>Teaches ' . $PET_STAT_DESCRIPTIONS[$item['hourlystat']] . '</li>';
            else
                $property .= '<li class="failure">Bugged hourly action.  Please let ' . $SETTINGS['author_resident_name'] . ' know.</li>';
        }

        $property .= '</ul></li>';

        $properties[] = $property;
    }

    if($item['cursed'] == 'yes') $properties[] = '<li>Is "cursed" (cannot be moved out of the house)</li>';
    if($item['cancombine'] == 'yes') $properties[] = '<li>Can be combined at the <a href="/af_combinationstation3.php">Combination Station</a></li>';

    if($item['questitem'] == 'yes')
        $properties[] = '<li>This item is important!  Hold on to it!</li>';
    else if($item['nosellback'] == 'yes' && $item['noexchange'] == 'yes')
        $properties[] = '<li>This item may not be sold to the game, or exchanged with other players.</li>';

    if(count($properties) > 0)
        echo '<ul>' . implode('', $properties) . '</ul>';

    $pawn_shop = array();
    if($can_pawn_with && $can_pawn_for)
        $pawn_avail = '<b>Can get</b> at the Pawn Shop;<br /><b>can give</b> to the Pawn Shop (and Greenhouse)';
    else if($can_pawn_with)
        $pawn_avail = '<b>Cannot get</b> at the Pawn Shop;<br /><b>can give</b> to the Pawn Shop (and Greenhouse)';
    else if($can_pawn_for)
        $pawn_avail = '<b>Can get</b> at the Pawn Shop;<br /><b>cannot give</b> to the Pawn Shop (or Greenhouse)';
    else
        $pawn_avail = '<b>Cannot get</b> at the Pawn Shop;<br /><b>cannot give</b> to the Pawn Shop (or Greenhouse)';

    echo '
    <hr />
    <h5>Current Market Information</h5>
    <table>
  ';

    if($item['custom'] == 'yes')
    {
        $command = 'SELECT markup,ownerid FROM psypets_custom_item_store WHERE itemid=' . $item['idnum'] . ' LIMIT 1';
        $selling = $GLOBALS['database']->FetchSingle($command);

        if($selling !== false)
        {
            $seller = get_user_byid($selling['ownerid']);
            echo '<tr><th valign="top"><nobr>Custom Item Store Listing</nobr></th><td><a href="/favorstore.php?resident=' . link_safe($seller['display']) . '">' . $seller['display'] . ' is selling copies for ' . ($selling['markup'] + 300) . ' Favor</a></td></tr>';
        }
    }
    ?>
    <tr><th valign="top"><nobr>Gamesell Value<a href="/help/gamesell.php" class="help">?</a></nobr></th><td><?= ($item['nosellback'] == 'yes' ? 'may not be sold to the game' : ceil($item['value'] * sellback_rate()) . '<span class="money">m</span>') ?></td></tr>
    <tr><th valign="top"><nobr><a href="/pawnshop.php">Pawn Shop</a> Availability</nobr></th><td><?= $pawn_avail ?></td></tr>
    <?php

    if($item['noexchange'] == 'no')
    {
        $command = 'SELECT monster_inventory.forsale AS min_price,monster_users.display AS display FROM monster_inventory JOIN monster_users WHERE monster_inventory.user=monster_users.user AND monster_inventory.forsale>0 AND monster_users.openstore=\'yes\' AND monster_inventory.itemname=' . quote_smart($item['itemname']) . ' ORDER BY min_price ASC LIMIT 1';
        $fm_item = fetch_single($command, 'marketsquare.php');

        if($fm_item === false)
            $store_note = 'none';
        else
            $store_note = '<a href="/userstore.php?user=' . $fm_item['display'] . '">' . $fm_item['min_price'] . '<span class="money">m</span></a> (from ' . resident_link($fm_item['display']) . ')';

        $command = 'SELECT idnum,bidvalue FROM monster_auctions WHERE claimed=\'no\' AND bidtime>' . $now . ' AND itemname=' . quote_smart($item['itemname']) . ' ORDER BY bidvalue ASC LIMIT 1';
        $ah_item = fetch_single($command, 'fetching lowest bid');

        if($ah_item === false)
            $auction_note = 'none';
        else
            $auction_note = '<a href="/auctiondetails.php?auction=' . $ah_item['idnum'] . '">' . $ah_item['bidvalue'] . '<span class="money">m</span></a>';
        ?>
        <tr><th valign="top"><nobr><a href="/reversemarket.php">Seller's Market</a> High Bid</nobr></th><td><?= $highbid === false ? 'none' : $highbid['bid'] . '<span class="money">m</span>' ?></td></tr>
        <tr><th valign="top"><nobr><a href="/auctionhouse.php">Auction House</a> Lowest Bid</nobr></th><td><?= $auction_note ?></td></tr>
        <tr><th valign="top"><nobr><a href="/fleamarket/">Flea Market</a> Lowest Offer</nobr></th><td><?= $store_note ?></td></tr>
    <?php
    }

    if($item['nosellback'] == 'no')
    {
        if($item['is_grocery'] == 'yes')
        {
            $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=\'grocerystore\' AND itemname=' . quote_smart($item['itemname']);
            $data = fetch_single($command, 'fetching refuse store inventory');

            $count = (int)$data['c'];

            if($count == 0)
                echo '<tr><th><a href="/grocerystore_gamesold.php">Farmer\'s Market</a> Inventory</th><td>none</td></tr>';
            else
                echo '<tr><th><a href="/grocerystore_gamesold.php">Farmer\'s Market</a> Inventory</th><td><a href="/grocerystore_gamesold.php">' . ceil($item['value'] / 5.0 * 4) . '<span class="money">m</span></a> (' . $count . ' available)</td></tr>';
        }
        else
        {
            $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=\'ihobbs\' AND itemname=' . quote_smart($item['itemname']);
            $data = fetch_single($command, 'fetching refuse store inventory');

            $count = (int)$data['c'];

            if($count == 0)
                echo '<tr><th><a href="/recycling_gamesell.php?letter=' . $item['itemname'][0] . '">Refuse Store</a> Inventory</th><td>none</td></tr>';
            else
                echo '<tr><th><a href="/recycling_gamesell.php?letter=' . $item['itemname'][0] . '">Refuse Store</a> Inventory</th><td><a href="/recycling_gamesell.php?letter=' . $item['itemname'][0] . '">' . $item['value'] . '<span class="money">m</span></a> (' . $count . ' available)</td></tr>';
        }
    }

    echo '</table>';

    if($item['noexchange'] == 'yes')
        echo '<p><i>This item may not be exchanged with other players.</i></p>';
    else if($item['nomarket'] == 'yes')
        echo '     <p><i>This item may not be placed into Basements.</i></p>';

if($is_edible)
{
if($user['idnum'] > 0)
{
    echo '<hr /><h5>Meal Information</h5>';

    if($item['ediblecaffeine'] > 0)
        echo '<ul><li>Contains caffeine<a href="/help/energy.php" class="help">?</a></li></ul>';

    if($item['ediblehealing'] > 0)
        echo '<ul><li>Has healing properties</li></ul>';
    ?>
<table>
    <tr class="titlerow">
        <th>Pet</th>
        <th><nobr>Meal Size</nobr></th>
    </tr>
    <?php
    $rowclass = begin_row_class();
    foreach($pets as $pet)
    {
        if($item['ediblefood'] > 0)
        {
            $ratio = max_food($pet) / $item['ediblefood'];

            if($ratio < .80)
                $food_size = 'Too much';
            else if($ratio < 1.5)
                $food_size = 'A full meal';
            else if($ratio < 3)
                $food_size = 'A light meal';
            else
                $food_size = 'A snack';
        }
        else
            $food_size = 'Unfilling';
        ?>
        <tr class="<?= $rowclass ?>">
            <td><?= $pet['petname'] ?></td>
            <td><?= $food_size ?></td>
        </tr>
        <?php
        $rowclass = alt_row_class($rowclass);
    }

    echo '</table>';
} // if logged in
} // if edible

    if($is_equip)
    {
        echo '<hr />';

        $bonus = EquipBonusDesc($equip_level);

        if($bonus)
            echo '<h5>Equipment (' . $bonus . ')</h5>';
        else
            echo '<h5>Equipment</h5>';

        if($item['equip_is_revised'] == 'no')
        {
            if($item['custom'] == 'yes')
                echo '<p>This custom item has not been updated to work with the new equipment system.  If you are the creator of this item, <a href="/recombination.php">have it recombined for free!</a></p>';
            else
                echo '<p>' . $SETTINGS['author_resident_name'] . ' should update this equipment to work with the new equipment system.</p>';
        }

        $stats = array(
            'str' => 'strength',
            'dex' => 'dexterity',
            'sta' => 'toughness',
            'int' => 'intelligence',
            'per' => 'perception',
            'wit' => 'fast thinking',
            'athletics' => 'athletics',
            'stealth' => 'stealth',
            'fertility' => 'fertility',
            'adventuring' => 'adventuring',
            'hunting' => 'hunting',
            'fishing' => 'fishing',
            'gathering' => 'gathering',
            'lumberjacking' => 'lumberjacking',
            'mining' => 'mining',
            'crafting' => 'handicrafting',
            'tailoring' => 'tailoring',
            'leather' => 'leather-working',
            'painting' => 'painting',
            'jeweling' => 'jeweling',
            'sculpting' => 'sculpting',
            'carpentry' => 'carpenting',
            'smithing' => 'smithing',
            'electronics' => 'electrical engineering',
            'mechanics' => 'mechanical engineering',
            'chemistry' => 'chemistry',
            'binding' => 'magic-binding',
            'gardening' => 'gardening',
            'piloting' => 'airship piloting',
        );

        echo '<ul>';

        if($item['equip_open'] > 0)
            echo '<li>Inspires openness!</li>';
        else if($item['equip_open'] < 0)
            echo '<li>Inspires conservativeness!</li>';

        if($item['equip_extraverted'] > 0)
            echo '<li>Inspires outgoingness!</li>';
        else if($item['equip_extraverted'] < 0)
            echo '<li>Inspires introvertedness!</li>';

        if($item['equip_conscientious'] > 0)
            echo '<li>Inspires conscientiousness!</li>';
        else if($item['equip_conscientious'] < 0)
            echo '<li>Inspires a laid-back attitude!</li>';

        if($item['equip_playful'] > 0)
            echo '<li>Inspires playfulness!</li>';
        else if($item['equip_playful'] < 0)
            echo '<li>Inspires seriousness!</li>';

        if($item['equip_independent'] > 0)
            echo '<li>Inspires independence!</li>';
        else if($item['equip_independent'] < 0)
            echo '<li>Inspires dependence!</li>';

        foreach($stats as $field=>$stat)
        {
            $bonus = $item['equip_' . $field];
            if($bonus != 0)
                echo '<li' . ($bonus < 0 ? ' class="failure"' : '') . '>' . ucfirst(EquipBonusDesc($bonus)) . ' for ' . $stat . '!</li>';
        }

        if($item['equip_more_dreams'] == 'yes')
            echo '<li>Increases the likelihood of remembering dreams</li>';

        if($item['equip_goldenmushroom'] == 'yes')
            echo '<li>Does something weird (but good!)</li>';

        if($item['equip_vampire_slayer'] == 'yes')
            echo '<li>Vampires recoil in its presence</li>';

        if($item['equip_were_killer'] == 'yes')
            echo '<li>Werecreatures recoil in its presence</li>';

        if($item['equip_berry_craft'] == 'yes')
            echo '<li>Grants an affinity for berries</li>';

        if($item['equip_pressurized'] == 'yes')
            echo '<li>Protects from very high or very low-pressure environments</li>';

        if($item['equip_flight'] == 'yes')
            echo '<li>Allows the user to fly</li>';

        if($item['equip_fire_immunity'] == 'yes')
            echo '<li>Protects from extremely high temperatures</li>';

        if($item['equip_chill_touch'] == 'yes')
            echo '<li>Chills and freezes things</li>';

        if($item['equip_healing'] == 'yes')
            echo '<li>Speeds healing</li>';

        echo '</ul>';

        if($user['idnum'] > 0)
        {
            ?>
            <table>
            <tr class="titlerow">
                <th>Pet</th>
                <th>Can&nbsp;Equip?</th>
            </tr>
            <?php
            $rowclass = begin_row_class();

            foreach($pets as $pet)
            {
                $reason = get_equip_message($item, $pet);
                if($reason == '')
                    $reason = '<td class="success">yes</td>';
                else
                    $reason = '<td class="failure">' . $reason . '</td>';
                ?>
                <tr class="<?= $rowclass ?>">
                    <td><?= $pet['petname'] ?></td>
                    <?= $reason ?>
                </tr>
                <?php
                $rowclass = alt_row_class($rowclass);
            }

            echo '</table>';
        } // if logged in
    } // if an equipment

    if($number_in_game > 0 && $number_in_game <= 12)
    {
        $search_time = microtime(true);

        $owners = $GLOBALS['database']->FetchMultiple('SELECT user FROM monster_inventory WHERE user!=\'psypets\' AND itemname=' . quote_smart($item['itemname']));

        $owner_list = array();

        foreach($owners as $owner)
            $owner_list[] = $owner['user'];

        if(count($owner_list) > 0)
        {
            $user_list = $GLOBALS['database']->FetchMultiple('
				SELECT graphic,is_a_whale,display
				FROM monster_users
				WHERE user ' . $GLOBALS['database']->In($owner_list) . '
				LIMIT ' . count($owner_list) . '
			');
        }
        else
            $user_list = array();

        $search_time = microtime(true) - $search_time;

        $footer_note = '<br />Took ' . round($search_time, 4) . 's looking up the items\' owners.';

        echo '<hr /><h5>Owners</h5>';

        if(count($user_list) < $number_in_game)
            echo '<p><i>(Copies of the item which are stashed in a Basement will not have their owner reported here.)</i></p>';

        if(count($user_list) > 0)
        {
            echo '<table><tr>';

            $count = 0;

            foreach($user_list as $this_user)
            {
                $count++;

                echo '<td><img src="' . user_avatar($this_user) . '" alt="" /></td><td style="padding-right: 1em;"><a href="/residentprofile.php?resident=' . link_safe($this_user['display']) . '">' . $this_user['display'] . '</a></td>' . "\n";

                if($count % 3 == 0 && $count != count($user_list))
                    echo '</tr><tr>';
            }

            echo '</tr></table>';
        }
    } // if there's between 1 and 12 in the game
}
?>