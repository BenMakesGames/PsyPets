<?php
$require_petload = 'no';
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/parklib.php';

$command = 'SELECT * FROM `monster_events` ' .
           'WHERE idnum=' . (int)$_GET['idnum'] . ' LIMIT 1';
$this_event = $database->FetchSingle($command, 'event details');

if($this_event === false)
{
  header('Location: ./park.php');
  exit();
}

$type_name = $EVENT_TYPES;

$pets = $this_event['participants'];
if(strlen($pets) > 0)
{
  $pets = str_replace('<', '', $pets);
  $pets = str_replace('>', '', $pets);
  $pets = explode(',', $pets);
}
else
  $pets = array();

$pet_info = array();

if(count($pets) > 0)
{
  foreach($pets as $idnum)
  {
		$this_pet = $database->FetchSingle('SELECT * FROM monster_pets WHERE `idnum`=' . (int)$idnum . ' LIMIT 1');
		if($this_pet === false)
      $pet_info[] = array('petname' => '[departed]');
		else
      $pet_info[] = $this_pet;
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park &gt; <?= $this_event['name'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="park.php">The Park</a> &gt; <?= $this_event['name'] ?></h4>
<?php
$host = get_user_byuser($this_event['host']);
?>
     <table>
      <tr>
       <td valign="top"><img src="gfx/events/<?= $this_event['graphic'] ?>" /></td>
       <td>
        <p>A <?= $type_name[$this_event['event']] ?><a href="<?= $EVENT_HELP_PAGES_BY_TYPE[$this_event['event']] ?>" class="help">?</a> event for <?= say_number($this_event['minparticipant']) ?> level <?= $this_event['minlevel'] . '-' . $this_event['maxlevel'] ?> pets hosted by <?= resident_link($host['display']) ?>.</p>
        <p><b>Entrance fee:</b> <?= $this_event['fee'] ?><span class="money">m</span>.</p>
<?php
if($host['idnum'] == $user['idnum'] && $this_event['finished'] == 'no')
  echo '<ul><li><a onclick="return confirm(\'If you cancel this event, you will not be refunded the cost to host it.  Any prizes you put up, however, will be returned to you.\\n\\nReally cancel this event?\');" href="cancelevent.php?idnum=' . $this_event['idnum'] . '">Cancel event</a></li></ul>';

if(strlen($this_event['descript']) > 0)
{

  if($now_month == 1 && $now_day == 18 && $now_year == 2012)
  {
    $this_event['descript'] = '{link http://' . $SETTINGS['site_domain'] . '/viewthread.php?threadid=72226 CENSORED}';
  }
?>
        <table>
         <tr><td>
          <?= format_text($this_event['descript']) ?>
         </td></tr>
        </table>
<?php
}
?>
        <h5>Prizes</h5>
        <p>
<?php
if(strlen($this_event['prizedescript']) > 0)
{
  $prizes = explode('<br />', $this_event['prizedescript']);
  unset($prizes[count($prizes) - 1]);
}
else
{
  if(strlen($this_event['prizes']) > 0)
    echo '<i>(This event is from the old days, when events did not keep very good track of their prizes.  The prize information is therefore not available.  Sorry.)</i>';

  $prizes = array();
}

if(count($prizes) > 0)
{
  // team games
  if($this_event['event'] == 'ctf' || $this_event['event'] == 'pictio')
    $team_game = true;
  else
    $team_game = false;

  if(!$team_game)
    echo '<ol>';

  $place = 0;
  foreach($prizes as $prize)
  {
    if($team_game)
    {
      if($place == 0)
      {
        echo '<p><b>Winning Team</b></p>';
        $show_prize = true;
      }
      else if($place == $this_event['minparticipant'] / 2)
      {
        echo '<p><b>Losing Team</b></p>';
        $show_prize = true;
      }
    }

    $place++;
    
    if(!$team_game)
      echo '<li>' . item_text_link($prize) . '</li>';
    else if($show_prize)
    {
      echo '<ul><li>' . item_text_link($prize) . ' for each member</li></ul>';
      $show_prize = false;
    }
  } // for each item

  if(!$team_game)
    echo '</ol>';
}
else
  echo 'This event has no prizes.';
?>
        </p>
        <h5>Participants<?= $this_event["finished"] == 'no' ? ' (' . ($this_event["minparticipant"] - count($pet_info)) . " more needed)" : "" ?></h5>
<?php
if(count($pet_info) > 0)
{
  $bgcolor = begin_row_class();
  
  echo '
    <table>
    <tr>
  ';

  $i = 0;

  foreach($pet_info as $pet)
  {
    if($pet['petname'] == '[departed]')
      echo '<td class="' . $bgcolor . '"></td><td class="dim ' . $bgcolor . '">[departed]</td>';
    else
      echo '<td class="' . $bgcolor . '">' . pet_graphic($pet) . '</td><td class="' . $bgcolor . '"><a href="/petprofile.php?petid=' . $pet['idnum'] . '">' . $pet['petname'] . '</a></td>';

    $bgcolor = alt_row_class($bgcolor);
    
    $i++;
    
    if($i % 3 == 0)
    {
      echo '</tr><tr>';
    }
  }
  echo '
    </tr>
    </table>
  ';
}
else
  echo '         <p><i>(No pets have yet signed up for this event.)</i></p>';

if($this_event['finished'] == 'yes')
{
?>
        <h5>Event Report</h5>
        <p><?= format_text($this_event['prereport']) ?></p>
        <p><?= format_text($this_event['report']) ?></p>
        <p><?= format_text($this_event['postreport']) ?></p>
<?php
}
?>
       </td>
      </tr>
     </table>
<?php
if($admin['manageevents'] == 'yes' && $this_event['finished'] == 'no')
{
?>
   <form action="forceevent.php" method="post">
   <input type="hidden" name="idnum" value="<?= $this_event['idnum'] ?>" />
   <input type="submit" value="Force Event" />
   </form>
<?php
}

if($admin['massgift'] == 'yes' && $this_event['finished'] == 'yes')
{
?>
 <form action="refundusers.php" method="post">
<?php
  $refund = array();

  foreach($pet_info as $pet)
		$refund[$pet['user']] += $this_event['fee'];

  foreach($refund as $this_user=>$amount)
    echo '<input type="hidden" name="' . $this_user . '" value="' . $amount . '" />';
?>
 <p><input type="submit" value="Refund Participants" class="bigbutton" onclick="return confirm('Reeaaaalllly?');" /><p>
 </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
</html>
