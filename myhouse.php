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
require_once 'commons/inventory.php';
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

if(strlen($house['curroom']) > 0)
{
  $room = 'home/' . $house['curroom'];

  if($house['curroom']{0} == '$')
    $sayroom = substr($house['curroom'], 1);
  else
    $sayroom = $house['curroom'];

  $THIS_ROOM = $house['curroom'];

  $nopetrooms = take_apart(',', $house['nopet_rooms']);
  $show_pets = (!in_array($house['curroom'], $nopetrooms));
}
else
{
  $room = 'home';
  $sayroom = 'Common';
  $THIS_ROOM = 'Common';
  $show_pets = true;
}

if($show_pets)
  load_user_pets($user, $userpets);

$max_pets = max_active_pets($user, $house);

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
$query_time = microtime(true);

$command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($room);
$data = $database->FetchSingle($command, 'fetching storage item count');

$num_items = (int)$data['c'];
$num_pages = ceil($num_items / 2000);
$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

$inventory = get_room_inventory($user['user'], $room, $num_items, $num_pages, $page, $house['sort']);

if($num_items > 2000)
  $page_note = true;

$query_time = microtime(true) - $query_time;

$footer_note .= '<br />Took ' . round($query_time, 4) . 's fetching this room\'s inventory.';

load_user_projects($user, $userprojects);

if($house['curroom'] == '')
{
  $offset = 0;
}
else
{
  $wall_rooms = explode(',', $house['rooms']);
  $offset = array_search($house['curroom'], $wall_rooms) + 1;
}

$walls = explode(',', $house['wallpapers']);
$wallpaper = $walls[$offset];

if($user['take_survey_please'] == 'yes')
{
  psymail_user($user['user'], 'csilloway', 'Monthly Survey', 'Hey, ' . $user['display'] . '!  I was hoping you could take this quick survey for me.  We collect responses every month, to try and get a feel for how you think everything is going.<br /><br />The survey is no more than three questions long - sometimes less for newer players - and while it\'s entirely optional, we\'d really love to hear from you!<br /><br /><ul><li><a href="gamerating.php">Take the survey</a></li></ul>');

  $command = 'UPDATE monster_users SET take_survey_please=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'clearing survey alert from user account');
}

if($wallpaper != 'none')
{
  if(is_numeric($wallpaper))
    $CONTENT_STYLE = 'background: #fff url(/gfx/postwalls/' . $POST_BACKGROUNDS[$wallpaper] . '.png) repeat;';
  else
    $CONTENT_STYLE = 'background: #fff url(/gfx/walls/' . $wallpaper . '.png) repeat;';
}

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
          xhtml = '<select class="project_dest" name="project_destination_' + id + '" id="project_destination_' + id + '" onchange="select_new_destination(' + id + ')">' +
   '<option value="">Common</option>' +
<?php
foreach($rooms as $this_room)
{
  echo '\'<option value="' . $this_room . '"\' + (data == \'' . $this_room . '\' ? \' selected="selected"\' : \'\') + \'>' . $this_room . '</option>\' +';
}
?>
   '</select>';

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
<?php if($user['inventory_context_menu'] == 'yes'): ?>

    $('#roominventory').jeegoocontext('inventorymenu');
<?php endif; ?>
  });
  </script>
<?php
if($user['fireworks'] != '')
{
?>
  <script type="text/javascript">
   var firework_string = '<?= $firework_string ?>';
  </script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/thread.js"></script>
<?php
}

if($user['toasty'] == 'yes')
{
  echo '<script type="text/javascript" src="//' . $SETTINGS['static_domain'] . '/js/toasty.js"></script>';

  $command = 'UPDATE monster_users SET toasty=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'no more toasty!');

  $command = 'UPDATE psypets_badges SET toasty=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'toasty! badge');

  $say_toasty = ($database->AffectedRows() > 0);
}

if($wallpaper == 'space')
{
?>
  <style type="text/css">
   #content li, #content p, #content h5, #content h4 { color: #fff; }
   #content ul.tabbed { border-bottom: 1px solid rgba(255, 255, 255, 0.75); }
   #content .row { background-color: transparent; }
   #content .row_backlit { background-color: transparent; border: 1px dashed rgba(255, 240, 192, 0.5); padding: 4px 3px 3px; }
  </style>
<?php
}
?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <div id="kitchen" style="display: none;"><center>
      <br /><br /><img src="/gfx/throbber.gif" width="16" height="16" /><br /><br /><br />
     </center></div>
     <div id="project_destination"></div>
