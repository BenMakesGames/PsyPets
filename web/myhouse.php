<?php
$whereat = 'home';
$wiki = 'My_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/petblurb.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/questlib.php';
require_once 'commons/backgrounds.php';

require_once 'commons/leonids.php';

if($house['maxbulk'] >= 2000 && !addon_exists($house, 'Kitchen'))
{
    $kitchen_project = $database->FetchSingle('
    SELECT a.idnum
    FROM
      monster_projects AS a
      LEFT JOIN psypets_homeimprovement AS b
        ON a.itemid=b.idnum
    WHERE
      a.type=\'construct\' AND
      a.userid=' . $user['idnum'] . ' AND
      b.name=\'Kitchen\'
  ');

    $offer_kitchen_addon = ($kitchen_project === false);

    if($offer_kitchen_addon && $_GET['action'] == 'kitchen')
    {
        $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
            'VALUES (\'construct\', ' . $user['idnum'] . ', 29, \'0\', \'You started this construction.\')';
        $database->FetchNone($command, 'starting project for house add-on');

        header('Location: /myhouse.php?msg=150:You start the foundations for a Kitchen Add-on!');
        exit();
    }
}
else
    $offer_kitchen_addon = false;

$house_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: my house');
if($house_tutorial_quest === false)
    $no_tip = true;

if($now_day == 6 && $now_month == 1 && $user['show_mysteriousshop'] == 'yes')
{
    $wise_men_day = get_quest_value($user['idnum'], 'wise men day ' . date('Y'));
    if($wise_men_day === false)
    {
        add_quest_value($user['idnum'], 'wise men day ' . date('Y'), 1);

        psymail_user($user['user'], 'msowner', 'Buy somethin\'', '<a href="mysteriousshop_special.php">Will ya!\'</a>');
        flag_new_mail($user['user']);
        $user['newmail'] = 'yes';
    }
}

if($user['show_park'] == 'no' && ($now - $user['signupdate']) >= 60 * 60 * 24)
{
    $database->FetchNone('UPDATE monster_users SET show_park=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1', 'revealing park');
    $user['show_park'] = 'yes';

    psymail_user($user['user'], $SETTINGS['site_ingame_mailer'], 'The Park', 'You can sign your pets up for events hosted by other players at The Park!  Keep an eye on the entrance fees, but also keep an eye out for prizes, which many players offer as part of their events.<br /><br />Depending on how well your pet does, it may gain safety, love, esteem, or even experience points.<br /><br >{i}(The Park has been revealed to you!  Find it in the Recreation menu.){/}');
    flag_new_mail($user['user']);
    $user['newmail'] = 'yes';
}

load_user_pets($user, $userpets);

$max_pets = max_active_pets($user, $house);

$initial_room = 'Common';

if(strlen($_GET['room']) > 0)
{
    $room = $_GET['room'];

    if (strlen($house['rooms']) > 0)
    {
        $rooms = explode(',', $house['rooms']);

        if (array_search($room, $rooms) !== false)
            $initial_room = $room;
    }
}

$house_hours = floor(($now - $house['lasthour']) / (60 * 60));

if($house_hours > 50)
{
    $house_hours = 50;
    $too_many_hours = true;
}

$can_spend_hours = (count($userpets) <= $max_pets && $house['curbulk'] <= min(max_house_size(), $house['maxbulk']) && $user['no_hours_fool'] == 'no');

if($can_spend_hours && $house_hours > 0 && $house_hours <= $user['auto_spend_hours'])
{
    header('Location: /myhouse/run_hours.php');
    exit();
}

// FETCH ROOM INVENTORY
if($house['curroom'] == '')
{
    $offset = 0;
}
else
{
    $wall_rooms = explode(',', $house['rooms']);
    $offset = array_search($house['curroom'], $wall_rooms) + 1;
}
/*
$walls = explode(',', $house['wallpapers']);
$wallpaper = $walls[$offset];
*/
if($user['take_survey_please'] == 'yes')
{
    psymail_user($user['user'], 'csilloway', 'Monthly Survey', 'Hey, ' . $user['display'] . '!  I was hoping you could take this quick survey for me.  We collect responses every month, to try and get a feel for how you think everything is going.<br /><br />The survey is no more than three questions long - sometimes less for newer players - and while it\'s entirely optional, we\'d really love to hear from you!<br /><br /><ul><li><a href="gamerating.php">Take the survey</a></li></ul>');

    $command = 'UPDATE monster_users SET take_survey_please=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'clearing survey alert from user account');
}
/*
if($wallpaper != 'none')
{
    if(is_numeric($wallpaper))
        $CONTENT_STYLE = 'background: #fff url(/gfx/postwalls/' . $POST_BACKGROUNDS[$wallpaper] . '.png) repeat;';
    else
        $CONTENT_STYLE = 'background: #fff url(/gfx/walls/' . $wallpaper . '.png) repeat;';
}
*/
if($_GET['viewby'] == 'icons' || $_GET['viewby'] == 'details')
{
    $house['view'] = $_GET['viewby'];
    $command = 'UPDATE monster_houses SET view=' . quote_smart($house['view']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating house view');
}

$fbi_quest = get_quest_value($user['idnum'], 'close encounter');

$FBI_QUEST = ($fbi_quest !== false && $fbi_quest['value'] < 7 && $now_month >= 11);

if($user['fireworks'] != '')
{
    require_once 'commons/threadfunc.php';

    $firework_string = '<div><p>How will you decorate this room?</p><table>';

    $fireworks = explode(',', $user['fireworks']);

    foreach($fireworks as $firework)
    {
        list($fireworkid, $quantity) = explode(':', $firework);

        $firework_string .= '<tr style="border-top: 1px solid #000;"><td style="background-image: url(gfx/postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png); text-align: center;"><img src="/gfx/shim.png" width="260" height="50" alt="" /><p><a href="giveroombackground.php?room=' . $house['curroom'] . '&firework=' . $fireworkid . '">Like this!</a> (' . $quantity . ' available)</p><img src="/gfx/shim.png" width="260" height="50" alt="" /></td>';
    }

    $firework_string .= '</table><center>[ <a href="#" onclick="firework_hide(); return false;">oops! nvm!</a> ]</center></div>';

    $firework_link = ' <a href="#" onclick="firework_popup(0, \'\'); return false;"><img src="/gfx/fireworks.png" width="16" height="16" alt="Apply Background" /></a>';
}

$rooms = explode(',', $house['rooms']);

if(strlen($_GET['msg']) > 0)
    $error_message = form_message(explode(',', $_GET['msg']));

if($user['toasty'] == 'yes')
{
    $database->FetchNone('UPDATE monster_users SET toasty=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
    $database->FetchNone('UPDATE psypets_badges SET toasty=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1');
    $say_toasty = ($database->AffectedRows() > 0);
}
else
    $say_toasty = false;

include 'commons/html.php';
?>
<head>
    <?php include 'commons/head.php'; ?>
    <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room</title>
    <script type="text/javascript" src="<?= $SETTINGS['protocol'] ?>://<?= $SETTINGS['static_domain'] ?>/js/scrolldetect.js"></script>
    <?php include 'commons/ajaxinventoryjs.php'; ?>
    <script type="text/javascript">
        function select_new_destination(id)
        {
            var destination = $('#project_destination_' + id).val();

            $('#project_dest_' + id).html('<img src="/gfx/throbber.gif" />');

            $.ajax({
                type: 'POST',
                url: 'ajax_projectdestination.php',
                data: 'id=' + id + '&dest=' + destination,
                success: function(data)
                {
                    if(data == 'error')
                        $('#project_dest_' + id).html('failed to save.<br />reload and try again.');
                    else
                    {
                        xhtml =
                            '<select class="project_dest" name="project_destination_' + id + '" id="project_destination_' + id + '" onchange="select_new_destination(' + id + ')">' +
                            '<option value="">Common</option>' +
                            <?php foreach($rooms as $this_room): ?>
                            '<option value="<?= $this_room ?>"' + (data == '<?= $this_room ?>' ? ' selected="selected"' : '') + '><?= $this_room ?></option>' +
                            <?php endforeach; ?>
                            '</select>'
                        ;

                        $('#project_dest_' + id).html(xhtml);
                    }
                }
            });
        }

        function disable_hours_form()
        {
            $('#run_hours_form input').hide();
            $('#run_hours_form').append('<img src="/gfx/throbber.gif" width="16" height="16" />');
            return true;
        }

        $(function()
        {
            $('#petactions').submit(function()
            {
                var this_form = $(this);
                var form_data = $(this).serialize() + '&ajax=1';

                $('#petactions .pets li').each(function(i)
                {
                    var select = $(this).find('select');
                    if(select.length > 0 && select.val() != 0) // 0 indicates "nothing for now"; no need to reload
                        $(this).find('select').attr('disabled', 'disabled');
                });

                $.post(
                    this_form.attr('action'),
                    form_data,
                    function(data)
                    {
                        $('#petactionmessages').css({'display': 'block'});
                        $('#petactionmessages').append(data);

                        $('#petactions .pets li').each(function(i)
                        {
                            var id = $(this).attr('data-pet-id');
                            var this_pet = $(this);
                            var select = $(this).find('select');

                            if(select.length > 0 && select.val() != 0) // 0 indicates "nothing for now"; no need to reload
                            {
                                $(this).load('/myhouse/petblurb.php?petid=' + id + '&numpets=<?= count($userpets) ?>');
                            }
                        });
                    }
                ); // artificial form post

                return false;
            }); // #petactions submit

            $('.js-load-room').on('click', function(e) {
                e.preventDefault();
                loadRoom($(this).attr('data-room'));
            });

            loadRoom('<?= $initial_room ?>');
        });

        function loadRoom(room)
        {
            $('#js-tab-inventory > ul.tabbed > li.activetab').removeClass('activetab');
            $('#js-tab-inventory > ul.tabbed > li a[data-room="' + room + '"]').closest('li').addClass('activetab');

            $('#js-house-inventory').empty();

            $('#js-house-inventory').load('/myhouse/room_inventory.php?room=' + room, function() {
                <?php if($user['inventory_context_menu'] == 'yes'): ?>
                $('#roominventory').jeegoocontext('inventorymenu');
                <?php endif; ?>
            });
        }
    </script>
    <?php if($user['fireworks'] != ''): ?>
        <script type="text/javascript">
            var firework_string = '<?= $firework_string ?>';
        </script>
        <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/thread.js"></script>
    <?php endif; ?>

    <?php if($user['toasty'] == 'yes'): ?>
        <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/toasty.js"></script>
    <?php endif; ?>
    <?php /*
    <?php if($wallpaper == 'space'): ?>
        <style type="text/css">
            #content li, #content p, #content h5, #content h4 { color: #fff; }
            #content ul.tabbed { border-bottom: 1px solid rgba(255, 255, 255, 0.75); }
            #content .row { background-color: transparent; }
            #content .row_backlit { background-color: transparent; border: 1px dashed rgba(255, 240, 192, 0.5); padding: 4px 3px 3px; }
        </style>
    <?php endif; ?>
    */ ?>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <div id="kitchen" style="display: none;"><center>
        <br /><br /><img src="/gfx/throbber.gif" width="16" height="16" /><br /><br /><br />
    </center></div>
    <div id="project_destination"></div>
    <?php if($house_tutorial_quest === false): ?>
        <?php include 'commons/tutorial/myhouse.php'; ?>
    <?php endif; ?>

    <?php if($its_your_birthday): ?>
        <div style="background:url('gfx/streamers_yellow.png'); height:48px; font-size:48px;"><center><img src="/gfx/happy_birthday.png" width="450" height="48" /></center></div>
        <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/confetti.js"></script>
    <?php endif; ?>
    <h4><?= $sayroom ?> Room, <?= $user['display'] ?>'s House <i>(<span id="housebulk"><?= render_house_bulk($house) ?></span><a href="/help/house_space.php" class="help">?</a> <a href="housesummary.php"><img src="/gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a><?= $firework_link?>)</i></h4>
    <?php if(count($userpets) > 0): ?>
        <?php if($house_hours > 0): ?>
            <?php if($user['no_hours_fool'] == 'yes'): ?>
                <p class="failure">HEY!  Items have been returned to your storage from the Market Square - possibly a LOT of them!  Before you run hours, make sure to clear out items as necessary, by gameselling, throwing away, stashing in the basement, etc, them.</p>
                <p class="failure">Why this warning?  Because if a lot of items <em>were</em> returned to you, you could have HUUUUUGE storage fees unless you took care of those items!</p>
                <ul><li><a href="gimmemyhousehours.php">Understood!  I'll accept responsibility for any storage fees I may have!  I'll even take a look at my incoming right this very moment!</a></li></ul>
            <?php elseif(!$can_spend_hours): ?>
                <p class="failure">
                    <?php if(count($userpets) > $max_pets): ?><strong>You have too many pets in your house.</strong><?php endif; ?>
                    <?php if($house['curbulk'] > min(max_house_size(), $house['maxbulk'])): ?><strong>You have too many items in your house.</strong><?php endif; ?>
                    <a href="/help/house_space.php" class="help">?</a> You must free up space before you can run hours.
                </p>
            <?php else: ?>
                <p>Your <?= count($userpets) != 1 ? 'pets are' : 'pet is' ?> waiting to perform <?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?> of actions.<a href="/help/hours.php" class="help">?</a><?php if($too_many_hours): ?>(You cannot accumulate more than 50 hours!  Now's the time to use 'em!)<?php endif; ?></p>
            <?php endif; ?>

            <div style="padding: 0 0 1em 1em;"><form action="/myhouse/run_hours.php" method="post" onsubmit="return disable_hours_form();" id="run_hours_form"><p>
                <?php if($can_spend_hours): ?>
                    <?php $confirm_skip = ($user['confirm_skip'] == 'yes' ? ' onclick="return confirm(\'Really-really?\');"' : ''); ?>
                    <input type="submit" name="action" value="<?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?>, go!" style="width:90px;" />
                    <input type="submit" name="action" value="8 hours, go!" style="width:90px;"<?= $house_hours < 8 ? ' disabled="disabled"' : '' ?> />
                    <input type="submit" name="action" value="Skip them!"<?= $confirm_skip ?> style="width:90px;" />
                <?php else: ?>
                    <input type="submit" name="action" value="<?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?>, go!" disabled="disabled" style="width:90px;" />
                    <input type="submit" name="action" value="8 hours, go!" style="width:90px;" disabled="disabled" />
                    <input type="submit" name="action" value="Skip them!"<?= $confirm_skip ?> style="width:90px;" />
                <?php endif; ?>
            </p></form></div>
        <?php else: ?>
            <p><i>(Your pet<?= (count($userpets) != 1 ? 's' : '') ?> will be ready to do stuff<span id="house-hour-timer"> in about <?= duration($house['lasthour'] + 60 * 60 - $now, 2) ?>.</span><a href="/help/hours.php" class="help">?</a>)</i></p>
            <script type="text/javascript">
                $(function() {
                    var count_down = <?= $house['lasthour'] + 60 * 60 - $now ?>;

                    var house_count_down = setInterval(
                        function() {
                            count_down--;

                            if(count_down == 0)
                            {
                                $('#house-hour-timer').html('... right now!  Reload the page to see!');
                                clearInterval(house_count_down);
                                return;
                            }

                            var
                                minutes = Math.floor(count_down / 60),
                                seconds = count_down % 60
                                ;

                            $('#house-hour-timer').html(' in ' + minutes + ' minute' + (minutes != 1 ? 's' : '') + ', ' + seconds + ' second' + (seconds != 1 ? 's' : '') + '.');
                        },
                        1000
                    );
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($error_message): ?>
        <p><?= $error_message ?></p>
    <?php endif; ?>

    <?php if($say_toasty === true): ?>
        <p><i>(You received the Toasty! Badge!)</i></p>
    <?php endif; ?>

    <?php if($offer_kitchen_addon): ?>
        <p>Your house is expanding nicely!  Why not build a proper kitchen?</p>
        <ul><li><a href="?action=kitchen">Why not, indeed!</a></li></ul>
    <?php endif; ?>

    <ul class="js-tab-bar tabbed">
        <li<?php if ($initial_room == 'Common'): ?> class="activetab"<?php endif; ?>><a href="#js-tab-pets">Pets</a></li>
        <li><a href="#js-tab-journals">Journals</a></li>
        <li><a href="#js-tab-logs">Logs</a></li>
        <li<?php if ($initial_room != 'Common'): ?> class="activetab"<?php endif; ?>><a href="#js-tab-inventory">Inventory</a></li>
    </ul>

    <div class="js-tab<?php if ($initial_room != 'Common'): ?> hidden<?php endif; ?>" id="js-tab-pets">
        <?php $half_hour_ready = 0; ?>

        <?php if(count($userpets) > 0): ?>
            <h5>Pets <i>(<?= count($userpets) ?> / <?= $max_pets ?><?php if(count($userpets) > 1): ?><a href="/help/house_space.php" class="help">?</a>; <a href="/myhouse/arrange_pets.php">rearrange</a><?php endif; ?>)</i></h5>
            <form action="/loveaction.php" method="post" name="petactions" id="petactions">
                <?php
                // PET LISTING START!
                ob_start();

                echo '<ul class="inventory pets">';

                $colstart = begin_row_class();
                $pet_count = 0;
                $maxpets = count($userpets);

                $loveoptions = love_options($inventory);

                if(addon_exists($house, 'Refreshing Spring'))
                    $loveoptions[-4] = 'Drink from Refreshing Spring';

                if(addon_exists($house, 'Lake'))
                {
                    if($now_month == 12 || $now_month == 1)
                        $loveoptions[-5] = 'Ice Skate on Lake';
                    else
                        $loveoptions[-5] = 'Play in Lake';
                }

                require_once 'commons/adventurelib.php';
                $adventure = get_adventure($user['idnum']);
                if($adventure !== false && $adventure['progress'] < $adventure['difficulty'])
                    $loveoptions[-1000] = 'Adventure!';

                foreach($userpets as $petnum=>$pet)
                {
                    echo '<li id="pet' . $pet['idnum'] . '" data-pet-id="' . $pet['idnum'] . '">';

                    if(pet_blurb($user, $house, $petnum, $maxpets, $pet, $loveoptions))
                        $half_hour_ready++;

                    echo '</li>';

                    $pet_count++;
                }

                echo '</ul><div class="endinventory"></div>';

                $pet_listing = ob_get_contents();
                ob_end_clean();
                // PET LISTING END!
                ?>
                <?php if($half_hour_ready > 0 && count($userpets) > 9): ?><p><input type="submit" name="submit" value="Go"></p><?php endif; ?>
                <?= $pet_listing ?>
                <?php if($half_hour_ready > 0): ?><p><input type="submit" name="submit" value="Go"></p><?php endif; ?>
            </form>
            <p id="petactionmessages" style="display:none;"></p>
        <?php elseif($user['breeder'] == 'no'): ?>
            <?php
            $command = 'SELECT idnum FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' LIMIT 1';
            $existing_pet = $database->FetchSingle($command, 'fetching existing pet');
            ?>
            <?php if($existing_pet === false): ?>
                <ul><li><a href="request_pet.php">Get a new, random pet</a></li></ul>
            <?php endif; ?>
        <?php else: ?>
            <p>You have no pets!  But you have a Breeder's License, so I'm sure you'll figure something out :P</p>
        <?php endif; ?>
    </div>

    <div class="js-tab hidden" id="js-tab-journals">

    </div>

    <div class="js-tab hidden" id="js-tab-logs">

    </div>

    <div class="js-tab<?php if ($initial_room == 'Common'): ?> hidden<?php endif; ?>" id="js-tab-inventory">
        <?php room_display($house); ?>
        <div id="js-house-inventory">

        </div>
    </div>
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
