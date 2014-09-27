<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/parklib.php';
require_once 'commons/economylib.php';
require_once 'commons/houselib.php';

require_once 'commons/eventlib.php';

if($user['show_park'] != 'yes')
{
  header('Location: /n404/');
  exit();
}

if($user['license'] != 'yes')
{
  header('Location: /park.php');
  exit();
}

$command = 'SELECT idnum FROM monster_events WHERE host=' . quote_smart($user['user']) . " AND finished='no' LIMIT 3";
$existing_events = $database->FetchMultiple($command, 'fetching existing event');

if(count($existing_events) >= 3)
{
  header('Location: /park.php?msg=162');
  exit();
}

$my_house = get_house_byuser($user['idnum']);

$my_addons = take_apart(',', $my_house['addons']);

$can_host_swimming_events = in_array('Indoor Swimming Pool', $my_addons);

$event_cost = 5; //value_with_inflation(10);

$errored = false;

if($_POST['submit'] == 'Next >')
{
  $_POST['name'] = trim($_POST['name']);

  $graphic_index = (int)$_POST['gfx_index'];

  if(!array_key_exists($graphic_index, $EVENT_GRAPHICS))
    $graphic_index = array_rand($EVENT_GRAPHICS);

  if(strlen($_POST['name']) < 4 || strlen($_POST['name']) > 32)
  {
    $name_error = 'The event name must be between 4 and 32 characters.';
    $errored = true;
  }

  if((int)$_POST['minlevel'] < 1)
  {
    $level_error = 'Level 1 is the lowest level.';
    $errored = true;
  }
  else if((int)$_POST['maxlevel'] < (int)$_POST['minlevel'])
  {
    $level_error = 'The maximum level must be greater than the minimum level.';
    $errored = true;
  }
  else if((int)$_POST['maxlevel'] > max_event_level((int)$_POST['minlevel']))
  {
    $level_error = 'The maximum level is too high.';
    $errored = true;
  }

  $event_size = (int)$_POST['size'];

  $the_descript = trim(htmlentities($_POST['descript']));
  $the_prereport = trim(htmlentities($_POST['prereport']));
  $the_postreport = trim(htmlentities($_POST['postreport']));

  $cost_per_pet = $event_cost;

  if($_POST['type'] == 'race')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'A race must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'fashion')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'A fashion show must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'swim' && $can_host_swimming_events)
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'A swimming race must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'jump')
  {
    if($event_size < 4 || $event_size > 16)
    {
      $size_error = 'A Long Jump must have between 4 and 16 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'fishing')
  {
    if($event_size < 4 || $event_size > 16)
    {
      $size_error = 'A Fishing competition must have between 4 and 16 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'archery')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'An archery contest must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'ctf')
  {
    if($event_size < 6 || $event_size > 16)
    {
      $size_error = 'A Capture the Flag game must have between 6 and 16 participants. ';
      $errored = true;
    }
    if($event_size % 2 != 0)
    {
      $size_error .= 'Capture the Flag games must have an even number of participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'hunt')
  {
    if($event_size < 4 || $event_size > 20)
    {
      $size_error = 'A scavenger hunt must have between 4 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'strategy')
  {
    if($event_size < 6 || $event_size > 20)
    {
      $size_error = 'A strategy competition must have between 6 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'brawl')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'A brawl competition must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'roborena')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'A Roborena competition must have between 8 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'ddr')
  {
    if($event_size < 6 || $event_size > 20)
    {
      $size_error = 'A Dance Mania competition must have between 6 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'mining')
  {
    if($event_size < 6 || $event_size > 20)
    {
      $size_error = 'A Digcraft Build competition must have between 6 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'crafts')
  {
    if($event_size < 6 || $event_size > 20)
    {
      $size_error = 'An Arts & Crafts competition must have between 6 and 20 participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'picturades')
  {
    if($event_size < 8 || $event_size > 16)
    {
      $size_error = 'Picturades games must have between 8 and 16 participants. ';
      $errored = true;
    }
    if($event_size % 2 != 0)
    {
      $size_error .= 'Picturades games must have an even number of participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'tow')
  {
    if($event_size < 8 || $event_size > 20)
    {
      $size_error = 'Tug of War competitions must have between 8 and 20 participants. ';
      $errored = true;
    }
    if($event_size % 2 != 0)
    {
      $size_error .= 'Tug of War competitions must have an even number of participants. ';
      $errored = true;
    }
  }
  else if($_POST['type'] == 'cookoff')
  {
    if($event_size < 5 || $event_size > 20)
    {
      $size_error = 'Cook-offs must have between 5 and 20 participants. ';
      $errored = true;
    }
  } 
  else
  {
    $type_error = 'You must choose an event type.';
    $errored = true;
  }

  if(strlen($_POST['fee']) == 0)
    $_POST['fee'] = 0;
  else if((int)$_POST['fee'] < 0)
  {
    $fee_error = 'Your fee must be a positive number, or 0.';
    $errored = true;
  }

  if($event_size * $cost_per_pet > $user['money'] && strlen($size_error) == 0)
  {
    $size_error = 'You cannot afford to rent the park for an event of this size (the fee would be ' . ($event_size * $cost_per_pet) . '<span class="money">m</span>)';
    $errored = true;
  }

  if($errored == false)
  {
    $command = 'UPDATE monster_users ' .
               'SET event_graphic=' . quote_smart($EVENT_GRAPHICS[$graphic_index]) . ', ' .
                   'event_name=' . quote_smart($_POST['name']) . ', ' .
                   'event_type=' . quote_smart($_POST['type']) . ', ' .
                   'event_minlevel=' . (int)$_POST['minlevel'] . ', ' .
                   'event_maxlevel=' . (int)$_POST['maxlevel'] . ', ' .
                   'event_size=' . $event_size . ', ' .
                   'event_fee=' . (int)$_POST['fee'] . ', ' .
                   'event_descript=' . quote_smart($the_descript) . ', ' .
                   'event_prereport=' . quote_smart($the_prereport) . ', ' .
                   'event_postreport=' . quote_smart($the_postreport) . ', ' .
                   'event_step=1 ' .
               'WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'creating event');

    header('Location: ./hostevent2.php');
    exit();
  }
  else
  {
    $_POST['name'] = stripslashes($_POST['name']);
  }
}
else if($_POST['submit'] == 'Cancel')
{
  $command = 'UPDATE monster_users ' .
             'SET event_step=0 ' .
             'WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'canceling event-creator');

  header('Location: ./park.php');
  exit();
}
else if($user['event_step'] > 0)
{
  $_POST['picture'] = $user['event_graphic'];
  $_POST['name'] = $user['event_name'];
  $_POST['type'] = $user['event_type'];
  $_POST['minlevel'] = $user['event_minlevel'];
  $_POST['maxlevel'] = $user['event_maxlevel'];
  $_POST['event_size'] = $user['event_size'];
  $_POST['fee'] = $user['event_fee'];

  $_POST['descript']   = $user['event_descript'];
  $_POST['prereport']  = $user['event_prereport'];
  $_POST['postreport'] = $user['event_postreport'];

  foreach($EVENT_GRAPHICS as $value=>$graphic)
  {
    if($graphic == $_POST['picture'])
    {
      $graphic_index = $value;
      break;
    }
  }
}
else
  $graphic_index = array_rand($EVENT_GRAPHICS);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park &gt; Host Event (step 1 of 2)</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function update_max_level()
   {
     var min = parseInt($('#minlevel').val());
     var max = Math.max(min * 2, min + 10);

     if(isNaN(max))
       $('#maxlevel').val('');
     else
       $('#maxlevel').val(max);
   }
  
   $(function() {
     $('#minlevel').change(update_max_level);
     $('#minlevel').keyup(update_max_level);
   });

   function random_type()
   {
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/park.php">The Park</a> &gt; Host Event (step 1 of 2)</h4>
<?php
$command = 'SELECT idnum,name FROM `monster_events` ' .
           'WHERE host=' . quote_smart($user['user']) . ' AND finished=\'no\' LIMIT 1';
$this_event = $database->FetchSingle($command, 'fetching existing event');

echo '
  <ul class="tabbed">
   <li><a href="/park.php">Browse Events</a></li>
   <li class="activetab"><a href="/hostevent1.php">Host a new event</a></li>
   <li><a href="/park_exchange.php">Exchanges</a></li>
  </ul>
';
?>
     <form method="post" name="hostevent">
     <h5>Basic Information</h5>
     <table class="verticaltable">
      <tr>
       <th>Name:</th>
       <td><input name="name" maxlength="32" size="32" value="<?= $_POST['name'] ?>" /></td>
<?php
if($name_error)
  echo '<td><p class="failure">' . $name_error . '</p></td>';
else
  echo '<td></td>';
?>
      </tr>
      <tr>
       <th valign="top">Icon:</td>
       <td colspan="2">
        <table>
         <tr>
<?php
foreach($EVENT_GRAPHICS as $i=>$graphic)
{
  if($i > 0 && $i % 24 == 0)
    echo '</tr><tr>';
  echo '<td class="centered"><img src="//' . $SETTINGS['static_domain'] . '/gfx/events/' . $graphic . '" width="24" height="24" alt="" /><br /><input type="radio" name="gfx_index" value="' . $i . '"' . ($graphic_index == $i ? ' checked' : '') . ' /></td>';
}
?>
         </tr>
        </table>
       </td>
      </tr>
      <tr>
       <th>Type:</th>
       <td>
        <select name="type" id="event_type">
<?php
if(!$can_host_swimming_events)
  unset($EVENT_TYPES['swim']);

foreach($EVENT_TYPES as $key=>$desc)
  echo ' <option value="' . $key . '"' . ($_POST['type'] == $key ? ' selected' : '') . '>' . $desc . '</option>';
?>
        <a href="#" onclick="random_type(); return false;"><img /></a>
       </td>
<?php
if($type_error)
  echo '<td class="failure">' . $type_error . '</td>';
else
  echo '<td></td>';
?>
      </tr>
      <tr>
       <th>Level&nbsp;range:</th>
       <td><input name="minlevel" id="minlevel" maxlength="3" size="2" value="<?= $_POST['minlevel'] ?>" /> - <input name="maxlevel" id="maxlevel" maxlength="3" size="2" value="<?= $_POST['maxlevel'] ?>" /></td>
<?php
if($level_error)
  echo '<td class="failure">' . $level_error . '</td>';
else
  echo '<td>The range of pet levels allowed.</td>';
?>
      </tr>
      <tr>
       <th>Size:</th>
       <td><input name="size" maxlength="2" size="2" value="<?= $_POST['event_size'] ?>" /></td>
<?php
if($size_error)
  echo '<td class="failure">' . $size_error . '</td>';
else
  echo '<td>The number of participants.  The PsyPets Park Service will charge you ' . $event_cost . '<span class="money">m</span> per pet.</td>';
?>
      </tr>
      <tr>
       <th>Fee:</td>
       <td><input name="fee" maxlength="4" size="4" value="<?= $_POST['fee'] ?>" /><span class="money">m</span></td>
<?php
if($fee_error)
  echo '<td class="failure">' . $fee_error . '</td>';
else
  echo '<td>The fee you will charge residents for each pet participating.</td>';
?>
      </tr>
     </table>
     <p><input type="submit" name="submit" value="Next &gt;" /> <input type="submit" name="submit" value="Cancel" /></p>
     <h5>Event Description (optional)</h5>
     <p>Description of event.</p>
     <p><textarea name="descript" cols="40" rows="10" style="width:400px;"><?= $the_descript ?></textarea></p>
     <h5>Pre-report Description</h5>
     <p>When the event finishes, this text is shown <em>before</em> the event report.</p>
     <p><textarea name="prereport" cols="40" rows="4" style="width:400px;"><?= $the_prereport ?></textarea></p>
     <h5>Post-report Description</h5>
     <p>When the event finishes, this text is shown <em>after</em> the event report.</p>
     <p><textarea name="postreport" cols="40" rows="4" style="width:400px;"><?= $the_postreport ?></textarea></p>
     <p><input type="submit" name="submit" value="Next &gt;" /> <input type="submit" name="submit" value="Cancel" /></p>
     </form>
<?php echo formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