<?php
if($house_tutorial_quest === false)
  include 'commons/tutorial/myhouse.php';

if($its_your_birthday)
{
  echo '<div style="background:url(\'gfx/streamers_yellow.png\'); height:48px; font-size:48px;"><center><img src="/gfx/happy_birthday.png" width="450" height="48" /></center></div>';
  echo '<script type="text/javascript" src="//' . $SETTINGS['static_domain'] . '/js/confetti.js"></script>';
}

echo '<h4>', $sayroom , ' Room, ', $user['display'], '\'s House <i>(<span id="housebulk">', render_house_bulk($house) , '</span><a href="/help/house_space.php" class="help">?</a> <a href="housesummary.php"><img src="/gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>', $firework_link, ')</i></h4>';

if(count($userpets) > 0)
{
  if($house_hours > 0)
  {
    if($user['no_hours_fool'] == 'yes')
    {
      echo '<p class="failure">HEY!  Items have been returned to your storage from the Market Square - possibly a LOT of them!  Before you run hours, make sure to clear out items as necessary, by gameselling, throwing away, stashing in the basement, etc, them.</p>';
      echo '<p class="failure">Why this warning?  Because if a lot of items <em>were</em> returned to you, you could have HUUUUUGE storage fees unless you took care of those items!</p>';
      echo '<ul><li><a href="gimmemyhousehours.php">Understood!  I\'ll accept responsibility for any storage fees I may have!  I\'ll even take a look at my incoming right this very moment!</a></li></ul>';
    }
    else if(!$can_spend_hours)
    {
      echo '<p class="failure">';

      if(count($userpets) > $max_pets)
        echo ' <strong>You have too many pets in your house.</strong>';
      if($house['curbulk'] > min(max_house_size(), $house['maxbulk']))
        echo ' <strong>You have too many items in your house.</strong>';

      echo '<a href="/help/house_space.php" class="help">?</a> You must free up space before you can run hours.</p>';
    }
    else
    {
      if($too_many_hours)
        $extra_hours_note = '  (You cannot accumulate more than 50 hours!  Now\'s the time to use \'em!)';
?>
<p>Your <?= count($userpets) != 1 ? 'pets are' : 'pet is' ?> waiting to perform <?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?> of actions.<a href="/help/hours.php" class="help">?</a><?= $extra_hours_note ?> </p>
<?php
    }
?>
<div style="padding: 0 0 1em 1em;"><form action="/myhouse/run_hours.php" method="post" onsubmit="return disable_hours_form();" id="run_hours_form"><p>
<?php
    if($can_spend_hours)
    {
      $confirm_skip = ($user['confirm_skip'] == 'yes' ? ' onclick="return confirm(\'Really-really?\');"' : '');
?>
<input type="submit" name="action" value="<?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?>, go!" style="width:90px;" />
<input type="submit" name="action" value="8 hours, go!" style="width:90px;"<?= $house_hours < 8 ? ' disabled="disabled"' : '' ?> />
<input type="submit" name="action" value="Skip them!"<?= $confirm_skip ?> style="width:90px;" />
<?php
    }
    else
    {
?>
<input type="submit" name="action" value="<?= $house_hours ?> hour<?= $house_hours != 1 ? 's' : '' ?>, go!" disabled="disabled" style="width:90px;" />
<input type="submit" name="action" value="8 hours, go!" style="width:90px;" disabled="disabled" />
<input type="submit" name="action" value="Skip them!"<?= $confirm_skip ?> style="width:90px;" />
<?php
    }
?>
</p></form></div>
<?php
  }
  else
  {
?>
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
<?php
  }
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($say_toasty === true)
  echo '<p><i>(You received the Toasty! Badge!)</i></p>';

if($offer_kitchen_addon)
{
  echo '
    <p>Your house is expanding nicely!  Why not build a proper kitchen?</p>
    <ul><li><a href="?action=kitchen">Why not, indeed!</a></li></ul>
  ';
}

if($show_pets)
{
  $half_hour_ready = 0;

  if(count($userpets) > 0)
  {
    echo '<h5>Pets <i>(', count($userpets), ' / ', $max_pets;

    if(count($userpets) > 1)
      echo '<a href="/help/house_space.php" class="help">?</a>; <a href="/myhouse/arrange_pets.php">rearrange</a>';

    echo ')</i></h5>';
    
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

    echo '<form action="/loveaction.php" method="post" name="petactions" id="petactions">';

    if($half_hour_ready > 0 && count($userpets) > 9)
      echo '<p><input type="submit" name="submit" value="Go"></p>';

    echo $pet_listing;

    if($half_hour_ready > 0)
      echo '<p><input type="submit" name="submit" value="Go"></p>';

    echo '
      </form>
      <p id="petactionmessages" style="display:none;"></p>
    ';
  }
  else if($user['breeder'] == 'no')
  {
    $command = 'SELECT idnum FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' LIMIT 1';
    $existing_pet = $database->FetchSingle($command, 'fetching existing pet');

    if($existing_pet === false)
      echo '<ul><li><a href="request_pet.php">Get a new, random pet</a></li></ul>';
  }
  else
    echo '<p>You have no pets!  But you have a Breeder\'s License, so I\'m sure you\'ll figure something out :P</p>';
}

$num_projects = count($userprojects);

if($num_projects > 0 && $show_pets)
{
  require_once 'commons/projectlib.php';

  $rowclass = begin_row_class();
?>
<h5>Projects <i>(<?= $num_projects ?>; <a href="#" onclick="ToggleProjects(); return false;">show/hide</a>)</i></h5>
<div id="houseprojects"<?= $num_projects > 9 ? ' style="display:none;"' : '' ?>>
<table>
<thead>
 <tr>
  <th></th>
  <th></th>
  <th>Project</th>
  <th class="centered">Progress</th>
  <th>Destination</th>
  <th>Notes</th>
 </tr>
</thead>
<tbody>
<?php
  foreach($userprojects as $localid=>$project)
  {
    if($project['type'] == 'craft')
      $project_details = get_craft_byid($project['projectid']);
    else if($project['type'] == 'engineer')
      $project_details = get_invention_byid($project['projectid']);
    else if($project['type'] == 'mechanical')
      $project_details = get_mechanics_byid($project['projectid']);
    else if($project['type'] == 'chemistry')
      $project_details = get_chemistry_byid($project['projectid']);
    else if($project['type'] == 'smith')
      $project_details = get_smith_byid($project['projectid']);
    else if($project['type'] == 'tailor')
      $project_details = get_tailor_byid($project['projectid']);
    else if($project['type'] == 'leatherwork')
      $project_details = get_leatherworking_byid($project['projectid']);
    else if($project['type'] == 'paint')
      $project_details = get_painting_byid($project['projectid']);
    else if($project['type'] == 'jewel')
      $project_details = get_jewelry_byid($project['projectid']);
    else if($project['type'] == 'carpenter')
      $project_details = get_carpentry_byid($project['projectid']);
    else if($project['type'] == 'sculpture')
      $project_details = get_sculpture_byid($project['projectid']);
    else if($project['type'] == 'binding')
      $project_details = get_binding_byid($project['projectid']);
    else if($project['type'] == 'gardening')
      $project_details = get_gardening_byid($project['projectid']);
    else
      $project_details = array();

    if($project_details === false)
      echo '<tr class="' . $rowclass . '"><td colspan="4">01: broken "' . $project['type'] . '" project :(</td></tr>';
    else if($project['type'] != 'construct' && $project['type'] != '')
    {
      $item = get_item_byname($project_details['makes']);
      $percent = floor($project['progress'] * 100 / $project_details['complexity']);
      if($percent == 100 && $project['progress'] < $project_details['complexity'])
        $percent = 99;

      $material_count = array();
      $project_materials = array();

      $materials = explode(',', $project_details['ingredients']);
      foreach($materials as $material)
        $material_count[$material]++;

      arsort($material_count);

      foreach($material_count as $material=>$count)
        $project_materials[] = $material . ($count > 1 ? ' x' . $count : '');
        
      $mouse_over = 'Tip(\'<b>Materials</b><br />' . str_replace(array("'", "\""), array("\'", "\\\""), implode('<br />', $project_materials)) . '\')';
?>
 <tr class="<?= $rowclass ?>">
  <td valign="top" style="padding-top:12px;"><a href="/cancelproject.php?id=<?= $project['idnum'] ?>" style="font-weight: bold; color:red;" onclick="return confirm('Really cancel this project?  You probably won\'t get all the materials back (it\'s like recycling the finished product).');">X</a></td>
  <td valign="top" onmouseover="<?= $mouse_over ?>" align="center"><?= item_display($item) ?></td>
  <td valign="top" onmouseover="<?= $mouse_over ?>"><?= $item['itemname'] ?></td>
  <td valign="top" onmouseover="<?= $mouse_over ?>" align="center"><?= $project['priority'] == 'yes' ? '<img src="/gfx/constructioncone.png" alt="" style="vertical-align:text-top;" /> ' : '' ?><?= $percent ?>%<?= $project['priority'] == 'yes' ? ' <img src="/gfx/constructioncone.png" alt="(high priority)" title="high priority" style="vertical-align:text-top;" />' : '' ?></td>
  <td valign="top"><div id="project_dest_<?= $project['idnum'] ?>">
   <select class="project_dest" name="project_destination_<?= $project['idnum'] ?>" id="project_destination_<?= $project['idnum'] ?>" onchange="select_new_destination(<?= $project['idnum'] ?>)">
    <option value="">Common</option>
<?php
foreach($rooms as $this_room)
{
  if('home/' . $this_room == $project['destination'])
    echo '<option value="' . $this_room . '" selected="selected">' . $this_room . '</option>';
  else
    echo '<option value="' . $this_room . '">' . $this_room . '</option>';
}
?>
   </select>
  </td>
  <td valign="top" onmouseover="<?= $mouse_over ?>"><?= render_project_notes($project['notes'], $project['idnum']) ?></td>
 </tr>
<?php
    }
    else if($project['type'] == 'construct')
    {
      $improvement = get_home_improvement_byid($project['itemid']);
      if($improvement === false)
        echo '<tr class="' . $rowclass . '"><td colspan="4">03: broken project :(</td></tr>';
      else
      {
?>
 <tr class="<?= $rowclass ?>">
  <td></td>
  <td valign="top" align="center"><img src="/gfx/homeimprovement.png" alt="home improvement" height="32" /></td>
  <td valign="top"><?= $improvement['name'] ?></td>
  <td valign="top" align="center"><?= $project['priority'] == 'yes' ? '<img src="/gfx/constructioncone.png" alt="" style="vertical-align:text-top;" /> ' : '' ?><?= floor($project['progress'] * 100 / $improvement['requirement']) ?>%<?= $project['priority'] == 'yes' ? ' <img src="/gfx/constructioncone.png" alt="(high priority)" title="high priority" style="vertical-align:text-top;" />' : '' ?></td>
  <td valign="top" class="centered">&mdash;</td>
  <td valign="top"><?= render_project_notes(format_text($project['notes']), $project['idnum']) ?></td>
 </tr>
<?php
      }
    }
    else
      echo '<tr class="' . $rowclass . '"><td colspan="4">02: broken project :(</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }
?>
</tbody>
</table>
</div>
<script type="text/javascript">
function ToggleProjects()
{
  $('#houseprojects').toggle(400);
}
</script>
<?php
}

room_display($house);
?>
<h5 class="nomargin">Items <i>(<?= $house['view'] == 'icons' ? 'icon view' : 'list view' ?>, <a href="myhouse.php?viewby=<?= $house['view'] == 'icons' ? 'details' : 'icons' ?>">switch to <?= $house['view'] == 'icons' ? 'list view' : 'icon view' ?></a>)</i></h5>
<?php
echo '<p style="padding-left: 2em;"><a href="autosort.php?applyto=' . $room . '">auto-sort items</a> | <a href="autosort_edit.php">configure auto-sorter</a>';

if($user['autosorterrecording'] == 'yes')
  echo ' | <span id="recordingautosort"><a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink></span>';
else
  echo ' | <span id="recordingautosort"><a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a></span>';

echo '</p>';

if($page_note)
{
  echo '<p><i>(You have over 2000 items in this room!  When this happens, PsyPets paginates your room\'s display.)</i></p>';
  echo paginate($num_pages, $page, 'myhouse.php?page=%s');
}

house_view($user, $house, $userpets, $inventory);

if($page_note)
  echo paginate($num_pages, $page, 'myhouse.php?page=%s');

echo '<p style="padding-top:1em;">(Remember: you can select a range of items by checking one item, then holding shift while checking another.)</p>';

if(($house['view'] == 'details' && count($inventory) > 20) ||
  ($house['view'] == 'icons' && count($inventory) / 10 > 15))
{
  room_display($house);
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
